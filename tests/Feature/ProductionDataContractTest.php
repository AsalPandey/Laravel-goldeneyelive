<?php

namespace Tests\Feature;

use App\Models\AnalyticsEvent;
use App\Models\Contact;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\JoinNowQuery;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionDataContractTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Mail::fake();
        $this->seed(RoleSeeder::class);
        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_expanded_content_columns_are_text_on_the_committed_schema(): void
    {
        $this->assertSame('text', Schema::getColumnType('contacts', 'landing_page'));
        $this->assertSame('text', Schema::getColumnType('join_now_queries', 'landing_page'));
        $this->assertSame('text', Schema::getColumnType('join_now_queries', 'source_page'));
        $this->assertSame('text', Schema::getColumnType('f_a_q_s', 'question'));
        $this->assertSame('text', Schema::getColumnType('notices', 'link'));
        $this->assertSame('text', Schema::getColumnType('service_pillars', 'cta_url'));
    }

    public function test_contact_length_boundary_persists_and_one_character_above_is_rejected(): void
    {
        $acceptedReferrer = str_repeat('r', 500);

        $this->post(route('contact-submit'), $this->contactPayload([
            'email' => 'boundary@example.com',
            'landing_page' => $acceptedReferrer,
        ]))->assertRedirect();

        $this->assertDatabaseHas(Contact::class, [
            'email' => 'boundary@example.com',
            'landing_page' => $acceptedReferrer,
        ]);

        $this->post(route('contact-submit'), $this->contactPayload([
            'email' => 'over-boundary@example.com',
            'landing_page' => str_repeat('r', 501),
        ]))->assertSessionHasErrors('landing_page');

        $this->assertDatabaseMissing(Contact::class, ['email' => 'over-boundary@example.com']);
    }

    public function test_join_now_boundaries_persist_and_overlong_values_are_rejected(): void
    {
        $this->post(route('join-now-submit'), [
            ...$this->joinPayload(),
            'email' => 'join-boundary@example.com',
            'address' => str_repeat('a', 255),
            'landing_page' => str_repeat('l', 500),
            'source_page' => str_repeat('s', 500),
        ])->assertRedirect();

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'join-boundary@example.com',
            'address' => str_repeat('a', 255),
            'landing_page' => str_repeat('l', 500),
            'source_page' => str_repeat('s', 500),
        ]);

        $this->post(route('join-now-submit'), [
            ...$this->joinPayload(),
            'email' => 'join-over-boundary@example.com',
            'address' => str_repeat('a', 256),
            'source_page' => str_repeat('s', 501),
        ])->assertSessionHasErrors(['address', 'source_page']);

        $this->assertDatabaseMissing(JoinNowQuery::class, ['email' => 'join-over-boundary@example.com']);
    }

    public function test_unusually_long_referrer_fallback_is_bounded_before_storage(): void
    {
        $this->withHeader('Referer', 'https://example.com/'.str_repeat('r', 70000))
            ->post(route('contact-submit'), $this->contactPayload([
                'email' => 'long-referrer@example.com',
            ]))
            ->assertRedirect();

        $contact = Contact::query()->where('email', 'long-referrer@example.com')->firstOrFail();

        $this->assertSame(500, mb_strlen((string) $contact->landing_page));
    }

    public function test_nepal_phone_formats_are_normalized_before_storage(): void
    {
        $contactFormats = [
            '+977 98-2345-6789' => '9823456789',
            '061-572599' => '061572599',
        ];

        foreach ($contactFormats as $input => $canonical) {
            $this->post(route('contact-submit'), $this->contactPayload([
                'email' => $canonical.'@example.com',
                'phone' => $input,
            ]))->assertRedirect();

            $this->assertDatabaseHas(Contact::class, [
                'email' => $canonical.'@example.com',
                'phone' => $canonical,
            ]);
        }

        $this->post(route('join-now-submit'), [
            ...$this->joinPayload(),
            'email' => 'formatted-join@example.com',
            'phone' => '+977 (97) 2345-6789',
        ])->assertRedirect();

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'formatted-join@example.com',
            'phone' => '9723456789',
        ]);
    }

    public function test_arbitrary_and_implausible_phone_values_remain_rejected(): void
    {
        foreach (['letters-9823456789', '+1 202 555 0100', '9800000000'] as $index => $phone) {
            $this->post(route('join-now-submit'), [
                ...$this->joinPayload(),
                'email' => "invalid-phone-{$index}@example.com",
                'phone' => $phone,
            ])->assertSessionHasErrors('phone');
        }
    }

    public function test_final_visible_course_selection_is_authoritative_for_storage_and_analytics(): void
    {
        [$courseA, $courseB] = $this->coursesForSelection();

        $this->post(route('join-now-submit'), [
            ...$this->joinPayload(),
            'email' => 'unchanged-course@example.com',
            'course' => $courseA->slug,
            'selected_course' => $courseA->slug,
            'preferred_course' => $courseA->slug,
        ])->assertRedirect();

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'unchanged-course@example.com',
            'course_id' => $courseA->id,
            'course_slug' => $courseA->slug,
            'selected_course' => $courseA->slug,
            'course' => $courseA->name,
        ]);

        $this->post(route('join-now-submit'), [
            ...$this->joinPayload(),
            'email' => 'changed-course@example.com',
            'course' => $courseB->slug,
            'selected_course' => $courseA->slug,
            'preferred_course' => 'tampered-hidden-course',
        ])->assertRedirect();

        $this->assertDatabaseHas(JoinNowQuery::class, [
            'email' => 'changed-course@example.com',
            'course_id' => $courseB->id,
            'course_slug' => $courseB->slug,
            'selected_course' => $courseB->slug,
            'course' => $courseB->name,
        ]);
        $this->assertDatabaseHas(AnalyticsEvent::class, [
            'event_name' => 'course_help_submit',
            'selected_course' => $courseB->slug,
        ]);
    }

    public function test_cms_boundaries_and_notice_seo_values_persist(): void
    {
        $question = str_repeat('q', 500);

        $this->actingAs($this->admin)->post(route('admin.faq.store'), [
            'question' => $question,
            'answer' => str_repeat('a', 10000),
            'status' => 'active',
            'order_priority' => 9999,
        ])->assertRedirect(route('admin.faq.index'));

        $this->assertDatabaseHas(FAQ::class, ['question' => $question]);

        $this->actingAs($this->admin)->post(route('admin.faq.store'), [
            'question' => str_repeat('q', 501),
            'answer' => str_repeat('a', 10001),
            'status' => 'active',
        ])->assertSessionHasErrors(['question', 'answer']);

        $noticeUrl = $this->urlOfLength(500);
        $this->actingAs($this->admin)->post(route('admin.notices.store'), [
            'title' => 'Durable SEO notice',
            'link' => $noticeUrl,
            'button_text' => 'Ask for Course Help',
            'status' => 'active',
            'display_type' => 'popup',
            'meta_title' => 'Durable notice metadata',
            'meta_description' => 'This must not be silently discarded.',
            'meta_keywords' => 'notice, course',
            'aeo_summary' => 'Durable answer summary.',
            'schema_markup' => '{"@type":"EducationalOrganization"}',
        ])->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas(Notice::class, [
            'title' => 'Durable SEO notice',
            'link' => $noticeUrl,
            'meta_title' => 'Durable notice metadata',
            'meta_description' => 'This must not be silently discarded.',
            'meta_keywords' => 'notice, course',
            'aeo_summary' => 'Durable answer summary.',
            'schema_markup' => '{"@type":"EducationalOrganization"}',
        ]);

        $pillarUrl = $this->urlOfLength(500);
        $this->actingAs($this->admin)->post(route('admin.service-pillars.store'), [
            'title' => 'Durable CTA pillar',
            'cta_label' => 'Ask for Course Help',
            'cta_url' => $pillarUrl,
            'status' => 'active',
        ])->assertRedirect(route('admin.service-pillars.index'));

        $this->assertDatabaseHas(ServicePillar::class, ['cta_url' => $pillarUrl]);
    }

    public function test_short_course_fields_reject_values_above_database_capacity(): void
    {
        $category = CourseCategory::factory()->create();

        $payload = [
            'name' => 'Boundary Course',
            'slug' => 'boundary-course',
            'category_id' => $category->id,
            'price' => str_repeat('p', 255),
            'duration' => str_repeat('d', 255),
            'instructor' => str_repeat('i', 255),
            'capacity' => str_repeat('c', 255),
            'description' => 'Description',
            'course_outline' => 'Outline',
            'status' => 'active',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), $payload)
            ->assertRedirect(route('admin.courses.index'));

        $this->assertDatabaseHas(Course::class, ['slug' => 'boundary-course']);

        $this->actingAs($this->admin)
            ->post(route('admin.courses.store'), [
                ...$payload,
                'slug' => 'over-boundary-course',
                'price' => str_repeat('p', 256),
                'duration' => str_repeat('d', 256),
                'instructor' => str_repeat('i', 256),
                'capacity' => str_repeat('c', 256),
            ])
            ->assertSessionHasErrors(['price', 'duration', 'instructor', 'capacity']);
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function contactPayload(array $overrides = []): array
    {
        return [
            'name' => 'Asha Sharma',
            'phone' => '9823456789',
            'email' => 'asha@example.com',
            'subject' => 'Course question',
            'message' => 'Please share course details.',
            ...$overrides,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function joinPayload(): array
    {
        return [
            'firstName' => 'Asha',
            'lastName' => 'Sharma',
            'email' => 'asha@example.com',
            'phone' => '9823456789',
            'course' => 'undecided',
            'help_topic' => 'Choosing a course',
        ];
    }

    /**
     * @return array{Course, Course}
     */
    private function coursesForSelection(): array
    {
        return [
            Course::factory()->create(['name' => 'Course A', 'slug' => 'course-a', 'status' => 'active']),
            Course::factory()->create(['name' => 'Course B', 'slug' => 'course-b', 'status' => 'active']),
        ];
    }

    private function urlOfLength(int $length): string
    {
        $prefix = 'https://example.com/?q=';

        return $prefix.str_repeat('x', $length - strlen($prefix));
    }
}

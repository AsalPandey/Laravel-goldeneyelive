<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use Illuminate\Contracts\Queue\Factory as QueueFactory;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class InquiryReliabilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        cache()->forget('setting_recaptcha_site_key');
        cache()->forget('setting_recaptcha_secret_key');
    }

    public function test_contact_submissions_with_the_same_email_and_subject_preserve_changed_messages(): void
    {
        Mail::fake();

        $first = $this->contactPayload([
            'message' => 'Please send the IELTS morning batch details.',
        ]);
        $second = $this->contactPayload([
            'message' => 'Please send the IELTS evening batch and fee details.',
        ]);

        $this->post(route('contact-submit'), $first)->assertRedirect();
        $this->post(route('contact-submit'), $second)->assertRedirect();

        $this->assertSame(2, Contact::count());
        $this->assertDatabaseHas(Contact::class, ['message' => $first['message']]);
        $this->assertDatabaseHas(Contact::class, ['message' => $second['message']]);
    }

    public function test_course_help_submissions_with_the_same_phone_and_course_preserve_changed_details(): void
    {
        Mail::fake();

        $first = $this->joinPayload([
            'goal' => 'I need a morning class.',
            'queries' => 'Please share the batch start date.',
        ]);
        $second = $this->joinPayload([
            'goal' => 'I now need an evening class.',
            'queries' => 'Please also share fees and payment options.',
        ]);

        $this->post(route('join-now-submit'), $first)->assertRedirect();
        $this->post(route('join-now-submit'), $second)->assertRedirect();

        $this->assertSame(2, JoinNowQuery::count());
        $this->assertDatabaseHas(JoinNowQuery::class, [
            'phone' => $first['phone'],
            'queries' => "I need a morning class.\n\nPlease share the batch start date.",
        ]);
        $this->assertDatabaseHas(JoinNowQuery::class, [
            'phone' => $second['phone'],
            'queries' => "I now need an evening class.\n\nPlease also share fees and payment options.",
        ]);
    }

    public function test_family_members_sharing_a_phone_number_are_preserved_as_separate_inquiries(): void
    {
        Mail::fake();

        $this->post(route('join-now-submit'), $this->joinPayload([
            'full_name' => 'Asha Sharma',
            'queries' => 'Inquiry for Asha.',
        ]))->assertRedirect();

        $this->post(route('join-now-submit'), $this->joinPayload([
            'full_name' => 'Bikash Sharma',
            'queries' => 'Inquiry for Bikash.',
        ]))->assertRedirect();

        $this->assertSame(2, JoinNowQuery::where('phone', '9823456789')->count());
        $this->assertDatabaseHas(JoinNowQuery::class, ['firstName' => 'Asha']);
        $this->assertDatabaseHas(JoinNowQuery::class, ['firstName' => 'Bikash']);
    }

    public function test_an_exact_repeated_submission_is_preserved_instead_of_silently_discarded(): void
    {
        Mail::fake();

        $payload = $this->contactPayload();

        $this->post(route('contact-submit'), $payload)->assertRedirect();
        $this->post(route('contact-submit'), $payload)->assertRedirect();

        $this->assertSame(2, Contact::where('email', $payload['email'])->count());
    }

    public function test_contact_inquiry_remains_stored_when_mail_setup_fails(): void
    {
        Log::spy();
        Mail::shouldReceive('to')
            ->once()
            ->andThrow(new RuntimeException('mail transport unavailable'));

        $response = $this->from(route('contact'))->post(route('contact-submit'), $this->contactPayload());

        $response->assertRedirect(route('contact'));
        $contact = Contact::sole();

        Log::shouldHaveReceived('warning')
            ->with('Inquiry notification could not be queued.', [
                'inquiry_type' => 'contact',
                'inquiry_id' => $contact->id,
                'exception' => RuntimeException::class,
            ])
            ->once();
    }

    public function test_course_inquiry_remains_stored_when_the_queue_is_unavailable(): void
    {
        Log::spy();
        $queue = Mockery::mock(QueueFactory::class);
        $queue->shouldReceive('connection')
            ->once()
            ->andThrow(new RuntimeException('queue unavailable'));
        $this->app->instance('queue', $queue);
        $this->app->instance(QueueFactory::class, $queue);

        $response = $this->from(route('join-now'))->post(route('join-now-submit'), $this->joinPayload());

        $response
            ->assertRedirect(route('join-now'))
            ->assertSessionHas('success');
        $inquiry = JoinNowQuery::sole();

        Log::shouldHaveReceived('warning')
            ->with('Inquiry notification could not be queued.', [
                'inquiry_type' => 'course_help',
                'inquiry_id' => $inquiry->id,
                'exception' => RuntimeException::class,
            ])
            ->once();
    }

    public function test_invalid_newsletter_email_uses_the_visible_newsletter_error_bag(): void
    {
        $response = $this->from(route('home'))->post(route('newsletter'), [
            'email' => 'not-an-email',
        ]);

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHasErrors(['email'], null, 'newsletter');

        $this->followRedirects($response)
            ->assertOk()
            ->assertSee('newsletterEmailError', false)
            ->assertSee('valid email address');

        $this->assertSame(0, NewsLetter::count());
    }

    public function test_existing_newsletter_email_is_normalized_and_receives_a_friendly_response(): void
    {
        NewsLetter::create(['email' => 'subscriber@example.com']);

        $response = $this->from(route('home'))->post(route('newsletter'), [
            'email' => ' Subscriber@Example.COM ',
        ]);

        $response
            ->assertRedirect(route('home'))
            ->assertSessionHas('alert.config');

        $alertConfiguration = (string) $response->getSession()->get('alert.config');
        $this->assertStringContainsString('Already Subscribed', $alertConfiguration);
        $this->assertStringContainsString('This email is already part of our newsletter list.', $alertConfiguration);

        $this->assertSame(1, NewsLetter::count());
        $this->assertDatabaseHas(NewsLetter::class, ['email' => 'subscriber@example.com']);
    }

    public function test_newsletter_unique_constraint_closes_the_concurrent_insert_race(): void
    {
        DB::table('news_letters')->insert([
            'email' => 'race@example.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            DB::table('news_letters')->insert([
                'email' => 'race@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->fail('The duplicate newsletter insert should have violated the database unique constraint.');
        } catch (QueryException) {
            $this->assertSame(1, NewsLetter::where('email', 'race@example.com')->count());
        }
    }

    public function test_phase_two_migration_rolls_back_and_reapplies(): void
    {
        $this->assertTrue(Schema::hasColumn('contacts', 'deleted_at'));
        $this->assertTrue(Schema::hasColumn('join_now_queries', 'deleted_at'));
        $this->assertContains(
            'news_letters_email_unique',
            collect(Schema::getIndexes('news_letters'))->pluck('name')->all()
        );

        $migration = require database_path('migrations/2026_07_28_193920_add_inquiry_reliability_to_submissions.php');
        $migration->down();

        $this->assertFalse(Schema::hasColumn('contacts', 'deleted_at'));
        $this->assertFalse(Schema::hasColumn('join_now_queries', 'deleted_at'));
        $this->assertNotContains(
            'news_letters_email_unique',
            collect(Schema::getIndexes('news_letters'))->pluck('name')->all()
        );

        $migration->up();

        $this->assertTrue(Schema::hasColumn('contacts', 'deleted_at'));
        $this->assertTrue(Schema::hasColumn('join_now_queries', 'deleted_at'));
        $this->assertContains(
            'news_letters_email_unique',
            collect(Schema::getIndexes('news_letters'))->pluck('name')->all()
        );
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
            'subject' => 'IELTS class inquiry',
            'message' => 'Please share IELTS class details.',
            'lead_source' => 'website',
            'landing_page' => '/contact',
            'cta_id' => 'contact-form',
            ...$overrides,
        ];
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function joinPayload(array $overrides = []): array
    {
        return [
            'full_name' => 'Asha Sharma',
            'phone' => '9823456789',
            'help_topic' => 'Choosing a course',
            'course' => 'undecided',
            'queries' => 'Please share suitable course details.',
            'source_page' => 'home',
            'source_section' => 'course-help',
            'lead_source' => 'website',
            'landing_page' => '/join-now',
            'cta_id' => 'course-help-form',
            ...$overrides,
        ];
    }
}

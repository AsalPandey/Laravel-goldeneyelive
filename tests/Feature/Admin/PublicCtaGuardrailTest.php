<?php

namespace Tests\Feature\Admin;

use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCtaGuardrailTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');
    }

    public function test_branding_cta_labels_are_normalized_before_save(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'hero_cta_1_text' => 'Ask for Course Guidance',
                'hero_cta_2_text' => 'Explore Programs',
                'hero_cta_text' => 'Contact Us',
                'popup_button_text' => '',
                'sticky_cta_text' => 'Book Free Counseling',
                'blog_cta_btn' => 'Ask What Fits Me',
                'faq_btn_text' => 'Contact Us',
                'whatsapp_cta_text' => 'Message us on WhatsApp',
                'whatsapp_button_text' => 'WhatsApp',
                'whatsapp_number' => '98 0000 0000',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_cta_1_text',
            'value' => 'Ask for Course Help',
        ]);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'hero_cta_2_text',
            'value' => 'View Course Details',
        ]);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_cta_text',
            'value' => 'Message on WhatsApp',
        ]);
        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'faq_btn_text',
            'value' => 'Ask for Course Help',
        ]);
        $this->assertDatabaseMissing(SiteSetting::class, [
            'value' => 'Ask for Course Guidance',
        ]);
    }

    public function test_whatsapp_number_is_rejected_when_invalid_and_canonicalized_when_valid(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'whatsapp_number' => 'random text',
            ])
            ->assertSessionHasErrors('whatsapp_number');

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'whatsapp_number' => '+977 98-0000-0000',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_number',
            'value' => '9779800000000',
        ]);
    }

    public function test_whatsapp_prefill_message_strips_raw_script_tags(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'whatsapp_number' => '9800000000',
                'whatsapp_prefill_message' => '<script>alert("x")</script>Hi GoldenEye Academy',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas(SiteSetting::class, [
            'key' => 'whatsapp_prefill_message',
            'value' => 'alert("x")Hi Golden Eye Academy',
        ]);
        $this->assertDatabaseMissing(SiteSetting::class, [
            'key' => 'whatsapp_prefill_message',
            'value' => '<script>alert("x")</script>Hi GoldenEye Academy',
        ]);
    }

    public function test_notice_cta_labels_are_normalized_from_their_public_links(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Course Notice',
                'subtitle' => 'Browse available programs.',
                'link' => '/courses-all',
                'button_text' => 'Explore Programs',
                'status' => 'active',
                'display_type' => 'bar',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Help Notice',
                'subtitle' => 'Ask before enrollment.',
                'link' => '/join-now?course=undecided',
                'button_text' => 'Ask for Course Guidance',
                'status' => 'active',
                'display_type' => 'popup',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas(Notice::class, [
            'title' => 'Course Notice',
            'button_text' => 'View Course Details',
        ]);
        $this->assertDatabaseHas(Notice::class, [
            'title' => 'Help Notice',
            'button_text' => 'Ask for Course Help',
        ]);
    }

    public function test_notice_whatsapp_link_uses_whatsapp_cta_label(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'WhatsApp Notice',
                'link' => 'https://wa.me/9779800000000',
                'button_text' => 'Message us on WhatsApp',
                'status' => 'active',
                'display_type' => 'popup',
            ])
            ->assertRedirect(route('admin.notices.index'));

        $this->assertDatabaseHas(Notice::class, [
            'title' => 'WhatsApp Notice',
            'button_text' => 'Message on WhatsApp',
        ]);
    }

    public function test_notice_and_service_pillar_reject_invalid_public_links(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.notices.store'), [
                'title' => 'Unsafe Notice',
                'link' => 'javascript:alert(1)',
                'button_text' => 'Ask for Course Help',
                'status' => 'active',
                'display_type' => 'popup',
            ])
            ->assertSessionHasErrors('link');

        $this->actingAs($this->admin)
            ->post(route('admin.service-pillars.store'), [
                'title' => 'Unsafe Pillar',
                'cta_label' => 'Explore Programs',
                'cta_url' => 'ftp://example.com/course',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('cta_url');

        $this->actingAs($this->admin)
            ->post(route('admin.service-pillars.store'), [
                'title' => 'Protected Admin Link Pillar',
                'cta_label' => 'Ask for Course Help',
                'cta_url' => '/admin/dashboard',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('cta_url');
    }

    public function test_service_pillar_cta_label_is_normalized_from_url(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.service-pillars.store'), [
                'title' => 'Course Browsing Pillar',
                'cta_label' => 'See Courses',
                'cta_url' => '/courses-all',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.service-pillars.index'));

        $this->actingAs($this->admin)
            ->post(route('admin.service-pillars.store'), [
                'title' => 'Help Pillar',
                'cta_label' => 'Contact Us',
                'cta_url' => '/contact',
                'status' => 'active',
            ])
            ->assertRedirect(route('admin.service-pillars.index'));

        $this->assertDatabaseHas(ServicePillar::class, [
            'title' => 'Course Browsing Pillar',
            'cta_label' => 'View Course Details',
        ]);
        $this->assertDatabaseHas(ServicePillar::class, [
            'title' => 'Help Pillar',
            'cta_label' => 'Ask for Course Help',
        ]);
    }

    public function test_public_page_keeps_frozen_cta_navbar_and_popup_contract_after_admin_save(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'hero_cta_1_text' => 'Ask for Course Guidance',
                'hero_cta_2_text' => 'Explore Programs',
                'popup_status' => 'active',
                'popup_title' => 'Course Guidance Before Enrollment',
                'popup_button_text' => 'Ask for Course Guidance',
                'whatsapp_number' => '9800000000',
                'whatsapp_cta_text' => 'Message us on WhatsApp',
            ])
            ->assertRedirect();

        $response = $this->get(route('home'))->assertOk();
        $html = $response->getContent();

        $this->assertSame(1, substr_count($html, 'site-desktop-nav'));
        $this->assertSame(1, substr_count($html, 'site-mobile-nav'));
        $this->assertStringContainsString('data-cta="navbar-course-help"', $html);
        $this->assertStringContainsString('siteNoticePopup', $html);
        $this->assertStringContainsString('Ask for Course Help', $html);
        $this->assertStringContainsString('View Course Details', $html);
        $this->assertStringContainsString('Message on WhatsApp', $html);
        $this->assertStringNotContainsString('Ask for Course Guidance', $html);
        $this->assertStringNotContainsString('Message us on WhatsApp', $html);
    }
}

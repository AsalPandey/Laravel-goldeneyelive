<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Notice;
use App\Models\ServicePillar;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\CourseCategorySeeder;
use Database\Seeders\CourseSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ServicePillarSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class Phase5BrandClarificationTest extends TestCase
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

    public function test_golden_eye_academy_remains_the_primary_public_identity(): void
    {
        SiteSetting::query()->create([
            'key' => 'site_name',
            'value' => 'GoldenEye',
            'type' => 'text',
        ]);

        foreach (['home', 'about', 'courses-all', 'contact'] as $routeName) {
            $response = $this->get(route($routeName))->assertOk();

            $response
                ->assertSee('Golden Eye Academy')
                ->assertDontSee('GoldenEye Academy')
                ->assertDontSee('Brilliant Education Pokhara');
        }

        $this->get(route('login'))
            ->assertOk()
            ->assertSeeText('Golden Eye Academy')
            ->assertDontSeeText('GoldenEye');
    }

    public function test_study_abroad_page_uses_the_approved_service_boundary_and_preserves_its_route(): void
    {
        $this->get(route('study-abroad-guidance'))
            ->assertOk()
            ->assertSeeText('Focus on the skills you need to practise.')
            ->assertSeeText('The team can explain relevant preparation classes and current availability without promising a score, admission or visa result.')
            ->assertDontSeeText('German-language classes')
            ->assertDontSeeText('legal and billing operator')
            ->assertSee('href="'.route('courses-all').'"', false);
    }

    public function test_admin_can_edit_every_changed_public_statement_through_existing_cms_fields(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.branding.index'))
            ->assertOk()
            ->assertSee('name="footer_about_text"', false)
            ->assertSee('name="audience_study_abroad_support_tagline"', false)
            ->assertSee('name="audience_study_abroad_support_title"', false)
            ->assertSee('name="audience_study_abroad_why"', false)
            ->assertSee('name="audience_study_abroad_proof_tagline"', false)
            ->assertSee('name="audience_study_abroad_proof"', false);

        $values = [
            'footer_about_text' => 'CMS5 custom Golden Eye academy description.',
            'audience_study_abroad_support_tagline' => 'CMS5 custom boundary tagline',
            'audience_study_abroad_support_title' => 'CMS5 custom boundary title',
            'audience_study_abroad_why' => 'CMS5 custom Golden Eye and Brilliant explanation.',
            'audience_study_abroad_proof_tagline' => 'CMS5 custom pathway tagline',
            'audience_study_abroad_proof' => "CMS5 custom German pathway\nCMS5 custom billing disclosure",
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), $values)
            ->assertRedirect();

        foreach ($values as $key => $value) {
            $this->assertDatabaseHas(SiteSetting::class, [
                'key' => $key,
                'value' => $value,
            ]);
        }

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText($values['footer_about_text']);

        $studyAbroadPage = $this->get(route('study-abroad-guidance'))->assertOk();
        foreach ($values as $key => $value) {
            if ($key === 'footer_about_text') {
                continue;
            }

            foreach (explode("\n", $value) as $line) {
                $studyAbroadPage->assertSeeText($line);
            }
        }

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'footer_about_text' => str_repeat('a', 1001),
                'audience_study_abroad_proof' => str_repeat('b', 1501),
            ])
            ->assertSessionHasErrors([
                'footer_about_text',
                'audience_study_abroad_proof',
            ]);
    }

    public function test_brand_settings_do_not_change_protected_products_or_product_routes(): void
    {
        $this->seed([
            CourseCategorySeeder::class,
            CourseSeeder::class,
            ServicePillarSeeder::class,
        ]);

        $coursesBefore = Course::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $categoriesBefore = CourseCategory::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $servicePillarsBefore = ServicePillar::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray();
        $courseRoutesBefore = Course::query()
            ->orderBy('id')
            ->pluck('slug')
            ->map(fn (string $slug): string => route('courses-detail', $slug))
            ->all();

        $this->assertCount(13, $coursesBefore);
        $this->assertCount(5, $categoriesBefore);
        $this->assertCount(7, $servicePillarsBefore);

        $this->actingAs($this->admin)
            ->post(route('admin.branding.update'), [
                'footer_about_text' => 'A brand-only setting update.',
                'audience_study_abroad_why' => 'A consultancy-boundary setting update.',
            ])
            ->assertRedirect();

        $this->assertSame(
            $coursesBefore,
            Course::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray(),
        );
        $this->assertSame(
            $categoriesBefore,
            CourseCategory::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray(),
        );
        $this->assertSame(
            $servicePillarsBefore,
            ServicePillar::query()->orderBy('id')->get()->makeHidden(['created_at', 'updated_at'])->toArray(),
        );
        $this->assertSame(
            $courseRoutesBefore,
            Course::query()
                ->orderBy('id')
                ->pluck('slug')
                ->map(fn (string $slug): string => route('courses-detail', $slug))
                ->all(),
        );

        foreach ($courseRoutesBefore as $courseRoute) {
            $this->get($courseRoute)->assertOk();
        }
    }

    public function test_popup_notice_hero_and_contact_features_remain_available(): void
    {
        SiteSetting::query()->upsert([
            ['key' => 'popup_status', 'value' => 'active', 'type' => 'text'],
            ['key' => 'popup_title', 'value' => 'Phase 5 Campaign Popup', 'type' => 'text'],
        ], ['key'], ['value', 'type']);

        Notice::query()->create([
            'title' => 'Phase 5 Announcement Banner',
            'status' => 'active',
            'display_type' => 'bar',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('home-hero', false)
            ->assertSee('Phase 5 Campaign Popup')
            ->assertSee('Phase 5 Announcement Banner')
            ->assertSee('siteNoticeStrip', false)
            ->assertSee('data-cta="navbar-course-help"', false)
            ->assertSee('data-cta="homepage-final-whatsapp"', false);

        Notice::query()->create([
            'title' => 'Phase 5 Popup Notice',
            'status' => 'active',
            'display_type' => 'popup',
        ]);

        cache()->flush();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Phase 5 Popup Notice')
            ->assertSee('Phase 5 Announcement Banner')
            ->assertSee('siteNoticePopup', false)
            ->assertSee('siteNoticeStrip', false);

        $this->get(route('contact'))->assertOk();
        $this->get(route('join-now'))->assertOk();
    }
}

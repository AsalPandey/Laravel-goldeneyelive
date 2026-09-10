<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MediaAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $staff;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RoleSeeder::class);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('Admin');

        $this->staff = User::factory()->create();
        $this->staff->assignRole('Staff');

        $this->student = User::factory()->create();
        $this->student->assignRole('Student');
    }

    public function test_admin_and_staff_can_list_media_but_staff_cannot_open_website_settings(): void
    {
        foreach ([$this->admin, $this->staff] as $user) {
            $this->actingAs($user)
                ->getJson(route('admin.media.index'))
                ->assertOk()
                ->assertJsonStructure([
                    'images' => [
                        '*' => ['name', 'path', 'size'],
                    ],
                ]);
        }

        $this->actingAs($this->staff)
            ->get(route('admin.branding.index'))
            ->assertForbidden();
    }

    public function test_staff_can_upload_a_new_media_asset(): void
    {
        $before = File::files(public_path('site/img'));

        try {
            $this->actingAs($this->staff)
                ->post(route('admin.media.store'), [
                    'image' => $this->fakePng('phase-one-media.png'),
                ])
                ->assertRedirect();

            $after = File::files(public_path('site/img'));
            $created = collect($after)->reject(
                fn ($file): bool => collect($before)->contains(
                    fn ($original): bool => $original->getRealPath() === $file->getRealPath(),
                ),
            );

            $this->assertCount(1, $created);
        } finally {
            foreach (File::files(public_path('site/img')) as $file) {
                if (! collect($before)->contains(fn ($original): bool => $original->getRealPath() === $file->getRealPath())) {
                    File::delete($file->getRealPath());
                }
            }
        }
    }

    public function test_students_and_guests_cannot_list_or_upload_media(): void
    {
        $this->getJson(route('admin.media.index'))->assertUnauthorized();

        $this->actingAs($this->student)
            ->getJson(route('admin.media.index'))
            ->assertForbidden();

        $this->actingAs($this->student)
            ->post(route('admin.media.store'), [
                'image' => $this->fakePng('denied.png'),
            ])
            ->assertForbidden();
    }

    public function test_media_controls_are_only_rendered_for_cms_roles(): void
    {
        $this->actingAs($this->staff)
            ->get(route('admin.courses.create'))
            ->assertOk()
            ->assertSee(route('admin.media.index'))
            ->assertDontSee(route('admin.branding.index'));

        $this->actingAs($this->student)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('mediaVaultModal');
    }

    private function fakePng(string $name): UploadedFile
    {
        $content = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=',
            true,
        );

        $this->assertNotFalse($content);

        return UploadedFile::fake()->createWithContent($name, $content);
    }
}

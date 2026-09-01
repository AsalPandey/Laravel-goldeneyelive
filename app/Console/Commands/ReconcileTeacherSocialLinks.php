<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

#[Signature('goldeneye:reconcile-teacher-social-links
    {--apply : Clear exact institutional URLs; the command is a dry run without this flag}
    {--backup-confirmed : Confirm a current database backup exists before a production apply}')]
#[Description('Safely remove known academy URLs from Teacher personal social fields.')]
class ReconcileTeacherSocialLinks extends Command
{
    private const FACEBOOK_URL = 'https://www.facebook.com/goldeneyeacademy';

    private const LINKEDIN_URL = 'https://www.linkedin.com/company/golden-eye-academy/';

    public function handle(): int
    {
        $apply = (bool) $this->option('apply');

        if ($apply && app()->environment('production') && ! $this->option('backup-confirmed')) {
            $this->error('Production apply refused: pass --backup-confirmed only after verifying a current database backup.');

            return self::FAILURE;
        }

        $facebookMatches = Teacher::query()->where('facebook_url', self::FACEBOOK_URL)->count();
        $linkedinMatches = Teacher::query()->where('linkedin_url', self::LINKEDIN_URL)->count();

        $this->table(
            ['Teacher field', 'Exact institutional URL', 'Matches'],
            [
                ['facebook_url', self::FACEBOOK_URL, $facebookMatches],
                ['linkedin_url', self::LINKEDIN_URL, $linkedinMatches],
            ],
        );

        if (! $apply) {
            $this->info('Dry run complete. Re-run with --apply after reviewing the exact-match counts.');

            return self::SUCCESS;
        }

        DB::transaction(function (): void {
            Teacher::query()
                ->where('facebook_url', self::FACEBOOK_URL)
                ->update(['facebook_url' => null]);

            Teacher::query()
                ->where('linkedin_url', self::LINKEDIN_URL)
                ->update(['linkedin_url' => null]);
        });

        Cache::forget('about_teachers');
        $this->info(($facebookMatches + $linkedinMatches).' exact institutional Teacher social value(s) cleared.');

        return self::SUCCESS;
    }
}

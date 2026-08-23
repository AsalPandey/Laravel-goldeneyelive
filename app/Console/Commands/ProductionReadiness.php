<?php

namespace App\Console\Commands;

use App\Http\Middleware\SecurityHeaders;
use App\Support\ApprovedCourseFaqDeploymentData;
use App\Support\Recaptcha;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\Process\Process;
use Throwable;

#[Signature('app:production-readiness {--skip-dependency-audits : Skip network/tool-dependent Composer and npm audit commands}')]
#[Description('Run read-only production deployment readiness checks without printing secrets.')]
class ProductionReadiness extends Command
{
    /** @var array<int, array{status: string, check: string, detail: string}> */
    private array $results = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->configurationChecks();
        $this->databaseChecks();
        $this->deliveryChecks();
        $this->filesystemAndBuildChecks();

        if (! $this->option('skip-dependency-audits')) {
            $this->dependencyChecks();
        } else {
            $this->record('WARN', 'Dependency audits', 'Skipped explicitly; run composer audit --locked and npm audit before release.');
        }

        $this->table(['Status', 'Check', 'Detail'], $this->results);

        $failures = collect($this->results)->where('status', 'FAIL')->count();
        $warnings = collect($this->results)->where('status', 'WARN')->count();
        $this->line("Readiness summary: {$failures} FAIL, {$warnings} WARN.");

        return $failures === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function configurationChecks(): void
    {
        $environment = app()->environment();
        $this->record('PASS', 'Environment', $environment);
        $this->record(version_compare(PHP_VERSION, '8.4.0', '>=') ? 'PASS' : 'FAIL', 'PHP version', PHP_VERSION);
        $this->record(filled(config('app.key')) ? 'PASS' : 'FAIL', 'Application key', filled(config('app.key')) ? 'present' : 'missing');
        $this->record(app()->hasDebugModeEnabled() && app()->isProduction() ? 'FAIL' : 'PASS', 'Production debug', config('app.debug') ? 'enabled' : 'disabled');

        $appUrl = (string) config('app.url');
        $https = str_starts_with(strtolower($appUrl), 'https://');
        $this->record($https ? 'PASS' : (app()->isProduction() ? 'FAIL' : 'WARN'), 'Secure application URL', $https ? 'HTTPS configured' : 'APP_URL is not HTTPS');

        $captchaStatus = Recaptcha::status();
        $this->record(
            $captchaStatus === Recaptcha::Enabled ? 'PASS' : (app()->isProduction() ? 'FAIL' : 'WARN'),
            'CAPTCHA protection',
            $captchaStatus === Recaptcha::Enabled ? 'site and secret keys present' : 'required environment keys are not both present',
        );

        $this->record(
            config('security.csp_report_only', true) ? 'WARN' : 'PASS',
            'Content Security Policy',
            config('security.csp_report_only', true) ? 'report-only; validate production reports before enforcement' : 'enforced',
        );
    }

    private function databaseChecks(): void
    {
        $connection = (string) config('database.default');
        $productionConnectionReady = ! app()->isProduction()
            || in_array($connection, ['mysql', 'mariadb'], true);
        $this->record(
            $productionConnectionReady ? 'PASS' : 'FAIL',
            'Database connection',
            $productionConnectionReady ? $connection : "{$connection}; production requires MySQL/MariaDB",
        );

        try {
            DB::connection()->getPdo();
            $this->record('PASS', 'Database connectivity', 'connected');

            $migrator = app('migrator');
            if (! $migrator->repositoryExists()) {
                $this->record('FAIL', 'Pending migrations', 'migration repository is missing');
            } else {
                $files = $migrator->getMigrationFiles(database_path('migrations'));
                $pending = count(array_diff(array_keys($files), $migrator->getRepository()->getRan()));
                $this->record($pending === 0 ? 'PASS' : 'FAIL', 'Pending migrations', (string) $pending);
            }

            $courseFaqReady = Schema::hasTable('courses')
                && Schema::hasTable('f_a_q_s')
                && Schema::hasTable('course_faq')
                && Schema::hasColumns('course_faq', ['course_id', 'faq_id']);
            $this->record($courseFaqReady ? 'PASS' : 'FAIL', 'Course-FAQ schema', $courseFaqReady ? 'required tables and columns present' : 'required schema is incomplete');

            if ($courseFaqReady) {
                $targetSlugs = array_keys(ApprovedCourseFaqDeploymentData::targetCourses());
                $targetsFound = DB::table('courses')->whereIn('slug', $targetSlugs)->count();
                $targetAssignments = DB::table('course_faq')
                    ->join('courses', 'courses.id', '=', 'course_faq.course_id')
                    ->whereIn('courses.slug', $targetSlugs)
                    ->count();
                $ready = $targetsFound === count($targetSlugs) && $targetAssignments === 68;
                $this->record($ready ? 'PASS' : 'WARN', 'Approved deployment data', "{$targetsFound}/".count($targetSlugs)." target courses; {$targetAssignments}/68 assignments (read-only summary)");
            }
        } catch (Throwable $exception) {
            $this->record('FAIL', 'Database connectivity', $exception::class);
        }
    }

    private function deliveryChecks(): void
    {
        $driver = (string) config('queue.default');
        $strategy = (string) config('queue.worker_strategy');
        $supported = $driver === 'sync' || in_array($strategy, ['cron', 'persistent'], true);
        $this->record($supported ? 'PASS' : (app()->isProduction() ? 'FAIL' : 'WARN'), 'Queue processing', $driver === 'sync' ? 'sync fallback' : "{$driver}; worker strategy ".($strategy ?: 'missing'));

        $pending = $this->tableCount((string) config("queue.connections.{$driver}.table", 'jobs'));
        $failed = $this->tableCount((string) config('queue.failed.table', 'failed_jobs'));
        $pendingStatus = $driver === 'sync' ? 'PASS' : ($pending === null ? (app()->isProduction() ? 'FAIL' : 'WARN') : 'PASS');
        $this->record($pendingStatus, 'Pending jobs', $driver === 'sync' ? 'not applicable to sync processing' : ($pending === null ? 'queue table unavailable' : (string) $pending));
        $failedStatus = $failed === null ? (app()->isProduction() ? 'FAIL' : 'WARN') : ($failed === 0 ? 'PASS' : 'WARN');
        $this->record($failedStatus, 'Failed jobs', $failed === null ? 'failed-job table unavailable' : (string) $failed);

        $scheduled = collect(app(Schedule::class)->events())
            ->contains(fn ($event): bool => str_contains((string) $event->command, 'queue:work'));
        $schedulerReady = $driver === 'sync'
            || $strategy === 'persistent'
            || ($strategy === 'cron' && $scheduled);
        $this->record($schedulerReady ? 'PASS' : 'FAIL', 'Scheduler readiness', $strategy === 'cron' && $scheduled ? 'cron-safe queue drain scheduled; deployment cron must run schedule:run' : ($driver === 'sync' || $strategy === 'persistent' ? 'no cron queue drain required for the selected mode' : 'no active queue processing strategy'));

        $mailer = (string) config('mail.default');
        $from = (string) config('mail.from.address');
        $mailerConfiguration = (array) config("mail.mailers.{$mailer}", []);
        $transport = (string) ($mailerConfiguration['transport'] ?? $mailer);
        $transportReady = match ($transport) {
            'smtp' => filled($mailerConfiguration['url'] ?? null)
                || ! in_array((string) ($mailerConfiguration['host'] ?? ''), ['', '127.0.0.1', 'localhost'], true),
            'log', 'array' => false,
            default => $mailerConfiguration !== [],
        };
        $mailReady = $transportReady
            && filled($from)
            && $from !== 'hello@example.com';
        $this->record($mailReady ? 'PASS' : (app()->isProduction() ? 'FAIL' : 'WARN'), 'Mail delivery', $mailReady ? "{$mailer} configured with a sender" : "{$mailer} is not a production delivery transport");
    }

    private function filesystemAndBuildChecks(): void
    {
        foreach ([
            storage_path(),
            storage_path('app'),
            storage_path('framework'),
            storage_path('logs'),
            base_path('bootstrap/cache'),
            public_path('site/img'),
        ] as $path) {
            $this->record(is_dir($path) && is_writable($path) ? 'PASS' : 'FAIL', 'Writable path', $path);
        }

        $manifestPath = public_path('build/manifest.json');
        $manifest = is_file($manifestPath) ? json_decode((string) file_get_contents($manifestPath), true) : null;
        $this->record(is_array($manifest) ? 'PASS' : 'FAIL', 'Build manifest', is_array($manifest) ? 'present and valid JSON' : 'missing or invalid');

        $expectedAssetsPresent = is_array($manifest)
            && collect($manifest)->every(fn (array $entry): bool => isset($entry['file']) && is_file(public_path('build/'.$entry['file'])));
        $this->record($expectedAssetsPresent ? 'PASS' : 'FAIL', 'Expected build assets', $expectedAssetsPresent ? 'all manifest entries exist' : 'one or more manifest assets are missing');

        $htaccess = is_file(public_path('.htaccess')) ? (string) file_get_contents(public_path('.htaccess')) : '';
        $cachePolicyPresent = str_contains($htaccess, 'max-age=31536000, immutable')
            && str_contains($htaccess, 'max-age=3600, must-revalidate');
        $this->record($cachePolicyPresent ? 'PASS' : 'FAIL', 'Static cache policy', $cachePolicyPresent ? 'fingerprinted and mutable policies are separated' : 'required cache directives are missing');
        $bootstrap = is_file(base_path('bootstrap/app.php')) ? (string) file_get_contents(base_path('bootstrap/app.php')) : '';
        $securityHeadersRegistered = class_exists(SecurityHeaders::class)
            && str_contains($bootstrap, 'SecurityHeaders::class');
        $this->record($securityHeadersRegistered ? 'PASS' : 'FAIL', 'Security headers', $securityHeadersRegistered ? 'application middleware registered' : 'middleware is missing or not registered');
    }

    private function dependencyChecks(): void
    {
        foreach ([
            'Composer audit' => ['composer', 'audit', '--locked', '--no-interaction'],
            'npm audit' => ['npm', 'audit'],
        ] as $label => $command) {
            try {
                $process = new Process($command, base_path(), timeout: 60);
                $process->run();
                $this->record($process->isSuccessful() ? 'PASS' : 'FAIL', $label, $process->isSuccessful() ? 'no blocking advisories' : 'audit command reported advisories or failed');
            } catch (Throwable $exception) {
                $this->record('WARN', $label, 'could not execute: '.$exception::class);
            }
        }
    }

    private function tableCount(string $table): ?int
    {
        try {
            return Schema::hasTable($table) ? DB::table($table)->count() : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function record(string $status, string $check, string $detail): void
    {
        $this->results[] = compact('status', 'check', 'detail');
    }
}

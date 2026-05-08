<?php

namespace App\Providers;

use App\Models\CourseCategory;
use App\Models\FAQ;
use App\Models\Notice;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Share settings and FAQs with relevant views with efficient caching
        View::composer(['site.*', 'errors.*', 'admin.dashboard'], function ($view) {
            $viewData = cache()->remember('site_shared_data', 3600, function () {
                $settings = [];
                $footerFaqs = [];
                $activeNotice = null;
                $activeNoticeBar = null;
                $activeNoticePopup = null;
                $activeCategories = [];

                try {
                    // Only proceed if database connection is available and tables exist
                    if (app()->environment('testing') || (config('database.default') && Schema::hasTable('site_settings'))) {
                        $settings = SiteSetting::all()->pluck('value', 'key')->toArray();

                        if (Schema::hasTable('f_a_q_s')) {
                            $footerFaqs = FAQ::where('status', 'active')
                                ->orderBy('order_priority', 'asc')
                                ->take(5)
                                ->get()
                                ->toArray();
                        }

                        if (Schema::hasTable('notices')) {
                            $activeNotices = Notice::where('status', 'active')
                                ->where(function ($q) {
                                    $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
                                })
                                ->where(function ($q) {
                                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                                })
                                ->orderByDesc('is_urgent')
                                ->latest('updated_at')
                                ->get();

                            $activeNotice = $activeNotices->first()?->toArray();
                            $activeNoticeBar = $activeNotices->firstWhere('display_type', 'bar')?->toArray();
                            $activeNoticePopup = $activeNotices
                                ->first(fn (Notice $notice): bool => in_array($notice->display_type ?? 'popup', ['popup', 'standard'], true))
                                ?->toArray();
                        }

                        if (Schema::hasTable('course_categories')) {
                            $activeCategories = CourseCategory::where('status', 'active')
                                ->whereNotNull('slug')
                                ->withCount(['courses' => function ($q) {
                                    $q->where('status', 'active');
                                }])
                                ->orderBy('order_priority', 'asc')
                                ->get()
                                ->toArray();
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('SettingsServiceProvider: Could not load site settings. '.$e->getMessage());
                }

                return [
                    'settings' => $settings,
                    'footerFaqs' => $footerFaqs,
                    'activeNotice' => $activeNotice,
                    'activeNoticeBar' => $activeNoticeBar,
                    'activeNoticePopup' => $activeNoticePopup,
                    'activeCategories' => $activeCategories,
                ];
            });

            $view->with([
                'settings' => $viewData['settings'] ?? [],
                'footerFaqs' => collect($viewData['footerFaqs'] ?? [])->map(fn ($item) => (object) $item),
                'activeNotice' => ($viewData['activeNotice'] ?? null) ? (object) $viewData['activeNotice'] : null,
                'activeNoticeBar' => ($viewData['activeNoticeBar'] ?? null) ? (object) $viewData['activeNoticeBar'] : null,
                'activeNoticePopup' => ($viewData['activeNoticePopup'] ?? null) ? (object) $viewData['activeNoticePopup'] : null,
                'categories' => collect($viewData['activeCategories'] ?? [])->map(fn ($item) => (object) $item),
            ]);
        });
    }
}

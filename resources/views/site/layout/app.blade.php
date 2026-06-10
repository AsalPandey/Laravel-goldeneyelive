<!DOCTYPE html>
<html lang="en">
@include('site.layout.header')
@php
    $trackingRouteName = request()->route()?->getName();
@endphp
<body class="bg-zinc-50/50"
    data-tracking-event="{{ trim($__env->yieldContent('tracking_event', '')) }}"
    data-source-page="{{ trim($__env->yieldContent('tracking_source_page', request('source_page', $trackingRouteName ?? request()->path()))) }}"
    data-source-section="{{ trim($__env->yieldContent('tracking_source_section', request('source_section', ''))) }}"
    data-selected-course="{{ trim($__env->yieldContent('tracking_selected_course', request('selected_course', request('course', '')))) }}"
    data-audience-type="{{ trim($__env->yieldContent('tracking_audience_type', request('audience_type', ''))) }}"
    data-inquiry-intent="{{ trim($__env->yieldContent('tracking_inquiry_intent', request('inquiry_intent', ''))) }}">
    <!-- Premium Loading Spinner -->
    <div id="spinner" class="bg-brand-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex flex-column align-items-center justify-content-center" style="z-index: 999999;">
        <div class="position-relative mb-3">
            <div class="spinner-border text-brand-gold" style="width: 3rem; height: 3rem; border-width: 0.2em;" role="status"></div>
            <div class="position-absolute top-50 start-50 translate-middle">
                <x-app-logo-icon class="size-5 fill-brand-gold animate-pulse" />
            </div>
        </div>
        <p class="text-brand-gold fw-black text-uppercase tracking-[0.4em]" style="font-size: 10px;">{{ $settings['site_name'] ?? 'GoldenEye' }} Academy</p>
    </div>

    <!-- Fail-safe Spinner Removal -->
    <script>
        (function() {
            const spinner = document.getElementById('spinner');
            if (spinner) {
                // Ensure it's removed even if main.js fails
                setTimeout(() => {
                    spinner.classList.remove('show');
                    // Completely remove from DOM after transition to prevent z-index issues
                    setTimeout(() => {
                        if (spinner.classList.contains('show') === false) {
                            spinner.style.display = 'none';
                        }
                    }, 600);
                }, 350);
            }
        })();
    </script>

    @include('site.layout.navbar')

	    @php
	        $noticeStripData = null;
	        $noticePopupData = null;
	        $courseHelpPopupLink = route('join-now', [
	            'course' => 'undecided',
	            'selected_course' => 'undecided',
	            'source_page' => 'popup',
	            'source_section' => 'course-help-popup',
	            'inquiry_intent' => 'course_help',
	        ]);
	        $courseHelpCtaText = trim((string) ($settings['popup_button_text'] ?? 'Ask for Course Help')) ?: 'Ask for Course Help';
	        $courseHelpCtaText = strtolower($courseHelpCtaText) === 'ask for course guidance' ? 'Ask for Course Help' : $courseHelpCtaText;
	        $popupBadgeText = trim((string) ($settings['notice_badge_text'] ?? 'Guidance First')) ?: 'Guidance First';
	        $popupDismissText = trim((string) ($settings['notice_dismiss_text'] ?? 'Close')) ?: 'Close';
	        $popupTitleText = trim((string) ($settings['popup_title'] ?? 'Course Guidance Before Enrollment')) ?: 'Course Guidance Before Enrollment';
	        $popupSubtitleText = trim((string) ($settings['popup_subtitle'] ?? $settings['popup_description'] ?? 'Not sure whether to choose IELTS, PTE, Korean, Japanese, computer skills, or web development? Send a quick course-help request first.')) ?: 'Not sure whether to choose IELTS, PTE, Korean, Japanese, computer skills, or web development? Send a quick course-help request first.';
	        $popupLink = trim((string) ($settings['popup_register_link'] ?? '')) ?: $courseHelpPopupLink;

	        $buildNoticeData = function ($notice) use ($courseHelpCtaText, $courseHelpPopupLink, $popupDismissText) {
	            $noticeButtonText = trim((string) ($notice->button_text ?? '')) ?: $courseHelpCtaText;
	            $noticeButtonText = strtolower($noticeButtonText) === 'ask for course guidance' ? 'Ask for Course Help' : $noticeButtonText;

	            return (object) [
	                'id' => 'notice_'.$notice->id,
	                'badge' => $notice->badge ?? 'Guidance First',
	                'title' => $notice->title,
	                'subtitle' => $notice->subtitle,
	                'button_text' => $noticeButtonText,
	                'button_link' => $notice->link ?? $courseHelpPopupLink,
	                'image' => \App\Support\PublicAsset::url($notice->image ?? null, 'site/img/carousel-1.png'),
	                'display_type' => $notice->display_type ?? 'popup',
	                'dismiss_text' => $popupDismissText,
	            ];
	        };

        if (isset($activeNoticeBar) && $activeNoticeBar) {
            $noticeStripData = $buildNoticeData($activeNoticeBar);
        }

        if (isset($activeNoticePopup) && $activeNoticePopup) {
            $noticePopupData = $buildNoticeData($activeNoticePopup);
        }

        if (! $noticeStripData && ! $noticePopupData && isset($activeNotice) && $activeNotice) {
            $sharedNoticeData = $buildNoticeData($activeNotice);

            if (($activeNotice->display_type ?? 'popup') === 'bar') {
                $noticeStripData = $sharedNoticeData;
            } else {
                $noticePopupData = $sharedNoticeData;
            }
        }

	        if (! $noticePopupData && (($settings['popup_status'] ?? null) === 'active')) {
	            $noticePopupData = (object) [
	                'id' => 'marketing_popup',
	                'badge' => $popupBadgeText,
	                'title' => $popupTitleText,
	                'subtitle' => $popupSubtitleText,
	                'button_text' => $courseHelpCtaText,
	                'button_link' => $popupLink,
	                'image' => \App\Support\PublicAsset::url($settings['popup_image'] ?? null, 'site/img/carousel-1.png'),
	                'display_type' => 'popup',
	                'dismiss_text' => $popupDismissText,
	            ];
	        }
	    @endphp

    @if($noticeStripData)
        <section id="siteNoticeStrip" class="site-notice-strip" data-notice-id="{{ $noticeStripData->id }}">
            <div class="container d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <span class="site-notice-badge">{{ $noticeStripData->badge }}</span>
                    <div>
                        <p class="site-notice-title">{{ $noticeStripData->title }}</p>
                        @if($noticeStripData->subtitle)
                            <p class="site-notice-subtitle">{{ $noticeStripData->subtitle }}</p>
                        @endif
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    <a href="{{ $noticeStripData->button_link }}" data-cta="notice-strip" class="btn btn-primary site-notice-cta">{{ $noticeStripData->button_text }}</a>
                    <button type="button" class="site-notice-close" aria-label="Dismiss notice" onclick="dismissSiteNotice('{{ $noticeStripData->id }}')">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </section>
        <script>
            (function () {
                const notice = document.getElementById('siteNoticeStrip');
                if (!notice) return;

                const id = notice.dataset.noticeId;
                const dismissed = localStorage.getItem('notice_dismissed_' + id);
                if (dismissed) {
                    notice.remove();
                }

                window.dismissSiteNotice = function (noticeId) {
                    localStorage.setItem('notice_dismissed_' + noticeId, new Date().toISOString());
                    notice.remove();
                };
            })();
        </script>
    @endif

    @yield('content')

    @include('site.layout.footer')
    @include('sweetalert::alert')

    @if($noticePopupData)
        <div id="siteNoticePopup" class="modal fade site-notice-popup" tabindex="-1" aria-hidden="true" data-notice-id="{{ $noticePopupData->id }}">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 overflow-hidden">
                    <button type="button" class="site-notice-popup-close" data-bs-dismiss="modal" aria-label="Close" onclick="dismissSitePopup('{{ $noticePopupData->id }}')">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="site-notice-popup-media">
                        <img src="{{ $noticePopupData->image }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $noticePopupData->title }}" loading="lazy" decoding="async" width="1001" height="561">
                    </div>
                    <div class="site-notice-popup-body">
	                        <span class="site-notice-badge">{{ $noticePopupData->badge ?: 'Guidance First' }}</span>
	                        <h2>{{ $noticePopupData->title }}</h2>
	                        @if($noticePopupData->subtitle)
	                            <p>{{ $noticePopupData->subtitle }}</p>
	                        @endif
	                        <div class="d-grid gap-2">
	                            <a href="{{ $noticePopupData->button_link }}" id="siteNoticePopupCta" data-default-href="{{ $noticePopupData->button_link }}" data-cta="notice-popup-course-help" data-source-page="popup" data-source-section="course-help-popup" data-inquiry-intent="course_help" onclick="dismissSitePopup('{{ $noticePopupData->id }}')" class="btn btn-primary site-notice-popup-cta py-3 rounded-pill font-black uppercase tracking-widest">
	                                {{ $noticePopupData->button_text }}
	                            </a>
                            <button type="button" onclick="dismissSitePopup('{{ $noticePopupData->id }}')" data-bs-dismiss="modal" class="btn btn-link site-notice-popup-later">
                                {{ $noticePopupData->dismiss_text }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	        <script>
	            (function () {
	                const popupId = '{{ $noticePopupData->id }}';
	                const autoClosedKey = 'course_help_popup_closed_' + popupId;
	                const dismissedKey = 'popup_dismissed_' + popupId;
	                const autoDelayMs = 7000;
	                let autoOpened = false;
	                let scrollTriggered = false;

	                function popupElement() {
	                    return document.getElementById('siteNoticePopup');
	                }

	                function popupCtaElement() {
	                    return document.getElementById('siteNoticePopupCta');
	                }

	                function shouldOpenCourseHelpPopup(link) {
	                    if (!link || link.closest('#siteNoticePopup')) {
	                        return false;
	                    }

	                    const label = (link.dataset.ctaLabel || link.textContent || '').trim().toLowerCase();
	                    const cta = (link.dataset.cta || '').toLowerCase();
	                    const href = link.getAttribute('href') || '';

	                    if (!href || href.startsWith('#') || link.target === '_blank') {
	                        return false;
	                    }

	                    return cta.includes('course-help')
	                        || cta.includes('course-guidance')
	                        || label.includes('ask for course help')
	                        || label.includes('ask for course guidance')
	                        || label.includes('course guidance');
	                }

	                function syncPopupCtaFromLink(link) {
	                    const popupCta = popupCtaElement();

	                    if (!popupCta) {
	                        return;
	                    }

	                    const nextHref = link?.href || popupCta.dataset.defaultHref || popupCta.href;
	                    popupCta.href = nextHref;

	                    if (link) {
	                        popupCta.dataset.sourcePage = link.dataset.sourcePage || document.body.dataset.sourcePage || 'popup';
	                        popupCta.dataset.sourceSection = link.dataset.sourceSection || link.dataset.cta || 'course-help-popup';
	                        popupCta.dataset.selectedCourse = link.dataset.selectedCourse || document.body.dataset.selectedCourse || '';
	                        popupCta.dataset.audienceType = link.dataset.audienceType || document.body.dataset.audienceType || '';
	                        popupCta.dataset.inquiryIntent = link.dataset.inquiryIntent || document.body.dataset.inquiryIntent || 'course_help';
	                    }
	                }

	                function recentlyDismissed() {
	                    const lastDismiss = localStorage.getItem(dismissedKey);

	                    if (!lastDismiss) {
	                        return false;
	                    }

	                    return (Date.now() - Number(lastDismiss)) < (12 * 60 * 60 * 1000);
	                }

	                window.dismissSitePopup = function (id) {
	                    const popup = popupElement();
	                    if (popup) {
	                        const modal = window.bootstrap ? bootstrap.Modal.getInstance(popup) : null;
	                        if (modal) {
	                            modal.hide();
	                        }
	                    }

	                    sessionStorage.setItem(autoClosedKey, '1');
	                    localStorage.setItem('popup_dismissed_' + id, String(Date.now()));
	                };

	                window.openSiteNoticePopup = function (options = {}) {
	                    const popup = popupElement();

	                    if (!popup || !window.bootstrap) {
	                        return false;
	                    }

	                    const force = options.force === true;

	                    if (!force && (sessionStorage.getItem(autoClosedKey) || recentlyDismissed())) {
	                        return false;
	                    }

	                    syncPopupCtaFromLink(options.link || null);

	                    const noticeModal = bootstrap.Modal.getOrCreateInstance(popup);
	                    noticeModal.show();

	                    return true;
	                };

	                document.addEventListener('click', function (event) {
	                    const trigger = event.target.closest('a[href]');

	                    if (!shouldOpenCourseHelpPopup(trigger)) {
	                        return;
	                    }

	                    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.defaultPrevented) {
	                        return;
	                    }

	                    event.preventDefault();
	                    window.openSiteNoticePopup({
	                        force: true,
	                        link: trigger,
	                    });
	                });

	                function maybeAutoOpen() {
	                    if (autoOpened || window.location.pathname.includes('/join-now')) {
	                        return;
	                    }

	                    autoOpened = window.openSiteNoticePopup({
	                        force: false,
	                    });
	                }

	                window.addEventListener('load', function () {
	                    window.setTimeout(maybeAutoOpen, autoDelayMs);
	                });

	                window.addEventListener('scroll', function () {
	                    if (scrollTriggered || autoOpened || window.location.pathname.includes('/join-now')) {
	                        return;
	                    }

	                    const scrollable = document.documentElement.scrollHeight - window.innerHeight;

	                    if (scrollable > 0 && (window.scrollY / scrollable) >= 0.25) {
	                        scrollTriggered = true;
	                        maybeAutoOpen();
	                    }
	                }, { passive: true });
	            })();
	        </script>
	    @endif

	    @php
	        $whatsappNumber = $settings['whatsapp_number'] ?? '9779856058599';
	        $whatsappCleanNumber = str_replace(['+', ' ', '-'], '', $whatsappNumber);
	        $whatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?');
	        $whatsappCtaText = trim((string) ($settings['whatsapp_cta_text'] ?? $settings['whatsapp_button_text'] ?? 'Message on WhatsApp')) ?: 'Message on WhatsApp';
	        $whatsappCtaText = $whatsappCtaText === 'Message us on WhatsApp' ? 'Message on WhatsApp' : $whatsappCtaText;
	        $whatsappCtaSubtext = trim((string) ($settings['whatsapp_cta_subtext'] ?? ''));
	    @endphp
	    @if($whatsappNumber)
	        <!-- WhatsApp CTA -->
	        <div class="whatsapp-btn-container">
	            <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" class="whatsapp-chat-cta" data-cta="whatsapp-chat" aria-label="{{ $whatsappCtaText }}">
	                <span class="whatsapp-chat-icon"><i class="fab fa-whatsapp"></i></span>
	                <span class="whatsapp-chat-copy">
	                    <strong>{{ $whatsappCtaText }}</strong>
	                    @if($whatsappCtaSubtext)
	                        <small>{{ $whatsappCtaSubtext }}</small>
	                    @endif
	                </span>
	            </a>
	        </div>
	        <script>
	            (function () {
	                const widget = document.querySelector('.whatsapp-btn-container');
	                const hero = document.querySelector('.home-hero');

	                if (!widget || !hero) {
	                    return;
	                }

	                const mobileQuery = window.matchMedia('(max-width: 575.98px)');
	                const syncWidgetVisibility = function () {
	                    const shouldHide = mobileQuery.matches && hero.getBoundingClientRect().bottom > (window.innerHeight - 96);
	                    widget.classList.toggle('is-hidden-over-hero', shouldHide);
	                };

	                syncWidgetVisibility();
	                window.addEventListener('scroll', syncWidgetVisibility, { passive: true });
	                window.addEventListener('resize', syncWidgetVisibility);
	            })();
	        </script>
	    @endif

</body>
</html>

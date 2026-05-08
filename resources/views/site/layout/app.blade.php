<!DOCTYPE html>
<html lang="en">
@include('site.layout.header')
<body class="bg-zinc-50/50">
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
        $buildNoticeData = function ($notice) {
            return (object) [
                'id' => 'notice_'.$notice->id,
                'badge' => $notice->badge ?? 'Announcement',
                'title' => $notice->title,
                'subtitle' => $notice->subtitle,
                'button_text' => $notice->button_text ?? 'Learn More',
                'button_link' => $notice->link ?? route('join-now'),
                'image' => \App\Support\PublicAsset::url($notice->image ?? null, 'site/img/carousel-1.png'),
                'display_type' => $notice->display_type ?? 'popup',
                'dismiss_text' => 'Close',
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
                'badge' => $settings['notice_badge_text'] ?? 'Admissions Open',
                'title' => $settings['popup_title'] ?? 'Still choosing? Ask us first.',
                'subtitle' => $settings['popup_subtitle'] ?? $settings['popup_description'] ?? 'Send your goal and our team will help you pick the right next step.',
                'button_text' => $settings['popup_button_text'] ?? 'Ask for Course Help',
                'button_link' => $settings['popup_register_link'] ?? route('join-now'),
                'image' => \App\Support\PublicAsset::url($settings['popup_image'] ?? null, 'site/img/carousel-1.png'),
                'display_type' => 'popup',
                'dismiss_text' => $settings['notice_dismiss_text'] ?? 'Maybe Later',
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
                        <img src="{{ $noticePopupData->image }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $noticePopupData->title }}">
                    </div>
                    <div class="site-notice-popup-body">
                        <span class="site-notice-badge">{{ $noticePopupData->badge }}</span>
                        <h2>{{ $noticePopupData->title }}</h2>
                        @if($noticePopupData->subtitle)
                            <p>{{ $noticePopupData->subtitle }}</p>
                        @endif
                        <div class="d-grid gap-2">
                            <a href="{{ $noticePopupData->button_link }}" data-cta="notice-popup" onclick="dismissSitePopup('{{ $noticePopupData->id }}')" class="btn btn-primary py-3 rounded-pill font-black uppercase tracking-widest">
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
            function dismissSitePopup(id) {
                const popup = document.getElementById('siteNoticePopup');
                if (popup) {
                    const modal = bootstrap.Modal.getInstance(popup);
                    if (modal) {
                        modal.hide();
                    }
                }
                localStorage.setItem('popup_dismissed_' + id, String(new Date().getTime()));
            }

            window.addEventListener('load', function() {
                const popupId = '{{ $noticePopupData->id }}';
                const lastDismiss = localStorage.getItem('popup_dismissed_' + popupId);
                const now = new Date().getTime();

                if (!lastDismiss || (now - Number(lastDismiss)) > (2 * 60 * 60 * 1000)) {
                    setTimeout(() => {
                        const popup = document.getElementById('siteNoticePopup');

                        if (popup && window.bootstrap) {
                            const noticeModal = new bootstrap.Modal(popup);
                            noticeModal.show();
                        }
                    }, 650);
                }
            });
        </script>
    @endif

    @php
        $whatsappNumber = $settings['whatsapp_number'] ?? '9779856058599';
        $whatsappCleanNumber = str_replace(['+', ' ', '-'], '', $whatsappNumber);
        $whatsappMessage = rawurlencode($settings['whatsapp_prefill_message'] ?? 'Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?');
    @endphp
    @if($whatsappNumber)
        <!-- WhatsApp CTA -->
        <div class="whatsapp-btn-container">
            <a href="https://wa.me/{{ $whatsappCleanNumber }}?text={{ $whatsappMessage }}" target="_blank" rel="noopener" class="whatsapp-chat-cta" data-cta="whatsapp-chat" aria-label="Chat with GoldenEye Academy on WhatsApp">
                <span class="whatsapp-chat-icon"><i class="fab fa-whatsapp"></i></span>
                <span class="whatsapp-chat-copy">
                    <strong>{{ $settings['whatsapp_cta_text'] ?? $settings['whatsapp_button_text'] ?? 'Message us on WhatsApp' }}</strong>
                    <small>{{ $settings['whatsapp_cta_subtext'] ?? 'Casual questions. Quick reply.' }}</small>
                </span>
            </a>
        </div>
    @endif

</body>
</html>

@php
    $analyticsEnabled = ($settings['analytics_tracking_enabled'] ?? 'active') !== 'disabled';
@endphp

<script>
    (function () {
        if (window.goldenEyeAnalyticsLoaded) {
            return;
        }

        window.goldenEyeAnalyticsLoaded = true;

        const config = {
            endpoint: @json(route('analytics.events.store')),
            csrfToken: @json(csrf_token()),
            enabled: @json($analyticsEnabled),
        };

        const allowedEvents = new Set([
            'cta_click',
            'course_detail_view',
            'inquiry_step_1_submit',
            'inquiry_step_2_submit',
            'course_help_submit',
            'add_more_details_click',
            'whatsapp_click',
            'phone_click',
            'course_filter_used',
            'parent_inquiry_click',
            'study_abroad_inquiry_click',
            'admin_lead_status_change',
            'phone_validation_error',
        ]);

        const shortText = function (value, fallback = '') {
            if (value === undefined || value === null) {
                return fallback;
            }

            return String(value).replace(/\s+/g, ' ').trim().slice(0, 255) || fallback;
        };

        const currentParams = function () {
            return new URLSearchParams(window.location.search);
        };

        const deviceType = function () {
            if (window.matchMedia('(max-width: 767px)').matches) {
                return 'mobile';
            }

            if (window.matchMedia('(max-width: 1024px)').matches) {
                return 'tablet';
            }

            return 'desktop';
        };

        const bodyContext = function () {
            const params = currentParams();
            const body = document.body;

            return {
                source_page: params.get('source_page') || body?.dataset.sourcePage || window.location.pathname,
                source_section: params.get('source_section') || body?.dataset.sourceSection || '',
                cta_label: '',
                selected_course: params.get('selected_course') || params.get('course') || body?.dataset.selectedCourse || '',
                audience_type: params.get('audience_type') || body?.dataset.audienceType || '',
                inquiry_intent: params.get('inquiry_intent') || body?.dataset.inquiryIntent || '',
                device_type: deviceType(),
                timestamp: new Date().toISOString(),
            };
        };

        const selectedCourseFromHref = function (href) {
            if (!href) {
                return '';
            }

            try {
                const url = new URL(href, window.location.origin);
                const explicitCourse = url.searchParams.get('selected_course') || url.searchParams.get('course');

                if (explicitCourse) {
                    return explicitCourse;
                }

                const match = url.pathname.match(/^\/courses\/([^/]+)/);

                return match ? decodeURIComponent(match[1]) : '';
            } catch (error) {
                return '';
            }
        };

        const inferredAudience = function (text) {
            const value = text.toLowerCase();

            if (value.includes('parent')) {
                return 'parent';
            }

            if (value.includes('study') || value.includes('abroad') || value.includes('ielts') || value.includes('pte')) {
                return 'study_abroad_applicant';
            }

            if (value.includes('computer') || value.includes('job') || value.includes('web')) {
                return 'job_skill_learner';
            }

            return '';
        };

        const inferredIntent = function (text) {
            const value = text.toLowerCase();

            if (value.includes('whatsapp')) {
                return 'whatsapp_message';
            }

            if (value.includes('phone')) {
                return 'phone_call';
            }

            if (value.includes('guidance') || value.includes('parent') || value.includes('study')) {
                return 'course_guidance';
            }

            return '';
        };

        const payloadForElement = function (element) {
            const label = element.matches('a[href^="tel:"]')
                ? 'Phone'
                : (element.href && element.href.includes('wa.me/') ? 'Message on WhatsApp' : shortText(element.dataset.ctaLabel || element.textContent));
            const sourceText = [
                element.dataset.cta,
                element.dataset.sourceSection,
                element.dataset.trackEvent,
                label,
                element.href || '',
            ].join(' ');

            return {
                source_page: element.dataset.sourcePage || '',
                source_section: element.dataset.sourceSection || element.dataset.cta || element.dataset.trackEvent || '',
                cta_label: label,
                selected_course: element.dataset.selectedCourse || selectedCourseFromHref(element.getAttribute('href')),
                audience_type: element.dataset.audienceType || inferredAudience(sourceText),
                inquiry_intent: element.dataset.inquiryIntent || inferredIntent(sourceText),
                metadata: {
                    cta_id: shortText(element.dataset.cta || element.dataset.trackEvent || ''),
                },
            };
        };

        const payloadForForm = function (form) {
            const data = new FormData(form);

            return {
                source_page: form.dataset.sourcePage || shortText(data.get('source_page') || data.get('landing_page')),
                source_section: form.dataset.sourceSection || shortText(data.get('source_section') || data.get('lead_source') || form.id || 'form'),
                cta_label: form.dataset.ctaLabel || 'Ask for Course Help',
                selected_course: form.dataset.selectedCourse || shortText(data.get('selected_course') || data.get('course') || ''),
                audience_type: form.dataset.audienceType || shortText(data.get('audience_type') || ''),
                inquiry_intent: form.dataset.inquiryIntent || shortText(data.get('inquiry_intent') || 'course_guidance'),
                metadata: {
                    form_id: shortText(form.id || ''),
                },
            };
        };

        const hasOptionalDetails = function (form) {
            return ['email', 'current_education_level', 'course', 'preferred_batch_time', 'goal', 'queries', 'contactMethod', 'address']
                .some(function (field) {
                    const input = form.querySelector('[name="' + field + '"]');

                    if (!input) {
                        return false;
                    }

                    const value = shortText(input.value);

                    return value !== '' && value !== 'undecided' && value !== 'Phone Call';
                });
        };

        window.goldenEyeTrack = function (eventName, payload = {}) {
            if (!allowedEvents.has(eventName)) {
                return;
            }

            const eventPayload = {
                ...bodyContext(),
                ...payload,
            };

            eventPayload.source_page = shortText(eventPayload.source_page, window.location.pathname);
            eventPayload.source_section = shortText(eventPayload.source_section);
            eventPayload.cta_label = shortText(eventPayload.cta_label);
            eventPayload.selected_course = shortText(eventPayload.selected_course);
            eventPayload.audience_type = shortText(eventPayload.audience_type);
            eventPayload.inquiry_intent = shortText(eventPayload.inquiry_intent);
            eventPayload.device_type = shortText(eventPayload.device_type, deviceType());
            eventPayload.timestamp = eventPayload.timestamp || new Date().toISOString();

            try {
                if (typeof window.gtag === 'function') {
                    window.gtag('event', eventName, {
                        source_page: eventPayload.source_page,
                        source_section: eventPayload.source_section,
                        cta_label: eventPayload.cta_label,
                        selected_course: eventPayload.selected_course,
                        audience_type: eventPayload.audience_type,
                        inquiry_intent: eventPayload.inquiry_intent,
                        device_type: eventPayload.device_type,
                    });
                }
            } catch (error) {
                // Analytics must never interrupt the website.
            }

            if (!config.enabled || !window.fetch) {
                return;
            }

            try {
                window.fetch(config.endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    keepalive: true,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        event_name: eventName,
                        ...eventPayload,
                    }),
                }).catch(function () {});
            } catch (error) {
                // Ignore tracking transport failures.
            }
        };

        document.addEventListener('click', function (event) {
            const element = event.target.closest('[data-cta], [data-track-event], a[href^="tel:"], a[href*="wa.me/"]');

            if (!element) {
                return;
            }

            const payload = payloadForElement(element);

            if (element.dataset.trackEvent) {
                window.goldenEyeTrack(element.dataset.trackEvent, payload);
            }

            if (element.matches('a[href^="tel:"]')) {
                window.goldenEyeTrack('phone_click', {...payload, cta_label: 'Phone'});
            }

            if (element.href && element.href.includes('wa.me/')) {
                window.goldenEyeTrack('whatsapp_click', {...payload, cta_label: 'Message on WhatsApp'});
            }

            if (element.dataset.cta) {
                window.goldenEyeTrack('cta_click', payload);

                const combinedContext = [
                    payload.source_section,
                    payload.cta_label,
                    payload.audience_type,
                    payload.inquiry_intent,
                ].join(' ').toLowerCase();

                if (combinedContext.includes('parent')) {
                    window.goldenEyeTrack('parent_inquiry_click', payload);
                }

                if (combinedContext.includes('study') || combinedContext.includes('abroad') || combinedContext.includes('ielts') || combinedContext.includes('pte')) {
                    window.goldenEyeTrack('study_abroad_inquiry_click', payload);
                }
            }
        }, true);

        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            const formPayload = payloadForForm(form);

            if (form.dataset.trackEvent) {
                window.goldenEyeTrack(form.dataset.trackEvent, formPayload);
            }

            if (form.dataset.analyticsForm === 'course-help') {
                window.goldenEyeTrack('inquiry_step_1_submit', formPayload);

                if (hasOptionalDetails(form)) {
                    window.goldenEyeTrack('inquiry_step_2_submit', formPayload);
                }
            }
        }, true);

        const trackPhoneValidationError = function (input, sourceSection) {
            window.setTimeout(function () {
                if (input.value && !input.validity.valid) {
                    window.goldenEyeTrack('phone_validation_error', {
                        source_section: sourceSection,
                        cta_label: 'Phone validation',
                        metadata: {
                            field: 'phone',
                            validation: 'client',
                        },
                    });
                }
            }, 0);
        };

        document.addEventListener('focusout', function (event) {
            if (event.target instanceof HTMLInputElement && event.target.matches('input[type="tel"]')) {
                trackPhoneValidationError(event.target, event.target.closest('form')?.dataset.sourceSection || 'phone-field');
            }
        }, true);

        document.addEventListener('invalid', function (event) {
            if (event.target instanceof HTMLInputElement && event.target.matches('input[type="tel"]')) {
                trackPhoneValidationError(event.target, event.target.closest('form')?.dataset.sourceSection || 'phone-field');
            }
        }, true);

        document.addEventListener('DOMContentLoaded', function () {
            const body = document.body;

            if (body?.dataset.trackingEvent) {
                window.goldenEyeTrack(body.dataset.trackingEvent);
            }
        });
    })();
</script>

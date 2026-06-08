<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Http\Requests\Site\JoinNowRequest;
use App\Http\Requests\Site\NewsletterRequest;
use App\Mail\ContactMail;
use App\Models\AnalyticsEvent;
use App\Models\Contact;
use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\SiteSetting;
use App\Support\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use RealRashid\SweetAlert\Facades\Alert;

class ContactController extends Controller
{
    public function contact()
    {
        return view('site.contact.contact');
    }

    /**
     * Handle Contact Form Submission
     */
    public function contactSubmit(ContactRequest $request)
    {
        $validated = $request->validated();

        $recaptchaResponse = $validated['g-recaptcha-response'] ?? null;

        if ($recaptchaResponse && ! $this->verifyRecaptcha($recaptchaResponse)) {
            return back()->withErrors(['g-recaptcha-response' => 'Security verification failed. Please try again.'])->withInput();
        }

        // Prevent Duplicate Inquiries (last 6h)
        $existing = Contact::where('email', $validated['email'])
            ->where('subject', $validated['subject'])
            ->where('created_at', '>=', now()->subHours(6))
            ->first();

        if ($existing) {
            Alert::info('Message Received', 'We have already received your message. Our team will get back to you shortly.');

            return back();
        }

        $validated['lead_source'] = $validated['lead_source'] ?? 'website';
        $validated['landing_page'] = $validated['landing_page'] ?? url()->previous();

        Contact::create($validated);

        Alert::success('Success', SiteSetting::getValue('contact_success_message', 'We appreciate your feedback. Our team will contact you soon.'));

        // Use database setting for admin email, fallback to config
        $adminEmail = SiteSetting::getValue('site_email', config('mail.from.address', 'contact@goldeneye.edu.np'));

        try {
            Mail::to($adminEmail)->queue(new ContactMail([
                'dataType' => 'contactMail',
                ...$validated,
            ]));
        } catch (\Exception $e) {
            // Log error with context but don't break for user
            logger()->error("Contact Mail failure for {$validated['email']}: ".$e->getMessage());
        }

        return back();
    }

    /**
     * Newsletter Subscription
     */
    public function newsletter(NewsletterRequest $request)
    {
        $validated = $request->validated();

        if ($request->filled('g-recaptcha-response')) {
            $isHuman = $this->verifyRecaptcha($request->input('g-recaptcha-response'));
            if (! $isHuman) {
                return back()->withErrors(['g-recaptcha-response' => 'Security verification failed. Please try again.'])->withInput();
            }
        }

        $exists = NewsLetter::where('email', $validated['email'])->exists();
        if ($exists) {
            Alert::info('Already Subscribed', 'This email is already part of our newsletter list.');

            return back();
        }

        NewsLetter::create(['email' => $validated['email']]);

        Alert::success('Success', SiteSetting::getValue('newsletter_success_message', 'Your email has been added to our newsletter.'));

        return back();
    }

    public function joinNow(Request $request)
    {
        $selectedCourse = $request->query('course') ?? $request->query('slug');

        return view('site.join-now.join-now', [
            'courses' => Course::publiclyVisible()->orderBy('name')->get(),
            'selectedCourse' => $selectedCourse,
            'helpTopics' => JoinNowRequest::helpTopics(),
        ]);
    }

    /**
     * Handle Enrollment (Join Now) Submission
     */
    public function joinNowSubmit(JoinNowRequest $request)
    {
        $validated = $request->validated();

        if (($validated['g-recaptcha-response'] ?? null) && ! $this->verifyRecaptcha($validated['g-recaptcha-response'])) {
            return back()->withErrors(['g-recaptcha-response' => 'Security verification failed. Please try again.'])->withInput();
        }

        $selectedCourseContext = $validated['selected_course'] ?? $validated['course'] ?? 'undecided';
        $needsCourseGuidance = $validated['course'] === 'undecided';
        $course = $needsCourseGuidance ? null : Course::publiclyVisible()->where('slug', $validated['course'])->first();

        if (! $needsCourseGuidance && ! $course) {
            return back()->withErrors(['course' => 'The selected course is currently not accepting enrollments.'])->withInput();
        }

        $courseName = $course?->name ?? 'Need help choosing a program';

        $existing = JoinNowQuery::where('phone', $validated['phone'])
            ->where('course', $courseName)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existing) {
            Alert::info('Request Received', 'We have already received your course help request. Our team is processing it.');

            return back();
        }

        $leadScore = $this->calculateJoinNowLeadScore($validated, $course);

        $trackingContext = [
            'lead_source' => $validated['lead_source'] ?? $validated['source_section'] ?? 'website',
            'landing_page' => $validated['landing_page'] ?? $validated['source_page'] ?? url()->previous(),
            'cta_id' => $validated['cta_id'] ?? $validated['inquiry_intent'] ?? null,
            'selected_course' => $selectedCourseContext,
            'source_page' => $validated['source_page'] ?? null,
            'source_section' => $validated['source_section'] ?? null,
            'audience_type' => $validated['audience_type'] ?? $this->inferAudienceType($validated),
            'inquiry_intent' => $validated['inquiry_intent'] ?? $this->inferInquiryIntent($validated),
            'lead_score' => $leadScore,
            'lead_status' => $this->leadStatusForScore($leadScore),
        ];

        unset($validated['full_name'], $validated['preferred_course']);

        $message = trim(implode("\n\n", array_filter([
            $validated['goal'] ?? null,
            $validated['queries'] ?? null,
        ])));

        $submissionData = [
            ...$validated,
            'email' => $validated['email'] ?? '',
            'lastName' => $validated['lastName'] ?? '',
            'address' => $validated['address'] ?? '',
            'contactMethod' => $validated['contactMethod'] ?? 'Phone Call',
            'queries' => $message,
            ...$trackingContext,
            'course_id' => $course?->id,
            'course_slug' => $course?->slug,
            'course' => $courseName, // Keep for backward compatibility/history
        ];

        $submission = JoinNowQuery::create($submissionData);

        try {
            AnalyticsEvent::record('course_help_submit', [
                ...$trackingContext,
                'cta_label' => 'Ask for Course Help',
                'device_type' => 'server',
                'metadata' => [
                    'lead_score' => (string) $leadScore,
                    'lead_status' => $trackingContext['lead_status'],
                    'submission_id' => (string) $submission->id,
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);
        }

        Alert::success('Thank you!', SiteSetting::getValue('enroll_success_message', 'Thank you! We received your inquiry. Our team will contact you soon.'));

        $adminEmail = SiteSetting::getValue('site_email', config('mail.from.address', 'contact@goldeneye.edu.np'));

        try {
            Mail::to($adminEmail)->queue(new ContactMail([
                'dataType' => 'joinNow',
                'subject' => $needsCourseGuidance ? 'New Course Help Request' : 'New Enrollment for '.$courseName,
                ...$submissionData,
                'course' => $courseName,
            ]));
        } catch (\Exception $e) {
            logger()->error('Enrollment Mail failure for '.($validated['email'] ?? 'no-email')." (Course: {$courseName}): ".$e->getMessage());
        }

        return back()->with('success', SiteSetting::getValue('enroll_success_message', 'Thank you! We received your inquiry. Our team will contact you soon.'));
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function calculateJoinNowLeadScore(array $validated, ?Course $course): int
    {
        $score = 0;
        $helpTopic = (string) ($validated['help_topic'] ?? '');
        $audienceType = (string) ($validated['audience_type'] ?? '');
        $message = trim((string) (($validated['goal'] ?? '').' '.($validated['queries'] ?? '')));

        if ($course || (($validated['course'] ?? 'undecided') !== 'undecided')) {
            $score += 5;
        }

        if (! empty($validated['phone'])) {
            $score += 5;
        }

        if (! empty($validated['preferred_batch_time'])) {
            $score += 4;
        }

        if ($helpTopic === 'Fees and timing' || str_contains(strtolower($message), 'fee') || str_contains(strtolower($message), 'timing')) {
            $score += 4;
        }

        if ($helpTopic === 'Parent inquiry' || str_contains(strtolower($audienceType), 'parent')) {
            $score += 3;
        }

        if (in_array($helpTopic, ['IELTS / PTE', 'Japanese / Korean'], true) || str_contains(strtolower($audienceType), 'study')) {
            $score += 3;
        }

        if (mb_strlen($message) > 30) {
            $score += 2;
        }

        return $score;
    }

    private function leadStatusForScore(int $score): string
    {
        return match (true) {
            $score >= 15 => 'Hot',
            $score >= 8 => 'Warm',
            default => 'Basic',
        };
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function inferAudienceType(array $validated): string
    {
        $sourceSection = strtolower((string) ($validated['source_section'] ?? ''));
        $helpTopic = (string) ($validated['help_topic'] ?? '');

        return match (true) {
            str_contains($sourceSection, 'parent') || $helpTopic === 'Parent inquiry' => 'parent',
            str_contains($sourceSection, 'study') || in_array($helpTopic, ['IELTS / PTE', 'Japanese / Korean'], true) => 'study_abroad_applicant',
            str_contains($sourceSection, 'job') || in_array($helpTopic, ['Computer skills', 'Web development'], true) => 'job_skill_learner',
            default => 'student',
        };
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function inferInquiryIntent(array $validated): string
    {
        $helpTopic = (string) ($validated['help_topic'] ?? '');

        return match ($helpTopic) {
            'Fees and timing' => 'fees_and_timing',
            'Parent inquiry' => 'parent_inquiry',
            'IELTS / PTE' => 'study_abroad_test_prep',
            'Japanese / Korean' => 'language_course',
            'Computer skills' => 'computer_skills',
            'Web development' => 'web_development',
            default => $validated['inquiry_intent'] ?? 'course_guidance',
        };
    }

    /**
     * Verify reCAPTCHA with Google API
     */
    private function verifyRecaptcha(?string $response): bool
    {
        Recaptcha::reportProductionMisconfiguration();

        $secret = Recaptcha::secretKey();
        if (! $secret) {
            return ! Recaptcha::hasConfiguredKey() && ! app()->isProduction();
        }

        try {
            $verificationResponse = Http::asForm()
                ->timeout(5)
                ->connectTimeout(2)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => request()->ip(),
                ]);

            return (bool) data_get($verificationResponse->json(), 'success', false);
        } catch (\Exception $e) {
            logger()->error('reCAPTCHA Verification Error: '.$e->getMessage());

            return false;
        }
    }
}

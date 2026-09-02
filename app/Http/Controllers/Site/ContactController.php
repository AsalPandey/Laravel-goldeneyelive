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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

        if ($recaptchaResponse && ! Recaptcha::verify($recaptchaResponse, $request->ip())) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Security verification failed or expired. Please complete it again.'])
                ->withInput();
        }

        $validated['lead_source'] = $validated['lead_source'] ?? 'website';
        $validated['landing_page'] = $validated['landing_page'] ?? Str::limit(url()->previous(), 500, '');

        $contact = Contact::create($validated);

        Alert::success('Success', SiteSetting::getValue('contact_success_message', 'We appreciate your feedback. Our team will contact you soon.'));

        // Use database setting for admin email, fallback to config
        $adminEmail = SiteSetting::getValue('site_email', config('mail.from.address', 'contact@goldeneye.edu.np'));

        try {
            Mail::to($adminEmail)->queue(new ContactMail([
                'dataType' => 'contactMail',
                ...$validated,
            ]));
        } catch (\Throwable $exception) {
            Log::warning('Inquiry notification could not be queued.', [
                'inquiry_type' => 'contact',
                'inquiry_id' => $contact->id,
                'exception' => $exception::class,
            ]);
        }

        return back();
    }

    /**
     * Newsletter Subscription
     */
    public function newsletter(NewsletterRequest $request)
    {
        $validated = $request->validated();

        $recaptchaResponse = $validated['g-recaptcha-response'] ?? null;

        if ($recaptchaResponse && ! Recaptcha::verify($recaptchaResponse, $request->ip())) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Security verification failed or expired. Please complete it again.'], 'newsletter')
                ->with('newsletter_validation_errors', [
                    'g-recaptcha-response' => ['Security verification failed or expired. Please complete it again.'],
                ])
                ->withInput();
        }

        $subscriber = NewsLetter::query()->createOrFirst([
            'email' => $validated['email'],
        ]);

        if (! $subscriber->wasRecentlyCreated) {
            Alert::info('Already Subscribed', 'This email is already part of our newsletter list.');

            return back();
        }

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

        if (($validated['g-recaptcha-response'] ?? null) && ! Recaptcha::verify($validated['g-recaptcha-response'], $request->ip())) {
            return back()
                ->withErrors(['g-recaptcha-response' => 'Security verification failed or expired. Please complete it again.'])
                ->withInput();
        }

        $selectedCourseContext = $validated['course'];
        $needsCourseGuidance = $validated['course'] === 'undecided';
        $course = $needsCourseGuidance ? null : Course::publiclyVisible()->where('slug', $validated['course'])->first();

        if (! $needsCourseGuidance && ! $course) {
            return back()->withErrors(['course' => 'The selected course is currently not accepting enrollments.'])->withInput();
        }

        $courseName = $course?->name ?? 'Need help choosing a program';

        $leadScore = $this->calculateJoinNowLeadScore($validated, $course);

        $trackingContext = [
            'lead_source' => $validated['lead_source'] ?? $validated['source_section'] ?? 'website',
            'landing_page' => $validated['landing_page'] ?? $validated['source_page'] ?? Str::limit(url()->previous(), 500, ''),
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
        } catch (\Throwable $exception) {
            Log::warning('Inquiry notification could not be queued.', [
                'inquiry_type' => 'course_help',
                'inquiry_id' => $submission->id,
                'exception' => $exception::class,
            ]);
        }

        return back();
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
}

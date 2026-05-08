<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\ContactRequest;
use App\Http\Requests\Site\JoinNowRequest;
use App\Http\Requests\Site\NewsletterRequest;
use App\Mail\ContactMail;
use App\Models\Contact;
use App\Models\Course;
use App\Models\JoinNowQuery;
use App\Models\NewsLetter;
use App\Models\SiteSetting;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
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

        $needsCourseGuidance = $validated['course'] === 'undecided';
        $course = $needsCourseGuidance ? null : Course::publiclyVisible()->where('slug', $validated['course'])->first();

        if (! $needsCourseGuidance && ! $course) {
            return back()->withErrors(['course' => 'The selected course is currently not accepting enrollments.'])->withInput();
        }

        $courseName = $course?->name ?? 'Need help choosing a program';

        // Prevent Duplicate Enrollments (last 24h)
        $existing = JoinNowQuery::where('email', $validated['email'])
            ->where('course', $courseName)
            ->where('created_at', '>=', now()->subDay())
            ->first();

        if ($existing) {
            Alert::info('Request Received', 'We have already received your course help request. Our team is processing it.');

            return back();
        }

        $submissionData = [
            ...$validated,
            'address' => $validated['address'] ?? '',
            'contactMethod' => $validated['contactMethod'] ?? 'Phone Call',
            'queries' => $validated['queries'] ?? '',
            'lead_source' => $validated['lead_source'] ?? 'website',
            'landing_page' => $validated['landing_page'] ?? url()->previous(),
            'cta_id' => $validated['cta_id'] ?? null,
            'course_id' => $course?->id,
            'course_slug' => $course?->slug,
            'course' => $courseName, // Keep for backward compatibility/history
        ];

        JoinNowQuery::create($submissionData);

        Alert::success('Success', SiteSetting::getValue('enroll_success_message', 'Your application has been received. We will get back to you shortly.'));

        $adminEmail = SiteSetting::getValue('site_email', config('mail.from.address', 'contact@goldeneye.edu.np'));

        try {
            Mail::to($adminEmail)->queue(new ContactMail([
                'dataType' => 'joinNow',
                'subject' => $needsCourseGuidance ? 'New Course Help Request' : 'New Enrollment for '.$courseName,
                ...$submissionData,
                'course' => $courseName,
            ]));
        } catch (\Exception $e) {
            logger()->error("Enrollment Mail failure for {$validated['email']} (Course: {$courseName}): ".$e->getMessage());
        }

        return back();
    }

    /**
     * Verify reCAPTCHA with Google API
     */
    private function verifyRecaptcha($response): bool
    {
        $secret = SiteSetting::getValue('recaptcha_secret_key');
        if (! $secret) {
            return true;
        }

        try {
            $client = new Client(['timeout' => 5.0, 'connect_timeout' => 2.0]);
            $res = $client->post('https://www.google.com/recaptcha/api/siteverify', [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $response,
                    'remoteip' => request()->ip(),
                ],
            ]);

            $body = json_decode((string) $res->getBody());

            return $body->success;
        } catch (\Exception $e) {
            logger()->error('reCAPTCHA Verification Error: '.$e->getMessage());

            return false;
        }
    }
}

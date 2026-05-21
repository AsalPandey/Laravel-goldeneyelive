<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsEvent;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class AnalyticsEventController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $trackingDisabled = SiteSetting::getValue('analytics_tracking_enabled', 'active') === 'disabled';
        } catch (Throwable) {
            $trackingDisabled = true;
        }

        if ($trackingDisabled) {
            return response()->json(['ok' => true, 'tracked' => false], 202);
        }

        $validator = Validator::make($request->all(), [
            'event_name' => ['required', 'string', Rule::in(AnalyticsEvent::ALLOWED_EVENTS)],
            'source_page' => ['nullable', 'string', 'max:500'],
            'source_section' => ['nullable', 'string', 'max:255'],
            'cta_label' => ['nullable', 'string', 'max:255'],
            'selected_course' => ['nullable', 'string', 'max:255'],
            'audience_type' => ['nullable', 'string', 'max:255'],
            'inquiry_intent' => ['nullable', 'string', 'max:255'],
            'device_type' => ['nullable', 'string', 'max:32'],
            'timestamp' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
            'metadata.*' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'tracked' => false], 202);
        }

        try {
            $event = AnalyticsEvent::record(
                (string) $validator->validated()['event_name'],
                $validator->validated(),
            );
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['ok' => true, 'tracked' => false], 202);
        }

        return response()->json(['ok' => true, 'tracked' => $event !== null], 202);
    }
}

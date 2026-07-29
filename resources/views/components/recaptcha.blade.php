@props([
    'errorBag' => 'default',
    'size' => null,
    'theme' => null,
])

@php
    $recaptchaSiteKey = \App\Support\Recaptcha::enabled()
        ? \App\Support\Recaptcha::siteKey()
        : null;
    $recaptchaError = isset($errors)
        ? $errors->getBag($errorBag)->first('g-recaptcha-response')
        : null;
@endphp

@if($recaptchaSiteKey)
    <div {{ $attributes->class(['recaptcha-field']) }}>
        <div
            class="g-recaptcha"
            data-sitekey="{{ $recaptchaSiteKey }}"
            @if($size) data-size="{{ $size }}" @endif
            @if($theme) data-theme="{{ $theme }}" @endif
        ></div>
        @if($recaptchaError)
            <div class="text-danger small mt-2" role="alert">{{ $recaptchaError }}</div>
        @endif
    </div>
@endif

<x-layouts::auth :title="__('Verify code')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Verify code')" :description="__('Enter the six-digit code from your email. It expires in 10 minutes.')" />
        <x-auth-session-status class="text-center" :status="session('status')" />
        <form method="POST" action="{{ route('password.otp.verify') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="otp" :label="__('Verification code')" type="text" inputmode="numeric" pattern="[0-9]{6}" minlength="6" maxlength="6" autocomplete="one-time-code" required autofocus />
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Verify code') }}</flux:button>
        </form>
        <flux:link :href="route('password.request')">{{ __('Request a new code') }}</flux:link>
    </div>
</x-layouts::auth>

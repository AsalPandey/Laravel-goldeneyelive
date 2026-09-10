<x-layouts::auth :title="__('Forgot password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Forgot password')" :description="__('Enter your organization email to receive a six-digit verification code.')" />
        <x-auth-session-status class="text-center" :status="session('status')" />
        <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="email" :label="__('Email address')" type="email" :value="old('email')" required autofocus autocomplete="email" />
            <flux:button variant="primary" type="submit" class="w-full">{{ __('Send verification code') }}</flux:button>
        </form>
        <flux:link :href="route('login')" wire:navigate>{{ __('Back to log in') }}</flux:link>
    </div>
</x-layouts::auth>

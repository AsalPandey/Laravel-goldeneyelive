<x-layouts::auth :title="__('Reset password')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Reset password')" :description="__('Please enter your new password below.')" />
        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
            @csrf
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password" viewable />
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required autocomplete="new-password" viewable />
            <flux:button type="submit" variant="primary" class="w-full">{{ __('Reset password') }}</flux:button>
        </form>
    </div>
</x-layouts::auth>

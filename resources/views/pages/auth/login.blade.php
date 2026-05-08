<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-8">
        <div class="flex flex-col gap-2 text-center">
            <h2 class="text-3xl font-heading font-black text-white uppercase tracking-tight">Admin <span class="text-brand-gold">Login</span></h2>
            <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">Institutional Management Portal</p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email Address')"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="admin@goldeneye.edu.np"
            />

            <!-- Password -->
            <div class="relative">
                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="current-password"
                    :placeholder="__('Your password')"
                    viewable
                />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-[10px] font-black uppercase text-brand-gold/60 hover:text-brand-gold transition-colors end-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot?') }}
                    </flux:link>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <flux:checkbox name="remember" :label="__('Remember me')" class="text-xs font-bold text-zinc-400" />
            </div>

            <flux:button type="submit" variant="primary" class="w-full py-4 rounded-2xl text-xs font-black uppercase tracking-[0.2em] shadow-xl" data-test="login-button">
                {{ __('Sign In') }}
            </flux:button>
        </form>

        @if (Route::has('register'))
            <div class="pt-4 border-t border-white/5 text-[10px] font-bold text-center uppercase tracking-widest text-zinc-500">
                <span>{{ __('New Account?') }}</span>
                <flux:link :href="route('register')" class="text-brand-gold hover:underline ml-1" wire:navigate>{{ __('Register') }}</flux:link>
            </div>
        @endif
    </div>
</x-layouts::auth>

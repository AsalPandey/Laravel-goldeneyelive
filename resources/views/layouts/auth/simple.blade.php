<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen antialiased bg-brand-dark overflow-x-hidden">
        {{-- Premium Background with Institutional Overlay --}}
        <div class="fixed inset-0 z-0">
            <div class="absolute inset-0 bg-brand-dark/80 backdrop-blur-sm z-10"></div>
            <img src="{{ asset($settings['hero_image'] ?? 'site/img/carousel-1.png') }}" class="w-full h-full object-cover opacity-30 grayscale" alt="Background">
        </div>

        {{-- Animated background elements --}}
        <div class="fixed inset-0 overflow-hidden pointer-events-none z-20">
            <div class="absolute top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-brand-gold/5 blur-[150px] animate-pulse"></div>
            <div class="absolute bottom-[20%] -right-[10%] w-[40%] h-[40%] rounded-full bg-brand-gold/10 blur-[120px] animate-pulse" style="animation-delay: 3s;"></div>
        </div>

        <div class="relative z-30 flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-md flex-col gap-10">
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-5 group transition-all" wire:navigate>
                    <div class="flex h-24 w-24 items-center justify-center rounded-3xl bg-brand-gold/10 border border-brand-gold/30 shadow-2xl group-hover:scale-110 transition-all duration-700 overflow-hidden relative">
                        <div class="absolute inset-0 bg-linear-to-tr from-brand-gold/30 to-transparent"></div>
                        <img src="{{ asset($settings['site_logo'] ?? 'site/img/logo.png') }}" class="h-14 w-auto relative z-10 object-contain" alt="Logo">
                    </div>
                    <div class="text-center">
                        <h1 class="text-3xl font-heading font-black tracking-tighter text-white uppercase">{{ $settings['site_name'] ?? 'GOLDENEYE' }}</h1>
                        <p class="text-[11px] tracking-[0.5em] font-black text-brand-gold uppercase -mt-1">{{ $settings['site_name_suffix'] ?? 'ACADEMY' }}</p>
                    </div>
                </a>

                <div class="bg-brand-dark/40 backdrop-blur-2xl rounded-[3rem] p-10 md:p-12 shadow-3xl border border-brand-gold/10 relative overflow-hidden group">
                    {{-- Subtle inner glow --}}
                    <div class="absolute -top-32 -left-32 w-64 h-64 bg-brand-gold/10 rounded-full blur-[100px] group-hover:bg-brand-gold/15 transition-all duration-1000"></div>
                    
                    <div class="relative z-10 flex flex-col gap-8">
                        {{ $slot }}
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-xs text-zinc-500 font-medium tracking-wide">© {{ date('Y') }} {{ $settings['site_name'] ?? 'GoldenEye' }} Academy. All rights reserved.</p>
                </div>
            </div>
        </div>
        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

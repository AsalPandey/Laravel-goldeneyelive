<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Platform')" class="grid">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @role('Admin|Staff')
                <flux:sidebar.group :heading="__('Academy Management')" class="grid">
                    <flux:sidebar.item icon="book-open" :href="route('admin.courses.index')" :current="request()->routeIs('admin.courses.*')" wire:navigate>
                        {{ __('Manage Courses') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="squares-plus" :href="route('admin.categories.index')" :current="request()->routeIs('admin.categories.*')" wire:navigate>
                        {{ __('Manage Categories') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('admin.service-pillars.index')" :current="request()->routeIs('admin.service-pillars.*')" wire:navigate>
                        {{ __('Service Pillars') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="squares-2x2" :href="route('admin.blog.index')" :current="request()->routeIs('admin.blog.*')" wire:navigate>
                        {{ __('Manage Blog') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-bottom-center-text" :href="route('admin.faq.index')" :current="request()->routeIs('admin.faq.*')" wire:navigate>
                        {{ __('Manage FAQs') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('admin.branding.index')" :current="request()->routeIs('admin.branding.*')" wire:navigate>
                        {{ __('Branding Center') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="globe-alt" :href="route('admin.seo.index')" :current="request()->routeIs('admin.seo.*')" wire:navigate>
                        {{ __('SEO & AI Authority') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="users" :href="route('admin.teachers.index')" :current="request()->routeIs('admin.teachers.*')" wire:navigate>
                        {{ __('Manage Teachers') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('admin.testimonials.index')" :current="request()->routeIs('admin.testimonials.*')" wire:navigate>
                        {{ __('Testimonials') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="megaphone" :href="route('admin.notices.index')" :current="request()->routeIs('admin.notices.*')" wire:navigate>
                        {{ __('Global Notices') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="envelope" :href="route('admin.submissions.contact-display')" :current="request()->routeIs('admin.submissions.contact-display')" wire:navigate>
                        {{ __('Contact Inquiries') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-plus" :href="route('admin.submissions.join_now-display')" :current="request()->routeIs('admin.submissions.join_now-display')" wire:navigate>
                        {{ __('Enrollment Queries') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="envelope-open" :href="route('admin.submissions.newsletter-display')" :current="request()->routeIs('admin.submissions.newsletter-display')" wire:navigate>
                        {{ __('Newsletter List') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
                @endrole
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="home" href="{{ route('home') }}" target="_blank">
                    {{ __('Visit Website') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        <flux:main>
            {{ $slot }}
        </flux:main>

        @include('sweetalert::alert')

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
        
        {{-- Global Media Vault Picker Modal --}}
        <div id="mediaVaultModal" class="hidden fixed inset-0 z-[9999] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white dark:bg-zinc-900 rounded-3xl w-full max-w-5xl h-[80vh] flex flex-col shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700">
                <div class="px-8 py-6 border-b border-zinc-100 dark:border-zinc-800 flex justify-between items-center bg-zinc-50/50 dark:bg-zinc-900/50">
                    <div>
                        <h3 class="text-xl font-black uppercase tracking-tight text-neutral-900 dark:text-white">Media Vault <span class="text-brand-gold">Library</span></h3>
                        <p class="text-xs text-neutral-500 dark:text-neutral-400 font-medium">Select an existing branding asset to assign to your content.</p>
                    </div>
                    <button type="button" onclick="closeMediaVault()" class="p-2 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-neutral-400 hover:text-brand-gold transition-all">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-8">
                    <div id="vaultLoading" class="flex flex-col items-center justify-center h-full py-12 gap-4">
                        <div class="w-12 h-12 border-4 border-brand-gold/20 border-t-brand-gold rounded-full animate-spin"></div>
                        <p class="text-xs font-black uppercase tracking-widest text-neutral-400">Inventorying Assets...</p>
                    </div>
                    <div id="vaultGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 hidden">
                        {{-- Populated via JS --}}
                    </div>
                </div>
                <div class="px-8 py-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex justify-between items-center">
                    <p class="text-[10px] text-neutral-400 font-bold italic">Note: Only images in site/img/ are managed here.</p>
                    <a href="{{ route('admin.branding.index') }}" target="_blank" class="text-xs font-black uppercase text-brand-gold hover:underline">Manage Vault Files</a>
                </div>
            </div>
        </div>

        <script>
            window.GoldenEyeMediaVault = window.GoldenEyeMediaVault || {
                currentPickerTarget: null,
                currentPreviewTarget: null,
            };

            window.openMediaVault = function (targetId, previewId = null) {
                window.GoldenEyeMediaVault.currentPickerTarget = document.querySelector(`input[name="${targetId}"]`);
                window.GoldenEyeMediaVault.currentPreviewTarget = previewId ? document.getElementById(previewId) : null;
                
                const modal = document.getElementById('mediaVaultModal');
                modal.classList.remove('hidden');
                
                fetchVaultAssets();
            };

            window.closeMediaVault = function () {
                document.getElementById('mediaVaultModal').classList.add('hidden');
            };

            window.copyPathToClipboard = function (path, btn) {
                navigator.clipboard.writeText(path).then(() => {
                    const original = btn.innerHTML;
                    btn.innerHTML = '<i class="fa fa-check text-green-500"></i>';
                    setTimeout(() => { btn.innerHTML = original; }, 2000);
                });
            };

            window.fetchVaultAssets = async function () {
                const grid = document.getElementById('vaultGrid');
                const loading = document.getElementById('vaultLoading');
                
                grid.classList.add('hidden');
                loading.classList.remove('hidden');

                try {
                    const response = await fetch('{{ route("admin.branding.index") }}?json=true');
                    const data = await response.json();
                    
                    grid.innerHTML = '';
                    data.images.forEach(img => {
                        const card = document.createElement('div');
                        card.className = 'group cursor-pointer rounded-2xl border border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-800 overflow-hidden hover:shadow-xl hover:border-brand-gold/40 transition-all';
                        card.onclick = () => selectVaultAsset(img.path);
                        const assetBase = "{{ asset('') }}";
                        const fullPath = assetBase + img.path;
                        
                        card.innerHTML = `
                            <div class="aspect-square bg-zinc-50 dark:bg-zinc-900 overflow-hidden relative">
                                <img src="${fullPath}" class="w-full h-full object-cover transition-transform group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex flex-col items-center justify-center p-3 gap-2">
                                    <button type="button" onclick="selectVaultAsset('${img.path}')" class="w-full py-2 bg-emerald-500 text-white text-[9px] font-black uppercase rounded-lg hover:bg-emerald-600 shadow-lg">Select File</button>
                                    <button type="button" onclick="event.stopPropagation(); copyPathToClipboard('${img.path}', this)" class="w-full py-2 bg-white/10 text-white text-[9px] font-black uppercase rounded-lg hover:bg-white/20 border border-white/20">Copy Path</button>
                                </div>
                            </div>
                            <div class="p-3">
                                <p class="text-[10px] font-black uppercase truncate text-neutral-700 dark:text-neutral-300">${img.name}</p>
                                <p class="text-[8px] text-neutral-400 font-bold">${img.size}</p>
                            </div>
                        `;
                        grid.appendChild(card);
                    });
                    
                    loading.classList.add('hidden');
                    grid.classList.remove('hidden');
                } catch (error) {
                    grid.innerHTML = `<p class="col-span-full py-12 text-center text-red-500 font-bold">Failed to load assets. Ensure you are logged in.</p>`;
                    loading.classList.add('hidden');
                    grid.classList.remove('hidden');
                }
            };

            window.selectVaultAsset = function (path) {
                if (window.GoldenEyeMediaVault.currentPickerTarget) {
                    window.GoldenEyeMediaVault.currentPickerTarget.value = path;
                }
                if (window.GoldenEyeMediaVault.currentPreviewTarget) {
                    const baseUrl = "{{ asset('') }}".replace(/\/$/, '');
                    window.GoldenEyeMediaVault.currentPreviewTarget.src = baseUrl + '/' + path.replace(/^\//, '');
                }
                closeMediaVault();
            };

            // Quick Status Toggle Helper
            window.toggleStatus = async function (id, model, element) {
                const badge = element.querySelector('span');
                const originalText = badge.innerText;
                badge.innerText = 'WAIT...';
                
                try {
                    // Logic for toggle status would go here - for now we use simple forms in the UI
                } catch(e) {}
            };
        </script>
    </body>
</html>

<x-layouts::app :title="__('Notice Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-6 border-b border-zinc-100 gap-4">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-brand-dark tracking-tight uppercase">Global <span class="text-brand-gold">Notices</span></h1>
                <p class="text-neutral-500 text-sm">Manage institutional announcements and conversion-focused popups.</p>
            </div>
            <div class="flex flex-col sm:flex-row items-center gap-4">
                <form action="{{ route('admin.notices.index') }}" method="GET" class="relative w-full sm:w-auto">
                    <label for="notice-search" class="sr-only">Search notices</label>
                    <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400 text-xs"></i>
                    <input id="notice-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search notices..." class="pl-10 pr-4 py-2.5 rounded-xl border-zinc-200 bg-white text-xs w-full sm:w-64 focus:border-brand-gold focus:ring-0">
                </form>
                <a href="{{ route('admin.notices.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-gold text-brand-dark px-6 py-3 text-sm font-black uppercase shadow-lg hover:bg-brand-dark hover:text-brand-gold transition-all w-full sm:w-auto justify-center">
                    <i class="fa fa-plus-circle"></i> Create New
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
            Notices can appear as either a popup-style notice or a top announcement bar. While active and in schedule, a popup-style Notice temporarily takes precedence over the separate Main Campaign Popup under Website Content → Marketing Tools. Scheduled times below use Nepal time.
        </div>

        <div class="hidden overflow-x-auto rounded-3xl border border-zinc-100 bg-white shadow-xl md:block">
            <table class="min-w-full divide-y divide-zinc-200">
                <thead class="bg-zinc-50/50">
                    <tr>
                        <th scope="col" class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Notice Detail</th>
                        <th scope="col" class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Destination</th>
                        <th scope="col" class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Status</th>
                        <th scope="col" class="px-8 py-5 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400 text-center">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 bg-white">
                    @forelse($notices as $notice)
                    <tr class="hover:bg-zinc-50/50 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-5">
                                <div class="h-16 w-24 flex-shrink-0 bg-zinc-100 rounded-2xl overflow-hidden border-2 border-white shadow-md">
                                    <img class="h-full w-full object-cover transition-transform group-hover:scale-110" src="{{ \App\Support\PublicAsset::url($notice->image, 'site/img/carousel-1.png') }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="{{ $notice->title }}">
                                </div>
                                <div class="space-y-1.5">
                                    <div class="text-sm font-black text-brand-dark leading-tight flex items-center gap-2">
                                        {{ $notice->title }}
                                        @if($notice->is_urgent) <span class="bg-red-500 w-2 h-2 rounded-full animate-pulse shadow-red-500/50 shadow-sm" title="Urgent"></span> @endif
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="text-[8px] font-black text-white uppercase tracking-widest bg-brand-dark px-2 py-0.5 rounded-full">{{ $notice->badge ?? 'ANNOUNCEMENT' }}</div>
                                        <div class="text-[8px] font-black text-brand-gold uppercase tracking-widest bg-brand-gold/10 px-2 py-0.5 rounded-full border border-brand-gold/20">{{ $notice->display_type ?? 'popup' }}</div>
                                    </div>
                                    @if($notice->starts_at || $notice->expires_at)
                                        <div class="text-[9px] text-neutral-500">
                                            {{ $notice->starts_at ? 'Starts '.\App\Support\CmsDateTime::forStaffDisplay($notice->starts_at) : 'Starts immediately' }}
                                            ·
                                            {{ $notice->expires_at ? 'Ends '.\App\Support\CmsDateTime::forStaffDisplay($notice->expires_at) : 'No end date' }}
                                            (Nepal time)
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="text-[10px] font-bold text-neutral-500 bg-zinc-50 px-3 py-1.5 rounded-xl border border-zinc-100 truncate max-w-[180px]" title="{{ $notice->link ?? 'Default Page' }}">
                                <i class="fas fa-link text-brand-gold me-2"></i> {{ $notice->link ?? 'Join Now flow' }}
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            @php
                                $publicationState = $notice->publicationState();
                                $stateClasses = match ($publicationState) {
                                    'live' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'scheduled' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'expired' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-rose-50 text-rose-700 border-rose-100',
                                };
                                $dotClasses = match ($publicationState) {
                                    'live' => 'bg-emerald-500',
                                    'scheduled' => 'bg-blue-500',
                                    'expired' => 'bg-amber-500',
                                    default => 'bg-rose-500',
                                };
                            @endphp
                            @role('Admin')
                            <form action="{{ route('admin.notices.toggle', $notice->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" aria-label="{{ $notice->status === 'active' ? 'Deactivate' : 'Activate' }} {{ $notice->title }}" class="inline-flex items-center rounded-full border-2 {{ $stateClasses }} px-4 py-1 text-[9px] font-black uppercase tracking-widest transition-all shadow-sm hover:brightness-95">
                                    <span class="w-2 h-2 rounded-full {{ $dotClasses }} me-2"></span>
                                    {{ $publicationState }}
                                </button>
                            </form>
                            @else
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-bold {{ $stateClasses }}">{{ ucfirst($publicationState) }}</span>
                            @endrole
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex justify-center gap-3">
                                @role('Admin')
                                <a href="{{ route('admin.notices.edit', $notice->id) }}" aria-label="Edit {{ $notice->title }}" title="Edit notice" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-100 text-neutral-600 hover:bg-brand-gold hover:text-brand-dark hover:shadow-lg transition-all border border-zinc-200 hover:border-brand-gold">
                                    <i class="fa fa-pencil-alt text-xs" aria-hidden="true"></i>
                                </a>
                                @endrole
                                @role('Admin')
                                <form action="{{ route('admin.notices.destroy', $notice->id) }}" method="POST" onsubmit="return confirm('Permanently delete this notice? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" aria-label="Permanently delete {{ $notice->title }}" title="Delete notice" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-zinc-100 text-neutral-600 hover:bg-rose-500 hover:text-white hover:shadow-lg transition-all border border-zinc-200 hover:border-rose-500">
                                        <i class="fa fa-trash-alt text-xs" aria-hidden="true"></i>
                                    </button>
                                </form>
                                @endrole
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-24 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-20 h-20 bg-zinc-50 rounded-full flex items-center justify-center border border-zinc-100">
                                    <i class="fas fa-bullhorn text-4xl text-zinc-200"></i>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-brand-dark font-black uppercase tracking-widest text-xs">Silence is Golden</p>
                                    <p class="text-neutral-400 text-[10px] font-medium">No global notices are currently scheduled.</p>
                                </div>
                                <a href="{{ route('admin.notices.create') }}" class="text-[10px] font-black uppercase text-brand-gold hover:underline mt-2">Post Your First Announcement</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="grid gap-4 md:hidden">
            @forelse($notices as $notice)
                @php
                    $publicationState = $notice->publicationState();
                    $stateClasses = match ($publicationState) {
                        'live' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                        'scheduled' => 'bg-blue-50 text-blue-700 border-blue-100',
                        'expired' => 'bg-amber-50 text-amber-700 border-amber-100',
                        default => 'bg-rose-50 text-rose-700 border-rose-100',
                    };
                @endphp
                <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm">
                    <div class="flex gap-4">
                        <img class="h-20 w-24 shrink-0 rounded-xl object-cover" src="{{ \App\Support\PublicAsset::url($notice->image, 'site/img/carousel-1.png') }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" alt="">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <h2 class="font-black text-brand-dark">{{ $notice->title }}</h2>
                                <span class="rounded-full border px-2.5 py-1 text-[9px] font-black uppercase tracking-wider {{ $stateClasses }}">{{ $publicationState }}</span>
                            </div>
                            <p class="mt-2 truncate text-xs text-neutral-500">{{ $notice->link ?: 'Join Now flow' }}</p>
                            @if($notice->starts_at || $notice->expires_at)
                                <p class="mt-2 text-[10px] text-neutral-500">
                                    {{ $notice->starts_at ? 'Starts '.\App\Support\CmsDateTime::forStaffDisplay($notice->starts_at) : 'Starts immediately' }} ·
                                    {{ $notice->expires_at ? 'Ends '.\App\Support\CmsDateTime::forStaffDisplay($notice->expires_at) : 'No end date' }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @role('Admin')
                        <div class="mt-4 grid grid-cols-3 gap-2 border-t border-zinc-100 pt-4">
                            <form action="{{ route('admin.notices.toggle', $notice) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="w-full rounded-xl border border-zinc-200 px-3 py-2 text-xs font-bold" aria-label="{{ $notice->status === 'active' ? 'Deactivate' : 'Activate' }} {{ $notice->title }}">
                                    {{ $notice->status === 'active' ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.notices.edit', $notice) }}" class="rounded-xl border border-zinc-200 px-3 py-2 text-center text-xs font-bold" aria-label="Edit {{ $notice->title }}">Edit</a>
                            <form action="{{ route('admin.notices.destroy', $notice) }}" method="POST" onsubmit="return confirm('Permanently delete this notice? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-full rounded-xl border border-rose-200 px-3 py-2 text-xs font-bold text-rose-700" aria-label="Permanently delete {{ $notice->title }}">Delete</button>
                            </form>
                        </div>
                    @endrole
                </article>
            @empty
                <div class="rounded-2xl border border-zinc-200 bg-white p-8 text-center text-sm text-neutral-500">No global notices are currently scheduled.</div>
            @endforelse
        </div>
        <div class="mt-6">
            {{ $notices->links() }}
        </div>
    </div>
</x-layouts::app>

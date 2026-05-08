<x-layouts::app :title="__('Knowledge Base Management')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Knowledge <span class="text-brand-gold">Base</span></h1>
                <p class="text-neutral-500 text-sm">Manage academy frequently asked questions and support content.</p>
            </div>
            <a href="{{ route('admin.faq.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-dark text-brand-gold px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-brand-gold hover:text-brand-dark transition-all">
                <i class="fa fa-plus-circle"></i> New FAQ Entry
            </a>
        </div>

        <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-neutral-100 shadow-sm">
            <div class="relative flex-1 max-w-md w-full">
                <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-neutral-400"></i>
                <form action="{{ route('admin.faq.index') }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search questions or answers..." 
                           class="w-full pl-12 pr-4 py-3 bg-neutral-50 border-neutral-100 rounded-xl text-sm focus:ring-0 focus:border-brand-gold transition-all">
                </form>
            </div>
            <div class="flex items-center gap-4 text-[11px] font-black uppercase text-neutral-400 tracking-widest">
                <div class="flex items-center gap-2">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span> Active: {{ \App\Models\FAQ::where('status', 'active')->count() }}
                </div>
                <div class="flex items-center gap-2 border-l border-neutral-100 pl-4">
                    <span class="h-2 w-2 rounded-full bg-slate-400"></span> Archived: {{ \App\Models\FAQ::where('status', 'inactive')->count() }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Question / Inquiry</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Priority</th>
                        <th scope="col" class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Live Status</th>
                        <th scope="col" class="relative px-6 py-4"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-neutral-50/30 transition-all">
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 rounded-xl bg-brand-gold/10 text-brand-gold flex items-center justify-center flex-shrink-0 border border-brand-gold/10 shadow-sm">
                                    <i class="fa fa-question text-xs"></i>
                                </div>
                                <div class="space-y-1">
                                    <div class="text-sm font-black text-neutral-900 leading-tight">{{ $faq->question }}</div>
                                    <div class="text-[10px] text-neutral-400 font-medium italic">{{ Str::limit(strip_tags($faq->answer), 80) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wider {{ $faq->order_priority > 0 ? 'bg-blue-50 text-blue-700' : 'bg-neutral-50 text-neutral-500' }}">
                                Weight: {{ $faq->order_priority }}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <form action="{{ route('admin.faq.toggle-status', $faq->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="group transition-all">
                                    @if($faq->status === 'active')
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 text-emerald-700 px-3 py-1 text-[10px] font-black uppercase border border-emerald-100 group-hover:bg-emerald-100">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-50 text-slate-500 px-3 py-1 text-[10px] font-black uppercase border border-slate-100 group-hover:bg-slate-100">
                                            Archived
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.faq.edit', $faq->id) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-neutral-900 hover:border-neutral-900 hover:shadow-lg transition-all">
                                    <i class="fa fa-pen-nib"></i>
                                </a>
                                <form action="{{ route('admin.faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Archive this FAQ permanently?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-eraser"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-24 text-center text-neutral-400 italic">No FAQ entries found matching your search.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faqs->hasPages())
            <div class="px-8 py-4 bg-white rounded-2xl border border-neutral-100 shadow-sm">
                {{ $faqs->links() }}
            </div>
        @endif
    </div>
</x-layouts::app>

<x-layouts::app :title="__('Service Pillars')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Service <span class="text-brand-gold">Pillars</span></h1>
                <p class="text-neutral-500 text-sm">Control the homepage catalogue, positioning, and CTA paths.</p>
            </div>
            <a href="{{ route('admin.service-pillars.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-gold text-brand-dark px-6 py-3 text-sm font-black uppercase shadow-xl hover:bg-brand-dark hover:text-brand-gold transition-all">
                <i class="fa fa-plus-circle"></i> Create Pillar
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-neutral-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-neutral-200">
                <thead class="bg-neutral-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">Pillar</th>
                        <th class="px-6 py-4 text-left text-[10px] font-black uppercase tracking-widest text-neutral-400">CTA</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Featured</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Order</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Status</th>
                        <th class="px-6 py-4 text-center text-[10px] font-black uppercase tracking-widest text-neutral-400">Manage</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-neutral-100">
                    @forelse($servicePillars as $pillar)
                        <tr class="hover:bg-neutral-50/30 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-start gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-brand-gold/10 text-brand-gold flex items-center justify-center border border-brand-gold/10">
                                        <i class="{{ $pillar->icon ?: 'fa fa-star' }}"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-black text-neutral-800 leading-tight">{{ $pillar->title }}</div>
                                        <div class="text-[11px] text-neutral-500 max-w-xl line-clamp-2 mt-1">{{ $pillar->summary }}</div>
                                        <div class="text-[9px] text-neutral-400 font-mono mt-1">{{ $pillar->slug }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-[11px] font-bold text-neutral-500">
                                {{ $pillar->cta_label ?? 'No CTA' }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                <span class="inline-flex rounded-full px-3 py-1 text-[9px] font-black uppercase {{ $pillar->is_featured ? 'bg-brand-gold/10 text-brand-gold' : 'bg-neutral-100 text-neutral-400' }}">
                                    {{ $pillar->is_featured ? 'Featured' : 'Standard' }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center font-mono text-xs text-neutral-500">{{ $pillar->sort_order }}</td>
                            <td class="px-6 py-5 text-center">
                                <form action="{{ route('admin.service-pillars.toggle-status', $pillar) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[10px] font-black tracking-tight uppercase transition-all shadow-sm border {{ $pillar->status === 'active' ? 'bg-emerald-50 text-emerald-700 border-emerald-100 hover:bg-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100 hover:bg-rose-100' }}">
                                        <span class="h-1.5 w-1.5 rounded-circle {{ $pillar->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                        {{ $pillar->status }}
                                    </button>
                                </form>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('admin.service-pillars.edit', $pillar) }}" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-orange-600 hover:border-orange-200 hover:shadow-lg transition-all">
                                        <i class="fa fa-pencil-alt text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.service-pillars.destroy', $pillar) }}" method="POST" onsubmit="return confirm('Delete this service pillar?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl bg-neutral-100 border border-neutral-200 text-neutral-600 hover:text-red-500 hover:border-red-200 hover:shadow-lg transition-all">
                                            <i class="fa fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-24 text-center text-neutral-400 font-medium italic">No service pillars have been created yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $servicePillars->links() }}
        </div>
    </div>
</x-layouts::app>

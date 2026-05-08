@php
    $servicePillar ??= null;
    $bullets = old('bullets', $servicePillar?->bullets ?? ['', '', '']);
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2 space-y-6">
        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Service Positioning</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Title</label>
                    <input type="text" name="title" value="{{ old('title', $servicePillar?->title) }}" required class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold">
                    @error('title') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Icon Class</label>
                    <input type="text" name="icon" value="{{ old('icon', $servicePillar?->icon) }}" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-mono text-[11px]" placeholder="fa fa-laptop-code">
                    @error('icon') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Slug</label>
                <input type="text" name="slug" value="{{ old('slug', $servicePillar?->slug) }}" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-mono text-[11px]" placeholder="auto-generated-from-title">
                @error('slug') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Summary</label>
                <textarea name="summary" rows="3" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all resize-none font-medium">{{ old('summary', $servicePillar?->summary) }}</textarea>
                @error('summary') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Catalogue Bullets</h3>
            @for($i = 0; $i < 5; $i++)
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Bullet {{ $i + 1 }}</label>
                    <textarea name="bullets[]" rows="2" class="w-full px-5 py-3 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all resize-none font-medium">{{ $bullets[$i] ?? '' }}</textarea>
                    @error("bullets.{$i}") <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                </div>
            @endfor
        </div>
    </div>

    <div class="space-y-6">
        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Display Controls</h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Status</label>
                    <select name="status" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold">
                        <option value="active" @selected(old('status', $servicePillar?->status ?? 'active') === 'active')>Active</option>
                        <option value="inactive" @selected(old('status', $servicePillar?->status) === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $servicePillar?->sort_order ?? 0) }}" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold">
                </div>
            </div>
            <label class="flex items-center gap-3 rounded-xl bg-white border border-neutral-100 p-4 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1" class="rounded border-neutral-300 text-brand-gold focus:ring-brand-gold" @checked(old('is_featured', $servicePillar?->is_featured ?? false))>
                <span class="text-xs font-black uppercase tracking-widest text-neutral-600">Feature on Homepage</span>
            </label>
        </div>

        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Conversion CTA</h3>
            <div class="space-y-2">
                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">CTA Label</label>
                <input type="text" name="cta_label" value="{{ old('cta_label', $servicePillar?->cta_label) }}" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold">
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">CTA URL</label>
                <input type="text" name="cta_url" value="{{ old('cta_url', $servicePillar?->cta_url) }}" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-mono text-[11px]">
            </div>
        </div>

        <x-seo-aeo-fields :model="$servicePillar" />
    </div>
</div>

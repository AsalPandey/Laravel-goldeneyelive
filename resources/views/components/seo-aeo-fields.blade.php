@props([
    'model' => null,
    'title' => 'Search and Answer Preview',
    'showTitle' => true,
    'metaTitleMax' => 255,
    'metaDescriptionMax' => 10000,
    'metaKeywordsMax' => 10000,
    'aeoSummaryMax' => 10000,
])

<div class="mt-10 p-3 sm:p-6 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-700 space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-xl bg-zinc-900 text-[#C5A059] flex items-center justify-center">
            <i class="fas fa-magic"></i>
        </div>
        <div>
            <h3 class="text-sm sm:text-lg font-black uppercase text-zinc-800 dark:text-white leading-tight">{{ $title }}</h3>
            <p class="text-[10px] text-zinc-500 font-medium mt-1">Add factual summaries for search results and answer previews. Leave fields empty to use the public fallback.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($showTitle)
            <div class="md:col-span-2">
                <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">Custom SEO Meta Title</label>
                <input type="text" name="meta_title" value="{{ old('meta_title', $model?->meta_title) }}"
                       class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white h-12 px-4 text-sm"
                       maxlength="{{ $metaTitleMax }}"
                       placeholder="Leave empty to use the content title">
                <p class="text-[10px] text-zinc-400 mt-2">Write a clear search title (up to {{ $metaTitleMax }} characters). Avoid repeating Golden Eye Academy; the public fallback adds the brand where needed.</p>
                @error('meta_title') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
            </div>
        @endif

        <div class="md:col-span-2">
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">SEO Meta Description</label>
            <textarea name="meta_description" rows="2" maxlength="{{ $metaDescriptionMax }}"
                      class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white p-4 text-sm">{{ old('meta_description', $model?->meta_description) }}</textarea>
            <p class="text-[10px] text-zinc-400 mt-2">Summarize the visible content accurately in up to {{ $metaDescriptionMax }} characters. Search engines may choose a different snippet.</p>
            @error('meta_description') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">SEO Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $model?->meta_keywords) }}" maxlength="{{ $metaKeywordsMax }}"
                   class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white h-12 px-4 text-sm"
                   placeholder="Relevant topics separated by commas">
            @error('meta_keywords') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">AEO Summary (AI Answer Snippet)</label>
            <textarea name="aeo_summary" rows="2" maxlength="{{ $aeoSummaryMax }}"
                      class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white p-4 text-sm"
                      placeholder="Answer the content's main question in one or two factual sentences">{{ old('aeo_summary', $model?->aeo_summary) }}</textarea>
            <p class="text-[10px] text-zinc-400 mt-2">Use one or two factual sentences that agree with the visible content (up to {{ $aeoSummaryMax }} characters).</p>
            @error('aeo_summary') <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
        </div>

        <div class="md:col-span-2 rounded-xl border border-blue-100 bg-blue-50 p-4 text-xs text-blue-900 dark:border-blue-900/40 dark:bg-blue-900/20 dark:text-blue-100">
            Laravel generates structured data from the published title, description, image, dates, relationships, and verified academy settings. No raw scripts or JSON-LD are accepted here.
        </div>
    </div>

    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
        <div class="flex gap-3">
            <i class="fas fa-lightbulb text-amber-500 mt-1"></i>
            <div>
                <p class="text-[11px] font-black uppercase text-amber-800 dark:text-amber-200 leading-none mb-1">User Guide: {{ $title }}</p>
                <p class="text-[10px] text-amber-700 dark:text-amber-300 leading-relaxed">Keep the answer summary, search title and description factual and consistent with the visible page. Preview before publishing and avoid duplicate titles.</p>
            </div>
        </div>
    </div>
</div>

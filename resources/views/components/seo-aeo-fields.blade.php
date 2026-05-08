@props(['model' => null, 'title' => 'Optimization (SEO / AEO / GEO)', 'showTitle' => true])

<div class="mt-10 p-6 bg-zinc-50 dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-700 space-y-6">
    <div class="flex items-center gap-3 mb-2">
        <div class="w-10 h-10 rounded-xl bg-zinc-900 text-[#C5A059] flex items-center justify-center">
            <i class="fas fa-magic"></i>
        </div>
        <div>
            <h3 class="text-lg font-black uppercase text-zinc-800 dark:text-white leading-none">{{ $title }}</h3>
            <p class="text-[10px] text-zinc-500 font-medium mt-1">Configure how search engines and AI models perceive this content.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @if($showTitle)
        <div class="md:col-span-2">
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">Custom SEO Meta Title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $model?->meta_title) }}" 
                   class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white h-12 px-4 text-sm"
                   placeholder="Defaults to content title if empty...">
            <p class="text-[10px] text-zinc-400 mt-2">Optimal length: 50-60 characters.</p>
        </div>
        @endif

        <div class="md:col-span-2">
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">SEO Meta Description</label>
            <textarea name="meta_description" rows="2" 
                      class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white p-4 text-sm">{{ old('meta_description', $model?->meta_description) }}</textarea>
            <p class="text-[10px] text-zinc-400 mt-2">Optimal length: 150-160 characters. Used by Google in search snippets.</p>
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">SEO Meta Keywords</label>
            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $model?->meta_keywords) }}" 
                   class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white h-12 px-4 text-sm"
                   placeholder="keyword1, keyword2, ...">
        </div>

        <div>
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">AEO Summary (AI Answer Snippet)</label>
            <textarea name="aeo_summary" rows="2" 
                      class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white p-4 text-sm"
                      placeholder="A concise 2-sentence summary for AI models like Perplexity/ChatGPT...">{{ old('aeo_summary', $model?->aeo_summary) }}</textarea>
        </div>

        <div class="md:col-span-2">
            <label class="block text-xs font-black uppercase text-zinc-500 mb-2 tracking-widest">Custom Schema Markup (JSON-LD)</label>
            <textarea name="schema_markup" rows="3" 
                      class="w-full rounded-xl border-zinc-200 dark:bg-zinc-800 dark:border-zinc-700 dark:text-white p-4 text-sm font-mono"
                      placeholder='{ "@@context": "https://schema.org", ... }'>{{ old('schema_markup', $model?->schema_markup) }}</textarea>
            <p class="text-[10px] text-zinc-400 mt-2">Advanced: Inject custom JSON-LD specifically for this page. Will be added to the head.</p>
        </div>
    </div>
    
    <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
        <div class="flex gap-3">
            <i class="fas fa-lightbulb text-amber-500 mt-1"></i>
            <div>
                <p class="text-[11px] font-black uppercase text-amber-800 dark:text-amber-200 leading-none mb-1">User Guide: {{ $title }}</p>
                <p class="text-[10px] text-amber-700 dark:text-amber-300 leading-relaxed">AI engines (ChatGPT/Gemini) prioritize the <strong>AEO Summary</strong>. Make it factual and direct. Google prioritizes the <strong>Meta Title</strong> and <strong>Description</strong>. Use the <strong>Schema</strong> field only if you need to override the automatic institutional data.</p>
            </div>
        </div>
    </div>
</div>

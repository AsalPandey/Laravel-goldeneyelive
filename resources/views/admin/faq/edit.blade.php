<x-layouts::app :title="__('Edit FAQ')">
    <div class="max-w-3xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Edit FAQ</h1>
            <a href="{{ route('admin.faq.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-2xl border border-neutral-100 bg-white p-10 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.faq.update', $faq->id) }}" method="POST" class="space-y-8">
                @csrf @method('PUT')
                <div>
                    <label class="premium-label">Question / Inquiry Title</label>
                    <input type="text" name="question" value="{{ old('question', $faq->question) }}" required class="premium-input">
                </div>

                <div>
                    <label class="premium-label">Detailed Response (Supports Rich Text)</label>
                    <textarea name="answer" id="answer" rows="5" required class="premium-input h-auto p-3">{{ old('answer', $faq->answer) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="premium-label">Publication Status</label>
                        <select name="status" required class="premium-input cursor-pointer">
                            <option value="active" {{ $faq->status === 'active' ? 'selected' : '' }}>PUBLISHED (Live on Website)</option>
                            <option value="inactive" {{ $faq->status === 'inactive' ? 'selected' : '' }}>ARCHIVED (Internal Only)</option>
                        </select>
                    </div>
                    <div>
                        <label class="premium-label">Display Priority (Higher = Top)</label>
                        <input type="number" name="order_priority" value="{{ old('order_priority', $faq->order_priority) }}" class="premium-input" placeholder="0">
                    </div>
                </div>

                <x-seo-aeo-fields :model="$faq" />

                <div class="flex justify-end pt-6 border-t border-neutral-50">
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-[#050C1C] py-3.5 px-12 text-xs font-black uppercase tracking-widest text-[#C5A059] shadow-2xl hover:bg-orange-600 hover:text-white transform hover:-translate-y-1 transition-all active:scale-95">
                        <i class="fa fa-sync-alt mr-2"></i> Update FAQ Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('answer', {
            height: 300,
            removeButtons: 'About',
            versionCheck: false 
        });
    </script>
    </div>
</x-layouts::app>

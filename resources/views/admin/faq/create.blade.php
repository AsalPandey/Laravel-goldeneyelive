<x-layouts::app :title="__('New FAQ')">
    <div class="max-w-3xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Create FAQ</h1>
            <a href="{{ route('admin.faq.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-2xl border border-neutral-100 bg-white p-10 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.faq.store') }}" method="POST" class="space-y-8">
                @csrf
                <div>
                    <label class="premium-label">Question / Inquiry Title</label>
                    <input type="text" name="question" value="{{ old('question') }}" required class="premium-input" placeholder="e.g. What is the duration of the Web Development course?">
                    @error('question') <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="premium-label">Detailed Response (Supports Rich Text)</label>
                    <textarea name="answer" id="answer" rows="5" required class="premium-input h-auto p-3">{{ old('answer') }}</textarea>
                    @error('answer') <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="premium-label">Publication Status</label>
                        <select name="status" required class="premium-input cursor-pointer">
                            <option value="inactive" {{ old('status', 'inactive') === 'inactive' ? 'selected' : '' }}>INACTIVE (Not Public)</option>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>PUBLIC (Live on Website)</option>
                        </select>
                        <p class="mt-1 text-xs text-neutral-500">New FAQs stay inactive unless you explicitly make them public.</p>
                        @error('status') <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="premium-label">Display Order</label>
                        <input type="number" min="0" name="order_priority" value="{{ old('order_priority', 0) }}" class="premium-input" placeholder="0">
                        <p class="mt-1 text-xs text-neutral-500">Lower numbers appear first in Admin and on the public FAQ page.</p>
                        @error('order_priority') <p class="mt-1 text-xs text-red-600 font-bold italic">{{ $message }}</p> @enderror
                    </div>
                </div>

                <x-seo-aeo-fields />

                <div class="p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-100 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-bold">Assign to Specific Courses (Optional)</label>
                        <span class="text-[10px] text-neutral-500">Checkbox UI (Searchable controls may be added for larger course sets)</span>
                    </div>
                    @if(isset($courses) && $courses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-2 border rounded-lg bg-white dark:bg-neutral-800">
                            @foreach($courses as $course)
                                <label class="flex items-start gap-2 p-2 rounded hover:bg-neutral-100 dark:hover:bg-neutral-700 cursor-pointer">
                                    <input type="checkbox" name="courses[]" value="{{ $course->id }}" {{ in_array($course->id, old('courses', [])) ? 'checked' : '' }} class="mt-1 rounded border-neutral-300 text-orange-600 focus:ring-orange-500">
                                    <div class="text-xs">
                                        <span class="font-medium text-neutral-900 dark:text-neutral-100">{{ $course->name }}</span>
                                        @if($course->status === 'inactive')
                                            <span class="ml-1 text-[9px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 font-bold">(Inactive)</span>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-neutral-500 italic">No active courses available for assignment.</p>
                    @endif
                    @error('courses')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-6 border-t border-neutral-50">
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-neutral-900 py-3.5 px-10 text-xs font-black uppercase tracking-widest text-[#C5A059] shadow-2xl hover:bg-orange-600 hover:text-white transform hover:-translate-y-1 transition-all active:scale-95">
                        <i class="fa fa-save mr-2"></i> Create FAQ
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

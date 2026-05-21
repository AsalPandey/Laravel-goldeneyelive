<x-layouts::app :title="__('Edit Course')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Edit Course: {{ $course->name }}</h1>
            <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Name</label>
                        <input type="text" name="name" value="{{ old('name', $course->name) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">URL Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $course->slug ?: \Illuminate\Support\Str::slug($course->name)) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3 bg-neutral-50 dark:bg-neutral-950">
                        <p class="text-[10px] text-red-500 mt-1 italic font-bold">Warning: Changing this will break existing links to this course.</p>
                        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Badge (e.g. Job Oriented)</label>
                        <input type="text" name="badge_text" value="{{ old('badge_text', $course->badge_text) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3" placeholder="Job Oriented">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Display Order</label>
                        <input type="number" name="display_order" value="{{ old('display_order', $course->display_order ?? 100) }}" min="0" max="9999" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <p class="text-[10px] text-neutral-500 mt-1 italic">Lower numbers appear first. Use 1-10 for seasonal hot courses.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Category</label>
                        <select name="category_id" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Price</label>
                        <input type="text" name="price" value="{{ old('price', $course->price) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Duration</label>
                        <input type="text" name="duration" value="{{ old('duration', $course->duration) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Instructor</label>
                        <input type="text" name="instructor" value="{{ old('instructor', $course->instructor) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Capacity</label>
                        <input type="text" name="capacity" value="{{ old('capacity', $course->capacity) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active (Public)</option>
                            <option value="inactive" {{ old('status', $course->status) == 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="premium-label">Full Curriculum Description</label>
                    <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('description', $course->description) }}</textarea>
                </div>

                <div>
                    <label class="premium-label">Target Outcomes / Course Outline</label>
                    <textarea name="course_outline" id="course_outline" rows="5" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-brand-gold focus:ring-brand-gold dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('course_outline', $course->course_outline) }}</textarea>
                </div>

                <x-seo-aeo-fields :model="$course" :showTitle="false" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-100">
                    <div>
                        <label class="block text-sm font-bold mb-2">Course Photo</label>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset($course->photo) }}" id="course_photo_preview" class="h-16 w-16 object-cover rounded shadow border">
                                <span class="text-[10px] text-neutral-400 font-mono italic">{{ $course->photo }}</span>
                            </div>
                            <div class="flex flex-col gap-2">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase">Option A: Upload</span>
                                <input type="file" name="photo" class="block w-full text-xs">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase mt-1">Option B: Use Library Path</span>
                                <div class="flex gap-2">
                                    <input type="text" name="photo_path" value="{{ $course->photo }}" placeholder="e.g. site/img/cat-1.jpg" class="flex-1 text-xs rounded border-neutral-300 dark:bg-neutral-800 h-8 px-2">
                                    <button type="button" onclick="openMediaVault('photo_path', 'course_photo_preview')" class="bg-brand-dark text-brand-gold text-[8px] font-black uppercase px-3 rounded-md hover:bg-brand-gold hover:text-brand-dark transition-all">Pick</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-l pl-6 flex flex-col justify-center">
                        <label class="block text-sm font-bold mb-2">Branding Hub</label>
                        <p class="text-[10px] text-neutral-500 mb-3 text-pretty">Reuse core branding assets here to keep course visuals consistent with the homepage.</p>
                        <button type="button" onclick="openMediaVault('photo_path', 'course_photo_preview')" class="inline-flex items-center gap-2 text-xs font-bold text-brand-gold hover:text-brand-dark transition-all">
                            <i class="fa fa-images"></i> Browse Library
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-6 p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $course->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-brand-gold rounded border-gray-300 focus:ring-brand-gold">
                        <label for="is_featured" class="text-sm font-bold text-neutral-900">Feature this course on homepage?</label>
                    </div>
                    <p class="text-[10px] text-neutral-500 italic">Featured courses appear first. Display Order controls the exact order across homepage and courses page.</p>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-brand-gold py-3 px-10 text-sm font-black uppercase text-brand-dark shadow-lg hover:bg-brand-dark hover:text-brand-gold transition-all">
                        Update Course Details
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initEditors() {
            if (typeof CKEDITOR !== 'undefined') {
                if (document.querySelector('textarea[name="course_outline"]')) {
                    CKEDITOR.replace('course_outline', { versionCheck: false });
                }
                if (document.querySelector('textarea[name="description"]')) {
                    CKEDITOR.replace('description', { versionCheck: false });
                }
            }
        }

        // Support standard load and Livewire navigation
        document.addEventListener('DOMContentLoaded', initEditors);
        document.addEventListener('livewire:navigated', initEditors);
    </script>
</x-layouts::app>

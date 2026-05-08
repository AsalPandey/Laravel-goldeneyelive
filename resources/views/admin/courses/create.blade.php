<x-layouts::app :title="__('Add New Course')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Add New Course</h1>
            <a href="{{ route('admin.courses.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Name</label>
                        <input type="text" name="name" id="course_name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">URL Slug (e.g. basic-computer)</label>
                        <input type="text" name="slug" id="course_slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <p class="text-[10px] text-neutral-500 mt-1 italic">Leave empty to auto-generate from name.</p>
                        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Badge (e.g. Job Oriented)</label>
                        <input type="text" name="badge_text" value="{{ old('badge_text', 'Job Oriented') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3" placeholder="Job Oriented">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Display Order</label>
                        <input type="number" name="display_order" value="{{ old('display_order', 100) }}" min="0" max="9999" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <p class="text-[10px] text-neutral-500 mt-1 italic">Lower numbers appear first. Use 1-10 for seasonal hot courses.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Category</label>
                        <select name="category_id" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Price (e.g., Rs. 5000)</label>
                        <input type="text" name="price" value="{{ old('price') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Duration (e.g., 3 Months)</label>
                        <input type="text" name="duration" value="{{ old('duration') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Instructor</label>
                        <input type="text" name="instructor" value="{{ old('instructor') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Capacity (e.g., 20 Seats)</label>
                        <input type="text" name="capacity" value="{{ old('capacity', '20 Seats') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div class="flex items-center gap-6 mt-4">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                            <select name="status" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active (Public)</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive (Draft)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="premium-label">Full Curriculum Description</label>
                    <textarea name="description" id="description" rows="3" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="premium-label">Target Outcomes / Course Outline</label>
                    <textarea name="course_outline" id="course_outline" rows="5" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('course_outline') }}</textarea>
                </div>

                <x-seo-aeo-fields :showTitle="false" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-100">
                    <div>
                        <label class="block text-sm font-bold mb-2">Course Photo</label>
                        <div class="space-y-3">
                            <div class="flex flex-col gap-2">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase">Option A: Upload</span>
                                <input type="file" name="photo" class="block w-full text-xs">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase mt-1">Option B: Use Library Path</span>
                                <div class="flex gap-2">
                                    <input type="text" name="photo_path" placeholder="e.g. site/img/cat-1.jpg" class="flex-1 text-xs rounded border-neutral-300 dark:bg-neutral-800 h-8 px-2">
                                    <button type="button" onclick="openMediaVault('photo_path')" class="bg-neutral-900 text-white text-[8px] font-black uppercase px-3 rounded-md hover:bg-orange-600 transition-all">Pick</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-l pl-6 flex flex-col justify-center">
                        <label class="block text-sm font-bold mb-2">Branding Hub</label>
                        <p class="text-[10px] text-neutral-500 mb-3 text-pretty">Reuse core branding assets here to keep course visuals consistent.</p>
                        <button type="button" onclick="openMediaVault('photo_path')" class="inline-flex items-center gap-2 text-xs font-bold text-orange-600 hover:text-orange-700">
                            <i class="fa fa-images"></i> Browse Library
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-6 p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                        <label for="is_featured" class="text-sm font-bold text-neutral-900">Feature this course on homepage?</label>
                    </div>
                    <p class="text-[10px] text-neutral-500 italic">Featured courses appear first. Display Order controls the exact order across homepage and courses page.</p>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-orange-600 py-3 px-10 text-sm font-bold text-white shadow-lg hover:bg-orange-700 transition-all">
                        Create New Course
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initCourseCreateEditors() {
            if (typeof CKEDITOR !== 'undefined') {
                if (document.querySelector('textarea[name="course_outline"]')) {
                    CKEDITOR.replace('course_outline', { versionCheck: false });
                }
                if (document.querySelector('textarea[name="description"]')) {
                    CKEDITOR.replace('description', { versionCheck: false });
                }
            }
        }
        document.addEventListener('DOMContentLoaded', initCourseCreateEditors);
        document.addEventListener('livewire:navigated', initCourseCreateEditors);

        document.getElementById('course_name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase()
                           .replace(/[^\w\s-]/g, '') // Remove special chars
                           .replace(/\s+/g, '-')      // Replace spaces with -
                           .replace(/-+/g, '-');      // Remove duplicate -
            document.getElementById('course_slug').value = slug;
        });
    </script>
</x-layouts::app>

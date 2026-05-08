<x-layouts::app :title="__('Add Testimonial')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Add New Testimonial</h1>
            <a href="{{ route('admin.testimonials.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Student Name</label>
                        <input type="text" name="student_name" value="{{ old('student_name') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        @error('student_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Name</label>
                        <input type="text" name="course_name" value="{{ old('course_name') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3" placeholder="e.g. IELTS Preparation">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Testimonial Content</label>
                    <textarea name="content" rows="4" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Rating (1-5)</label>
                        <select name="rating" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>★★★★★ (5)</option>
                            <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>★★★★☆ (4)</option>
                            <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>★★★☆☆ (3)</option>
                            <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>★★☆☆☆ (2)</option>
                            <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>★☆☆☆☆ (1)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Student Photo</label>
                        <div class="mt-1 flex flex-col gap-2">
                            <input type="file" name="photo" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <div class="flex items-center gap-2">
                                <input type="text" name="photo_path" placeholder="OR Library Path: site/img/..." class="flex-1 rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-8 px-3 text-xs">
                                <button type="button" onclick="openMediaVault('photo_path')" class="text-[10px] font-black text-white bg-neutral-900 px-3 py-1 rounded-md uppercase hover:bg-orange-600 transition-all">
                                    Pick
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="sr-only peer">
                            <div class="relative w-11 h-6 bg-neutral-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 dark:peer-focus:ring-orange-800 rounded-full peer dark:bg-neutral-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-neutral-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-neutral-600 peer-checked:bg-orange-600"></div>
                            <span class="ms-3 text-sm font-medium text-neutral-900 dark:text-neutral-300">Feature on Homepage</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center rounded-md bg-orange-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-orange-700">Add Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>

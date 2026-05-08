<x-layouts::app :title="__('Edit Testimonial')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Edit: {{ $testimonial->student_name }}</h1>
            <a href="{{ route('admin.testimonials.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Student Name</label>
                        <input type="text" name="student_name" value="{{ old('student_name', $testimonial->student_name) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Course Name</label>
                        <input type="text" name="course_name" value="{{ old('course_name', $testimonial->course_name) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Testimonial Content</label>
                    <textarea name="content" rows="4" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('content', $testimonial->content) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Rating (1-5)</label>
                        <select name="rating" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ old('rating', $testimonial->rating) == $i ? 'selected' : '' }}>{{ str_repeat('★', $i) }}{{ str_repeat('☆', 5 - $i) }} ({{ $i }})</option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-center gap-4">
                        @if($testimonial->photo)
                        <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-full border border-neutral-200 dark:border-neutral-700">
                            <img src="{{ asset($testimonial->photo) }}" id="testimonial_photo_preview" class="h-full w-full object-cover">
                        </div>
                        @endif
                        <div class="flex-1 space-y-2">
                            <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Update Photo (Optional)</label>
                            <input type="file" name="photo" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                            <div class="flex items-center gap-2">
                                <input type="text" name="photo_path" value="{{ $testimonial->photo }}" placeholder="OR Library Path: site/img/..." class="flex-1 rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-8 px-3 text-xs">
                                <button type="button" onclick="openMediaVault('photo_path', 'testimonial_photo_preview')" class="text-[10px] font-black text-white bg-neutral-900 px-3 py-1 rounded-md uppercase hover:bg-orange-600 transition-all">
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
                            <option value="active" {{ old('status', $testimonial->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $testimonial->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-6 p-4 bg-orange-50 rounded-xl border border-orange-100">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $testimonial->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                            <label for="is_featured" class="text-sm font-bold text-neutral-900">Feature on homepage?</label>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center rounded-md bg-orange-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-orange-700">Update Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>

@php
    $savedCourseIds = isset($post) ? $post->courses->pluck('id')->all() : [];
    $selectedCourseIds = collect(old('courses', old('courses_present') ? [] : $savedCourseIds))
        ->map(fn ($courseId) => (int) $courseId)
        ->all();
@endphp

<section class="rounded-xl border border-neutral-200 bg-neutral-50 p-5 dark:border-neutral-700 dark:bg-neutral-900" data-blog-course-picker>
    <input type="hidden" name="courses_present" value="1">
    <div class="mb-4">
        <label class="block text-sm font-bold text-neutral-900 dark:text-white">Related Courses</label>
        <p class="mt-1 text-xs text-neutral-500">Optional. Select the current academy courses that this article directly helps readers understand or compare.</p>
    </div>

    <label class="sr-only" for="related_course_search">Search related courses</label>
    <input id="related_course_search" type="search" data-course-search placeholder="Search courses…" class="mb-4 block h-10 w-full rounded-md border-neutral-300 px-3 text-sm shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:border-neutral-700 dark:bg-neutral-800 dark:text-white">

    @if($courses->isNotEmpty())
        <div class="grid max-h-64 grid-cols-1 gap-2 overflow-y-auto rounded-lg border border-neutral-200 bg-white p-2 md:grid-cols-2 dark:border-neutral-700 dark:bg-neutral-800" data-course-options>
            @foreach($courses as $course)
                <label class="flex cursor-pointer items-start gap-2 rounded p-2 hover:bg-neutral-100 dark:hover:bg-neutral-700" data-course-option data-course-name="{{ Str::lower($course->name) }}">
                    <input type="checkbox" name="courses[]" value="{{ $course->id }}" @checked(in_array($course->id, $selectedCourseIds, true)) class="mt-1 rounded border-neutral-300 text-orange-600 focus:ring-orange-500">
                    <span class="text-xs text-neutral-900 dark:text-neutral-100">
                        <span class="font-medium">{{ $course->name }}</span>
                        @if($course->status !== 'active')
                            <span class="ml-1 rounded bg-red-100 px-1.5 py-0.5 text-[9px] font-bold text-red-700">Inactive</span>
                        @endif
                    </span>
                </label>
            @endforeach
        </div>
    @else
        <p class="text-xs italic text-neutral-500">No active courses are available.</p>
    @endif

    @error('courses')
        <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
    @enderror
    @error('courses.*')
        <p class="mt-2 text-xs font-bold text-red-600">{{ $message }}</p>
    @enderror
</section>

@once
    <script>
        (function () {
            function bindBlogCoursePickers() {
                document.querySelectorAll('[data-blog-course-picker]').forEach(function (picker) {
                    if (picker.dataset.bound === 'true') {
                        return;
                    }

                    picker.dataset.bound = 'true';
                    const search = picker.querySelector('[data-course-search]');

                    search?.addEventListener('input', function () {
                        const query = this.value.trim().toLowerCase();

                        picker.querySelectorAll('[data-course-option]').forEach(function (option) {
                            option.hidden = query !== '' && ! option.dataset.courseName.includes(query);
                        });
                    });
                });
            }

            document.addEventListener('DOMContentLoaded', bindBlogCoursePickers);
            document.addEventListener('livewire:navigated', bindBlogCoursePickers);
            bindBlogCoursePickers();
        })();
    </script>
@endonce

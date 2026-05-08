<x-layouts::app :title="__('Add Teacher')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Add New Teacher</h1>
            <a href="{{ route('admin.teachers.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.teachers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Full Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3" placeholder="e.g. IELTS Instructor">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Bio</label>
                    <textarea name="bio" rows="4" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('bio') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Facebook URL</label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Photo</label>
                    <div class="mt-1 flex flex-col gap-3">
                        <input type="file" name="photo" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-orange-700 hover:file:bg-orange-100">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-neutral-500">OR Use Library Path:</span>
                            <input type="text" name="photo_path" placeholder="site/img/team-1.jpg" class="flex-1 rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-8 px-3 text-xs">
                            <button type="button" onclick="openMediaVault('photo_path')" class="bg-neutral-900 text-white text-[9px] font-black uppercase px-4 py-2 rounded-lg hover:bg-orange-600 transition-all">Pick</button>
                        </div>
                    </div>
                </div>

                <x-seo-aeo-fields />
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                    <label for="is_featured" class="text-sm font-bold text-neutral-900">Feature this teacher on homepage?</label>
                </div>
                <p class="text-[10px] text-neutral-500 italic">Featured teachers appear in the "Our Instructors" carousel on the homepage.</p>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center rounded-md bg-orange-600 py-2 px-6 text-sm font-medium text-white shadow-sm hover:bg-orange-700">Add Teacher</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initTeacherCreateEditor() {
            if (typeof CKEDITOR !== 'undefined' && document.querySelector('textarea[name="bio"]')) {
                CKEDITOR.replace('bio', { versionCheck: false });
            }
        }
        document.addEventListener('DOMContentLoaded', initTeacherCreateEditor);
        document.addEventListener('livewire:navigated', initTeacherCreateEditor);
    </script>
</x-layouts::app>

<x-layouts::app :title="__('Edit Teacher')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Edit: {{ $teacher->name }}</h1>
            <a href="{{ route('admin.teachers.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.teachers.update', $teacher->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Designation</label>
                        <input type="text" name="designation" value="{{ old('designation', $teacher->designation) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Bio</label>
                    <textarea name="bio" rows="4" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('bio', $teacher->bio) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Facebook URL</label>
                        <input type="url" name="facebook_url" value="{{ old('facebook_url', $teacher->facebook_url) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url', $teacher->linkedin_url) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <option value="active" {{ old('status', $teacher->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $teacher->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-neutral-50 dark:bg-neutral-900 rounded-xl border border-neutral-100">
                    <div>
                        <label class="block text-sm font-bold mb-2">Staff Photo</label>
                        <div class="space-y-3">
                            @if($teacher->photo)
                            <div class="flex items-center gap-3">
                                <img src="{{ asset($teacher->photo) }}" id="teacher_photo_preview" class="h-16 w-16 object-cover rounded shadow border">
                                <span class="text-[10px] text-neutral-400 font-mono italic">{{ $teacher->photo }}</span>
                            </div>
                            @endif
                            <div class="flex flex-col gap-2">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase">Option A: Upload New</span>
                                <input type="file" name="photo" class="block w-full text-xs">
                                <span class="text-[9px] font-bold text-neutral-400 uppercase mt-1">Option B: Use Library Path</span>
                                <div class="flex gap-2">
                                    <input type="text" name="photo_path" value="{{ $teacher->photo }}" placeholder="e.g. site/img/team-1.jpg" class="flex-1 text-xs rounded border-neutral-300 dark:bg-neutral-800 h-8 px-2">
                                    <button type="button" onclick="openMediaVault('photo_path', 'teacher_photo_preview')" class="bg-neutral-900 text-white text-[8px] font-black uppercase px-3 rounded-md hover:bg-orange-600 transition-all">Pick</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-l pl-6 flex flex-col justify-center">
                        <label class="block text-sm font-bold mb-2">Media Vault</label>
                        <p class="text-[10px] text-neutral-500 mb-3">Quickly access the branding hub to find existing files for this profile.</p>
                        <button type="button" onclick="openMediaVault('photo_path', 'teacher_photo_preview')" class="inline-flex items-center gap-2 text-xs font-bold text-orange-600 hover:text-orange-700">
                            <i class="fa fa-images"></i> Browse Library
                        </button>
                    </div>
                </div>

                <x-seo-aeo-fields :model="$teacher" />
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1" {{ old('is_featured', $teacher->is_featured) ? 'checked' : '' }} class="w-4 h-4 text-orange-600 rounded border-gray-300 focus:ring-orange-500">
                    <label for="is_featured" class="text-sm font-bold text-neutral-900">Feature this teacher on homepage?</label>
                </div>
                <p class="text-[10px] text-neutral-500 italic">Featured teachers appear in the "Our Instructors" carousel on the homepage.</p>

                <div class="flex justify-end mt-6">
                    <button type="submit" class="inline-flex justify-center rounded-xl bg-orange-600 py-3 px-12 text-sm font-bold text-white shadow-lg hover:bg-orange-700 transition-all">Update Teacher Profile</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initTeacherEditor() {
            if (typeof CKEDITOR !== 'undefined' && document.querySelector('textarea[name="bio"]')) {
                CKEDITOR.replace('bio', { versionCheck: false });
            }
        }
        document.addEventListener('DOMContentLoaded', initTeacherEditor);
        document.addEventListener('livewire:navigated', initTeacherEditor);
    </script>
</x-layouts::app>

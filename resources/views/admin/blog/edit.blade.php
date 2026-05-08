<x-layouts::app :title="__('Edit Blog Post')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Edit: {{ $post->title }}</h1>
            <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.blog.update', $post->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Blog Title</label>
                        <input type="text" name="title" value="{{ old('title', $post->title) }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">URL Slug</label>
                        <input type="text" name="slug" value="{{ old('slug', $post->slug) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3 bg-neutral-50 dark:bg-neutral-950">
                        <p class="text-[10px] text-red-500 mt-1 italic font-bold">Warning: Changing this will break existing links to this article.</p>
                        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Author</label>
                    <input type="text" name="author" value="{{ old('author', $post->author) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Content</label>
                    <textarea name="content" rows="10" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('content', $post->content) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            <option value="draft" {{ $post->status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ $post->status === 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <x-seo-aeo-fields :model="$post" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white rounded-2xl border-4 border-neutral-50 shadow-inner">
                    <div>
                        <label class="block text-sm font-black text-neutral-900 mb-3">Featured Graphic</label>
                        <div class="space-y-4">
                            @if($post->image)
                                <div class="relative group w-32 h-20 overflow-hidden rounded-xl">
                                    <img src="{{ asset($post->image) }}" id="blog_image_preview" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" class="w-full h-full object-cover shadow-sm border-2 border-white">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <span class="text-[8px] text-white font-bold uppercase tracking-widest">Active Image</span>
                                    </div>
                                </div>
                            @endif
                            <div class="p-4 bg-neutral-50 rounded-xl border border-dashed border-neutral-200">
                                <span class="text-[9px] font-black text-neutral-400 uppercase tracking-widest block mb-2">Replace: Direct Upload</span>
                                <input type="file" name="image" class="block w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:bg-orange-600 file:text-white hover:file:bg-neutral-900 transition-all">
                            </div>
                            
                            <div class="p-4 bg-neutral-50 rounded-xl border border-dashed border-neutral-200">
                                <span class="text-[9px] font-black text-neutral-400 uppercase tracking-widest block mb-2">Replace: Library Path</span>
                                <div class="flex gap-2">
                                    <input type="text" name="image_path" id="image_path" value="{{ $post->image }}" placeholder="e.g. site/img/carousel-1.png" class="flex-1 rounded-xl border-neutral-200 text-xs h-10 px-4 focus:border-orange-600 focus:ring-0">
                                    <button type="button" onclick="openMediaVault('image_path', 'blog_image_preview')" class="bg-neutral-900 text-[#C5A059] text-[9px] font-black uppercase px-4 flex items-center justify-center rounded-xl hover:bg-orange-600 hover:text-white transition-all">
                                         Vault
                                     </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center items-center text-center p-6 border-l border-neutral-100">
                        <div class="h-16 w-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4 border border-orange-100 shadow-sm">
                            <i class="fa fa-highlighter text-2xl"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase text-neutral-900 mb-1">Editorial Integrity</h4>
                        <p class="text-[10px] text-neutral-500 leading-relaxed max-w-[200px]">Always ensure your slugs are concise and include primary keywords for the best AEO performance.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-neutral-100">
                    <button type="submit" class="inline-flex justify-center rounded-2xl bg-[#050C1C] py-4 px-14 text-sm font-black uppercase tracking-widest text-[#C5A059] shadow-2xl hover:bg-orange-600 hover:text-white transform hover:-translate-y-1 transition-all active:scale-95">
                        <i class="fa fa-sync-alt mr-2"></i> Update Article
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initBlogEditor() {
            if (typeof CKEDITOR !== 'undefined' && document.querySelector('textarea[name="content"]')) {
                CKEDITOR.replace('content', {
                    height: 450,
                    removeButtons: 'About',
                    versionCheck: false 
                });
            }
        }
        document.addEventListener('DOMContentLoaded', initBlogEditor);
        document.addEventListener('livewire:navigated', initBlogEditor);
    </script>
</x-layouts::app>

<x-layouts::app :title="__('New Blog Post')">
    <div class="max-w-4xl mx-auto p-6">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-neutral-900 dark:text-white">Create Blog Post</h1>
            <a href="{{ route('admin.blog.index') }}" class="text-sm font-medium text-neutral-500 hover:text-neutral-700 dark:text-neutral-400">Back to List</a>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 shadow-sm dark:border-neutral-700 dark:bg-neutral-800">
            <form action="{{ route('admin.blog.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Blog Title</label>
                        <input type="text" name="title" id="blog_title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">URL Slug (e.g. how-to-learn-web-dev)</label>
                        <input type="text" name="slug" id="blog_slug" value="{{ old('slug') }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                        <p class="text-[10px] text-neutral-500 mt-1 italic">Leave empty to auto-generate from title.</p>
                        @error('slug') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Author</label>
                    <input type="text" name="author" value="{{ old('author', auth()->user()->name) }}" class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Content</label>
                    <textarea name="content" rows="10" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white p-3">{{ old('content') }}</textarea>
                    @error('content') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Status</label>
                        <select name="status" required class="mt-1 block w-full rounded-md border-neutral-300 shadow-sm focus:border-orange-500 focus:ring-orange-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white h-10 px-3">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>

                <x-seo-aeo-fields />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-white rounded-2xl border-4 border-neutral-50 shadow-inner">
                    <div>
                        <label class="block text-sm font-black text-neutral-900 mb-3">Featured Graphic</label>
                        <div class="space-y-4">
                            <div id="blog_image_container" class="hidden relative group w-32 h-20 overflow-hidden rounded-xl mb-4">
                                <img src="" id="blog_image_preview" class="w-full h-full object-cover shadow-sm border-2 border-white">
                            </div>
                            <div class="p-4 bg-neutral-50 rounded-xl border border-dashed border-neutral-200">
                                <span class="text-[9px] font-black text-neutral-400 uppercase tracking-widest block mb-2">Method 1: Direct Upload</span>
                                <input type="file" name="image" class="block w-full text-xs text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:uppercase file:bg-orange-600 file:text-white hover:file:bg-neutral-900 transition-all">
                            </div>
                            
                            <div class="p-4 bg-neutral-50 rounded-xl border border-dashed border-neutral-200">
                                <span class="text-[9px] font-black text-neutral-400 uppercase tracking-widest block mb-2">Method 2: Use Centralized Library</span>
                                <div class="flex gap-2">
                                    <input type="text" name="image_path" id="image_path" placeholder="e.g. site/img/carousel-1.png" class="flex-1 rounded-xl border-neutral-200 text-xs h-10 px-4 focus:border-orange-600 focus:ring-0">
                                    <button type="button" onclick="openMediaVault('image_path', 'blog_image_preview'); document.getElementById('blog_image_container').classList.remove('hidden');" class="bg-neutral-900 text-[#C5A059] text-[9px] font-black uppercase px-4 flex items-center justify-center rounded-xl hover:bg-orange-600 hover:text-white transition-all">
                                        Vault
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col justify-center items-center text-center p-6">
                        <div class="h-16 w-16 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center mb-4 border border-orange-100 shadow-sm">
                            <i class="fa fa-robot text-2xl"></i>
                        </div>
                        <h4 class="text-xs font-black uppercase text-neutral-900 mb-1">AEO/GEO Intelligence</h4>
                        <p class="text-[10px] text-neutral-500 leading-relaxed max-w-[200px]">Our engines automatically generate semantic sitemaps and base Schema.org markups for every post.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-neutral-100">
                    <button type="submit" class="inline-flex justify-center rounded-2xl bg-[#050C1C] py-4 px-12 text-sm font-black uppercase tracking-widest text-[#C5A059] shadow-2xl hover:bg-orange-600 hover:text-white transform hover:-translate-y-1 transition-all active:scale-95">
                        <i class="fa fa-rocket mr-2"></i> Launch Article
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.ckeditor.com/4.25.1-lts/standard/ckeditor.js"></script>
    <script>
        function initBlogCreateEditor() {
            if (typeof CKEDITOR !== 'undefined' && document.querySelector('textarea[name="content"]')) {
                CKEDITOR.replace('content', {
                    height: 400,
                    removeButtons: 'About',
                    versionCheck: false 
                });
            }
        }
        document.addEventListener('DOMContentLoaded', initBlogCreateEditor);
        document.addEventListener('livewire:navigated', initBlogCreateEditor);

        document.addEventListener('livewire:navigated', () => {
            const blogTitle = document.getElementById('blog_title');
            if (blogTitle) {
                blogTitle.addEventListener('input', function() {
                    let title = this.value;
                    let slug = title.toLowerCase()
                                    .replace(/[^\w\s-]/g, '') // Remove special chars
                                    .replace(/\s+/g, '-')      // Replace spaces with -
                                    .replace(/-+/g, '-');      // Remove duplicate -
                    document.getElementById('blog_slug').value = slug;
                });
            }
        });
        
        // Initial binding
        const blogTitle = document.getElementById('blog_title');
        if (blogTitle) {
            blogTitle.addEventListener('input', function() {
                let title = this.value;
                let slug = title.toLowerCase()
                                .replace(/[^\w\s-]/g, '')
                                .replace(/\s+/g, '-')
                                .replace(/-+/g, '-');
                document.getElementById('blog_slug').value = slug;
            });
        }
    </script>
</x-layouts::app>

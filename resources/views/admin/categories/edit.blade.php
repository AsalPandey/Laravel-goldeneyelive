<x-layouts::app :title="__('Modify Department Details')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8">
        <div class="flex items-center justify-between pb-4 border-b border-neutral-100">
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Update <span class="text-brand-gold">Department</span></h1>
                <p class="text-neutral-500 text-sm">Modify the details for the <strong>{{ $category->name }}</strong> category.</p>
            </div>
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-neutral-100 text-neutral-600 px-6 py-3 text-sm font-black uppercase hover:bg-neutral-900 hover:text-white transition-all">
                <i class="fa fa-arrow-left"></i> Back to Hub
            </a>
        </div>

        <div class="bg-white rounded-3xl border border-neutral-100 shadow-xl overflow-hidden">
            <form action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="p-10">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Basic Information -->
                    <div class="space-y-6">
                        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Department Identity</h3>
                            
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Category Name</label>
                                <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                                       class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold placeholder:font-normal"
                                       placeholder="e.g., Computer Classes">
                                @error('name') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Custom Slug</label>
                                <input type="text" name="slug" value="{{ old('slug', $category->slug ?: \Illuminate\Support\Str::slug($category->name)) }}" required
                                       class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-mono text-[11px]"
                                       placeholder="computer-classes">
                                <p class="text-[10px] text-neutral-500 mt-1 italic">Required. Use lowercase words with hyphens.</p>
                                @error('slug') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Display & Control -->
                        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Display & Control</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Status</label>
                                    <select name="status" class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold">
                                        <option value="active" {{ old('status', $category->status) === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $category->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Order Priority</label>
                                    <input type="number" name="order_priority" value="{{ old('order_priority', $category->order_priority) }}"
                                           class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all font-bold"
                                           placeholder="0">
                                    @error('order_priority') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Category Image -->
                        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4">
                            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Category Visual</h3>
                            <div class="flex items-start gap-6">
                                <div class="w-32 h-32 rounded-2xl bg-white border border-neutral-100 overflow-hidden shadow-inner flex-shrink-0">
                                    <img id="cat_preview" src="{{ asset($category->image ?? 'site/img/cat-1.jpg') }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 space-y-3">
                                    <div class="space-y-1">
                                        <span class="text-[9px] font-black uppercase text-neutral-400">Option A: Upload New</span>
                                        <input type="file" name="image" class="block w-full text-[10px] text-neutral-500">
                                    </div>
                                    <div class="space-y-1">
                                        <span class="text-[9px] font-black uppercase text-neutral-400">Option B: Use Library Path</span>
                                        <div class="flex gap-2">
                                            <input type="text" name="image_path" id="cat_image_path" value="{{ old('image_path', $category->image) }}" class="flex-1 px-3 py-2 rounded-lg border-neutral-100 bg-white text-[10px] font-mono" placeholder="site/img/cat-1.jpg">
                                            <button type="button" onclick="openMediaVault('image_path', 'cat_preview')" class="px-3 py-2 bg-brand-dark text-brand-gold text-[9px] font-black uppercase rounded-lg hover:bg-brand-gold hover:text-brand-dark transition-all">Pick</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-6">
                        <div class="p-6 bg-neutral-50/50 rounded-2xl border border-neutral-100 space-y-4 h-full">
                            <h3 class="text-xs font-black uppercase tracking-widest text-neutral-400">Context & Purpose</h3>
                            
                            <div class="space-y-2">
                                <label class="text-[11px] font-black uppercase text-neutral-500 tracking-wider">Description</label>
                                <textarea name="description" rows="5"
                                          class="w-full px-5 py-4 rounded-xl border-neutral-100 bg-white text-sm focus:border-brand-gold focus:ring-0 transition-all resize-none font-medium"
                                          placeholder="Define what this department covers...">{{ old('description', $category->description) }}</textarea>
                                @error('description') <p class="text-rose-500 text-[10px] font-bold mt-1 uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <x-seo-aeo-fields :model="$category" />
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-neutral-100 flex justify-between items-center">
                    <div class="text-[10px] font-black uppercase text-neutral-300 tracking-widest">
                        Category ID: #{{ $category->id }} | Last Updated: {{ $category->updated_at->format('M d, Y') }}
                    </div>
                    <div class="flex gap-4">
                        <a href="{{ route('admin.categories.index') }}" class="px-8 py-4 rounded-xl bg-neutral-100 text-neutral-500 text-sm font-black uppercase hover:bg-neutral-200 transition-all">
                            Discard Changes
                        </a>
                        <button type="submit" class="px-12 py-4 rounded-xl bg-brand-gold text-brand-dark text-sm font-black uppercase shadow-2xl hover:bg-brand-dark hover:text-brand-gold transition-all transform hover:-translate-y-1">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts::app>

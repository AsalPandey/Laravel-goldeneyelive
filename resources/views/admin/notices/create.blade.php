<x-layouts::app :title="__('Post New Notice')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8 max-w-4xl mx-auto">
        <div class="flex items-center gap-5 pb-6 border-b border-zinc-100">
            <a href="{{ route('admin.notices.index') }}" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-zinc-200 text-neutral-400 hover:text-brand-gold hover:border-brand-gold transition-all shadow-sm">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-brand-dark tracking-tight uppercase">New <span class="text-brand-gold">Notice</span></h1>
                <p class="text-neutral-500 text-sm">Upload a new institutional announcement graphic.</p>
            </div>
        </div>

        <form action="{{ route('admin.notices.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            <div class="bg-white rounded-[2rem] border border-zinc-100 p-10 shadow-xl space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Notice Title</label>
                        <input type="text" name="title" required value="{{ old('title') }}" maxlength="60"
                               class="w-full bg-zinc-50 border-zinc-100 rounded-2xl p-4 text-sm font-bold text-brand-dark focus:border-brand-gold focus:ring-4 focus:ring-brand-gold/10 transition-all" 
                               placeholder="e.g., Admission Open 2024">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Notice Subtitle</label>
                        <textarea name="subtitle" rows="2" maxlength="160"
                                  class="w-full bg-zinc-50 border-zinc-100 rounded-2xl p-4 text-sm font-bold text-brand-dark focus:border-brand-gold focus:ring-4 focus:ring-brand-gold/10 transition-all" 
                                  placeholder="Brief description of the notice...">{{ old('subtitle') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Notice Badge</label>
                        <input type="text" name="badge" value="{{ old('badge') }}" maxlength="20"
                               class="w-full bg-zinc-50 border-zinc-100 rounded-2xl p-4 text-sm font-bold text-brand-dark focus:border-brand-gold focus:ring-4 focus:ring-brand-gold/10 transition-all" 
                               placeholder="e.g., ANNOUNCEMENT">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Status</label>
                        <select name="status" class="w-full bg-zinc-50 border-zinc-100 rounded-2xl p-4 text-sm font-bold text-brand-dark focus:border-brand-gold focus:ring-4 focus:ring-brand-gold/10 transition-all">
                            <option value="active">Active (Visible)</option>
                            <option value="inactive">Inactive (Hidden)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 bg-zinc-50 rounded-[2rem] border border-zinc-100">
                    <div class="md:col-span-2">
                        <h4 class="text-[11px] font-black uppercase text-brand-dark tracking-widest mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-brand-gold"></i> Scheduling & Intelligence
                        </h4>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-3">Display Style</label>
                        <select name="display_type" class="w-full bg-white border-zinc-200 rounded-2xl p-4 text-sm font-bold text-brand-dark focus:border-brand-gold focus:ring-4 focus:ring-brand-gold/10 transition-all">
                            <option value="popup">Pop-up Modal (Standard)</option>
                            <option value="bar">Sticky Top Bar (Global)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-zinc-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-gold"></div>
                            <span class="ms-3 text-[10px] font-black uppercase text-neutral-600">High Urgency</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-4">Notice Graphic</label>
                    <div class="relative group cursor-pointer mb-6">
                        <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">
                        <div onclick="document.getElementById('imageInput').click()" 
                             class="w-full aspect-[16/9] bg-zinc-50 rounded-3xl border-2 border-dashed border-zinc-200 flex flex-col items-center justify-center gap-3 group-hover:border-brand-gold/40 hover:bg-brand-gold/5 transition-all overflow-hidden relative">
                            <img id="preview" class="absolute inset-0 w-full h-full object-cover hidden">
                            <i class="fa fa-cloud-upload-alt text-4xl text-zinc-300 group-hover:text-brand-gold transition-all"></i>
                            <span class="text-[10px] font-black uppercase text-neutral-400 group-hover:text-brand-dark transition-all">Click to upload graphic</span>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-4 bg-zinc-50 p-6 rounded-2xl border border-zinc-100">
                        <span class="text-[10px] font-black uppercase text-neutral-400 shrink-0">OR Library Path</span>
                        <input type="text" name="image_path" id="image_path" placeholder="site/img/carousel-1.png" class="w-full bg-white border-zinc-200 rounded-xl p-3 text-xs font-mono text-neutral-600 focus:border-brand-gold focus:ring-0">
                        <button type="button" onclick="openMediaVault('image_path', 'preview')" class="w-full sm:w-auto text-[10px] font-black text-white bg-brand-dark px-6 py-3 rounded-xl uppercase hover:bg-brand-gold hover:text-brand-dark shadow-lg transition-all whitespace-nowrap">
                            Vault Picker
                        </button>
                    </div>
                </div>
            </div>

            <x-seo-aeo-fields />
            <div class="flex justify-end pt-4">
                <button type="submit" class="bg-brand-dark text-white px-12 py-5 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-brand-gold hover:text-brand-dark shadow-2xl transition-all animate-glow">
                    Deploy Notice <i class="fas fa-paper-plane ms-2"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('imageInput').onchange = function (evt) {
            const [file] = this.files;
            if (file) {
                const preview = document.getElementById('preview');
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
        }
    </script>
</x-layouts::app>

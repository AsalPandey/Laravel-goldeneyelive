<x-layouts::app :title="__('Modify Notice')">
    <div class="flex h-full w-full flex-1 flex-col gap-8 p-8 max-w-4xl mx-auto">
        <div class="flex items-center gap-4 pb-4 border-b border-neutral-100">
            <a href="{{ route('admin.notices.index') }}" class="p-3 rounded-xl bg-white border border-neutral-100 text-neutral-400 hover:text-orange-600 transition-all">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div class="space-y-1">
                <h1 class="text-3xl font-black text-neutral-900 tracking-tight uppercase">Edit <span class="text-orange-600">Notice</span></h1>
                <p class="text-neutral-500 text-sm">Update or swap the announcement graphic for the homepage.</p>
            </div>
        </div>

        <form action="{{ route('admin.notices.update', $notice->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf @method('PUT')
            
            <div class="bg-white rounded-3xl border border-neutral-100 p-8 shadow-sm space-y-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Notice Title <span class="font-normal text-neutral-300">(Max 60 chars)</span></label>
                    <input type="text" name="title" required value="{{ old('title', $notice->title) }}" maxlength="60"
                           class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all" 
                           placeholder="e.g., Admission Open 2024">
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Notice Subtitle <span class="font-normal text-neutral-300">(Max 160 chars)</span></label>
                    <textarea name="subtitle" rows="2" maxlength="160"
                              class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all" 
                              placeholder="Brief description of the notice...">{{ old('subtitle', $notice->subtitle) }}</textarea>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Notice Badge <span class="font-normal text-neutral-300">(e.g., URGENT, EVENT, OFFER)</span></label>
                    <input type="text" name="badge" value="{{ old('badge', $notice->badge) }}" maxlength="20"
                           class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all" 
                           placeholder="e.g., ANNOUNCEMENT">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Status</label>
                        <select name="status" class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all">
                            <option value="active" {{ $notice->status === 'active' ? 'selected' : '' }}>Active (Visible)</option>
                            <option value="inactive" {{ $notice->status === 'inactive' ? 'selected' : '' }}>Inactive (Hidden)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Button Action Text</label>
                        <input type="text" name="button_text" value="{{ old('button_text', $notice->button_text ?? 'Join Now') }}" 
                               class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all" 
                               placeholder="e.g., Register Now">
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Target Destination Link (Optional)</label>
                    <input type="text" name="link" value="{{ old('link', $notice->link) }}" 
                           class="w-full bg-neutral-50 border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all" 
                           placeholder="e.g., /courses/web-development or https://facebook.com/...">
                    <p class="text-[10px] text-neutral-400 mt-2 italic">If left blank, it will default to the Join Now page.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-neutral-50 rounded-3xl border border-neutral-100">
                    <div class="md:col-span-2">
                        <h4 class="text-[11px] font-black uppercase text-neutral-900 tracking-widest mb-4 flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-orange-600"></i> Scheduling & Display
                        </h4>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Starts At (Optional)</label>
                        <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $notice->starts_at ? \Carbon\Carbon::parse($notice->starts_at)->format('Y-m-d\TH:i') : '') }}" 
                               class="w-full bg-white border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Expires At (Optional)</label>
                        <input type="datetime-local" name="expires_at" value="{{ old('expires_at', $notice->expires_at ? \Carbon\Carbon::parse($notice->expires_at)->format('Y-m-d\TH:i') : '') }}" 
                               class="w-full bg-white border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Display Style</label>
                        <select name="display_type" class="w-full bg-white border-none rounded-xl p-4 text-sm font-bold text-neutral-900 focus:ring-2 focus:ring-orange-500 transition-all">
                            <option value="popup" {{ $notice->display_type === 'popup' ? 'selected' : '' }}>Pop-up Modal (Standard)</option>
                            <option value="bar" {{ $notice->display_type === 'bar' ? 'selected' : '' }}>Sticky Top Bar (Global)</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent', $notice->is_urgent) ? 'checked' : '' }} id="urgentCheck"
                               class="w-5 h-5 rounded border-neutral-300 text-orange-600 focus:ring-orange-500">
                        <label for="urgentCheck" class="text-xs font-black uppercase text-neutral-600 cursor-pointer">Mark as High-Urgency</label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-neutral-400 mb-2">Update Notice Graphic (Optional)</label>
                    <div class="relative group cursor-pointer mb-4">
                        <input type="file" name="image" id="imageInput" class="hidden" accept="image/*">
                        <div onclick="document.getElementById('imageInput').click()" 
                             class="w-full aspect-[16/9] bg-neutral-50 rounded-2xl border-2 border-dashed border-neutral-200 flex flex-col items-center justify-center gap-3 group-hover:border-orange-300 transition-all overflow-hidden relative">
                            <img id="preview" src="{{ asset($notice->image) }}" onerror="this.src='{{ asset('site/img/carousel-1.png') }}'" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all">
                                <i class="fa fa-sync-alt text-3xl text-white"></i>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 bg-neutral-50 p-4 rounded-xl">
                        <span class="text-[10px] font-black uppercase text-neutral-400 shrink-0">OR Library Path</span>
                        <input type="text" name="image_path" value="{{ $notice->image }}" placeholder="site/img/carousel-1.png" class="flex-1 bg-white border-none rounded-lg p-2 text-xs font-mono text-neutral-600 focus:ring-1 focus:ring-orange-500">
                        <button type="button" onclick="openMediaVault('image_path', 'preview')" class="text-[10px] font-black text-white bg-neutral-900 px-4 py-2 rounded-lg uppercase hover:bg-orange-600 transition-all">
                            Pick from Library
                        </button>
                    </div>
                    <p class="text-[10px] text-neutral-400 mt-2 italic font-medium">Leave empty to keep the current graphic. Max 2MB.</p>
                </div>
            </div>

            <x-seo-aeo-fields :model="$notice" />
                <button type="submit" class="bg-neutral-900 text-white px-10 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-orange-600 shadow-xl transition-all">
                    Update Notice
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
            }
        }
    </script>
</x-layouts::app>

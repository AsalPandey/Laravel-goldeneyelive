<x-layouts::app :title="__('Website Brand Authority')">
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
        .brand-hub-tabs button.active {
            border-bottom: 4px solid var(--color-brand-gold);
            color: var(--color-brand-gold);
            font-weight: 900;
        }
        .brand-hub-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .branding-tab-content { display: none; }
        .branding-tab-content.active { display: block; }
        
        .premium-input {
            width: 100%; border: 1px solid #e2e8f0; border-radius: 14px; height: 48px; padding: 0 18px; font-size: 14px; transition: 0.2s;
            background: #fff;
        }
        .premium-input:focus { outline: none; border-color: var(--color-brand-gold); box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.1); }
        .premium-label { font-size: 11px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.8px; }
        
        .brand-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease;
        }
        .brand-card:hover { transform: translateY(-4px); }
        .section-icon {
            width: 48px; height: 48px; border-radius: 12px; background: var(--color-brand-dark); color: var(--color-brand-gold);
            display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 20px;
        }
        .helper-text { font-size: 11px; color: #94a3b8; margin-top: 6px; font-style: italic; }
    </style>

    <div class="max-w-7xl mx-auto p-8 space-y-10">
        {{-- Authority Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-100">
            <div>
                <h1 class="text-4xl font-black text-zinc-900 tracking-tighter uppercase leading-none">Branding <span class="text-brand-gold">Authority</span></h1>
                <p class="text-zinc-500 text-sm mt-3 font-medium">Simplify how you manage the academy's core identity, visuals, and global settings.</p>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="px-6 py-3 bg-zinc-100 text-zinc-600 rounded-2xl font-black text-xs uppercase hover:bg-zinc-200 transition-all">Live Preview</a>
                <button form="brandingForm" type="submit" class="px-10 py-3 bg-brand-dark text-brand-gold rounded-2xl font-black text-xs uppercase shadow-2xl hover:bg-brand-gold hover:text-brand-dark transition-all transform active:scale-95">Deploy Brand Update</button>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div class="brand-hub-tabs flex gap-10 border-b border-zinc-100 overflow-x-auto no-scrollbar">
            <button onclick="switchTab('visuals')" id="tabBtn-visuals" class="active pb-5 px-2 text-xs font-black uppercase tracking-[2px] whitespace-nowrap transition-all">Core Visuals</button>
            <button onclick="switchTab('contact')" id="tabBtn-contact" class="pb-5 px-2 text-xs font-black uppercase tracking-[2px] whitespace-nowrap transition-all">Contact & Socials</button>
            <button onclick="switchTab('content')" id="tabBtn-content" class="pb-5 px-2 text-xs font-black uppercase tracking-[2px] whitespace-nowrap transition-all">Page Builder</button>
            <button onclick="switchTab('marketing')" id="tabBtn-marketing" class="pb-5 px-2 text-xs font-black uppercase tracking-[2px] whitespace-nowrap transition-all">Marketing Tools</button>
            <button onclick="switchTab('vault')" id="tabBtn-vault" class="pb-5 px-2 text-xs font-black uppercase tracking-[2px] whitespace-nowrap transition-all">Media Library</button>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-10">
            {{-- Visual Identity Preview --}}
            <div class="xl:col-span-2 bg-zinc-900 rounded-[32px] p-8 text-white relative overflow-hidden shadow-2xl border border-zinc-800">
                <div class="absolute top-0 right-0 p-10 opacity-10 pointer-events-none">
                    <i class="fa fa-university text-[120px]"></i>
                </div>
                <div class="relative z-10">
                    <span class="inline-block px-4 py-1.5 bg-brand-gold/20 text-brand-gold rounded-full text-[10px] font-black uppercase tracking-widest mb-6">Live Identity Preview</span>
                    <div class="flex flex-col md:flex-row items-center gap-10">
                        <div class="w-32 h-32 bg-white rounded-3xl p-4 flex items-center justify-center shadow-2xl border-4 border-brand-gold/20">
                            <img src="{{ asset($settings['site_logo'] ?? 'site/img/logo.png') }}" class="max-w-full max-h-full object-contain">
                        </div>
                        <div class="flex-1 text-center md:text-left">
                            <h2 class="text-3xl font-black tracking-tighter leading-none mb-2">{{ $settings['site_name'] ?? 'GoldenEye' }} {{ $settings['site_name_suffix'] ?? 'Academy' }}</h2>
                            <p class="text-zinc-400 text-sm font-medium italic">"{{ Str::limit($settings['meta_description'] ?? 'Tagline not set...', 80) }}"</p>
                            <div class="mt-6 flex flex-wrap gap-4 justify-center md:justify-start">
                                <span class="text-[10px] font-bold text-zinc-500 uppercase flex items-center gap-2"><i class="fa fa-envelope text-[#C5A059]"></i> {{ $settings['site_email'] ?? 'Not Set' }}</span>
                                <span class="text-[10px] font-bold text-zinc-500 uppercase flex items-center gap-2"><i class="fa fa-phone text-[#C5A059]"></i> {{ $settings['site_phone'] ?? 'Not Set' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats Card --}}
            <div class="bg-white rounded-[32px] border border-zinc-100 p-8 shadow-sm flex flex-col justify-center">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-zinc-400 tracking-widest">Brand Completeness</span>
                        <span class="text-lg font-black text-brand-gold">{{ $brandCompleteness }}%</span>
                    </div>
                    <div class="w-full h-2 bg-zinc-100 rounded-full overflow-hidden">
                        <div class="h-full bg-brand-gold" style="width: {{ $brandCompleteness }}%"></div>
                    </div>
                    <p class="text-[10px] text-zinc-500 leading-relaxed font-medium">All core visuals, social hubs, and SEO metadata are synchronized with the live production environment.</p>
                </div>
            </div>
        </div>

        <form id="brandingForm" action="{{ route('admin.branding.update') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- TAB: CORE VISUALS --}}
            <div id="tab-visuals" class="branding-tab-content active space-y-10">
                <div class="guide-box">
                    <div class="guide-title"><i class="fas fa-info-circle"></i> Branding Guide: Visual Identity</div>
                    <p class="guide-text">Manage your institution's core visuals here. The **Logo** appears in the header and emails, while the **Favicon** is the small icon in browser tabs. Use the **Media Library** tab to pick from existing files if you've already uploaded them.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="brand-card">
                        <div class="section-icon"><i class="fa fa-fingerprint"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Identity Hub</h3>
                        <p class="text-sm text-zinc-500 mb-8">Manage the core visual markers of the academy.</p>
                        
                        <div class="space-y-8">
                            <div>
                                <label class="premium-label">Official Institution Logo</label>
                                <div class="flex items-center gap-6 p-6 bg-zinc-50 rounded-2xl border border-zinc-100">
                                    <div class="w-20 h-20 bg-white rounded-xl shadow-sm flex items-center justify-center p-2 border border-zinc-200">
                                        <img src="{{ asset($settings['site_logo'] ?? 'site/img/logo.png') }}" 
                                             onerror="this.src='{{ asset('site/img/logo.png') }}'"
                                             class="max-w-full max-h-full object-contain">
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <input type="file" name="site_logo" class="text-xs">
                                        <div class="flex gap-2">
                                            <input type="text" name="site_logo_path" id="input_site_logo" value="{{ $settings['site_logo'] ?? '' }}" class="premium-input h-10 text-[10px] py-1 px-3 flex-1" placeholder="Or select from library...">
                                            <button type="button" onclick="openPicker('input_site_logo')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[9px] font-black uppercase hover:bg-[#C5A059] hover:text-white transition-all whitespace-nowrap">
                                                <i class="fa fa-images"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($isAdmin)
                                <div>
                                    <label class="premium-label">Institution Name</label>
                                    <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'GoldenEye' }}" class="premium-input font-black text-[#050C1C]" placeholder="e.g. GoldenEye">
                                </div>
                                <div>
                                    <label class="premium-label">Brand Suffix</label>
                                    <input type="text" name="site_name_suffix" value="{{ $settings['site_name_suffix'] ?? 'Academy' }}" class="premium-input" placeholder="e.g. Academy">
                                </div>
                                @else
                                <div class="col-span-2 p-4 bg-zinc-50 rounded-xl border border-dashed border-zinc-200">
                                    <p class="text-[10px] font-black uppercase text-zinc-400 text-center"><i class="fa fa-lock mr-2"></i> Institution name settings restricted to Admin</p>
                                </div>
                                @endif
                            </div>

                            <div>
                                <label class="premium-label">Logo Location Subtitle</label>
                                <input type="text" name="logo_subtitle" value="{{ $settings['logo_subtitle'] ?? 'Pokhara — Srijanachowk' }}" class="premium-input" placeholder="e.g. Pokhara — Nepal">
                                <p class="helper-text">Appears below the Academy name in the header.</p>
                            </div>

                            <div class="pt-6 border-t border-zinc-100">
                                <label class="premium-label">Browser Favicon</label>
                                <div class="flex items-center gap-6 p-6 bg-zinc-50 rounded-2xl border border-zinc-100">
                                    <div class="w-16 h-16 bg-white rounded-xl shadow-sm flex items-center justify-center p-2 border border-zinc-200">
                                        <img src="{{ asset($settings['site_favicon'] ?? 'site/img/logo.png') }}" 
                                             onerror="this.src='{{ asset('site/img/logo.png') }}'"
                                             class="w-8 h-8 object-contain">
                                    </div>
                                    <div class="flex-1 space-y-2">
                                        <input type="file" name="site_favicon" class="text-xs">
                                        <div class="flex gap-2">
                                            <input type="text" name="site_favicon_path" id="input_site_favicon" value="{{ $settings['site_favicon'] ?? '' }}" class="premium-input h-10 text-[10px] py-1 px-3 flex-1" placeholder="Or select from library...">
                                            <button type="button" onclick="openPicker('input_site_favicon')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[9px] font-black uppercase hover:bg-[#C5A059] hover:text-white transition-all whitespace-nowrap">
                                                <i class="fa fa-images"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="brand-card">
                        <div class="section-icon bg-zinc-900"><i class="fa fa-heading text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Authority Headers</h3>
                        <p class="text-sm text-zinc-500 mb-8">Manage authority headers across the landing page.</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="premium-label">Hero Gold Badge Text</label>
                                <input type="text" name="hero_badge_text" value="{{ $settings['hero_badge_text'] ?? 'Official Global Academy' }}" class="premium-input">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="premium-label">Hero CTA 1 (Primary)</label>
                                    <input type="text" name="hero_cta_1_text" value="{{ $settings['hero_cta_1_text'] ?? 'Ask for Course Help' }}" class="premium-input">
                                </div>
                                <div>
                                    <label class="premium-label">Hero CTA 2 (Secondary)</label>
                                    <input type="text" name="hero_cta_2_text" value="{{ $settings['hero_cta_2_text'] ?? 'See Courses' }}" class="premium-input">
                                </div>
                            </div>
                            <div>
                                <label class="premium-label">About Section Title</label>
                                <input type="text" name="about_section_title" value="{{ $settings['about_section_title'] ?? 'Why GoldenEye?' }}" class="premium-input">
                            </div>
                            <div>
                                <label class="premium-label">Testimonials Title</label>
                                <input type="text" name="testimonials_title" value="{{ $settings['testimonials_title'] ?? 'Join 5,000+ Success Stories' }}" class="premium-input">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                    <div class="brand-card">
                        <div class="section-icon"><i class="fa fa-sliders-h"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Interface Labels</h3>
                        <p class="text-sm text-zinc-500 mb-8">Customize interface and interaction text.</p>
                        
                        <div class="space-y-6">
                            <div>
                                <label class="premium-label">Mobile Menu Label</label>
                                <input type="text" name="navbar_menu_label" value="{{ $settings['navbar_menu_label'] ?? 'MENU' }}" class="premium-input" placeholder="e.g. EXPLORE">
                            </div>
                            <div>
                                <label class="premium-label">Footer FAQ Heading</label>
                                <input type="text" name="footer_faq_title" value="{{ $settings['footer_faq_title'] ?? 'Academic Guide' }}" class="premium-input">
                            </div>
                            <div>
                                <label class="premium-label">WhatsApp CTA Text</label>
                                <input type="text" name="whatsapp_cta_text" value="{{ $settings['whatsapp_cta_text'] ?? $settings['whatsapp_button_text'] ?? 'Message us on WhatsApp' }}" class="premium-input" placeholder="Message us on WhatsApp">
                                <input type="text" name="whatsapp_cta_subtext" value="{{ $settings['whatsapp_cta_subtext'] ?? 'Casual questions. Quick reply.' }}" class="premium-input mt-3" placeholder="Casual questions. Quick reply.">
                                <textarea name="whatsapp_prefill_message" rows="2" class="premium-input h-auto py-3 mt-3" placeholder="Prefilled WhatsApp message">{{ $settings['whatsapp_prefill_message'] ?? 'Hi GoldenEye Academy, I have a quick question. Can you help me choose the right course?' }}</textarea>
                            </div>
                            <div class="pt-6 border-t border-zinc-100">
                                <label class="premium-label">Sticky Inquiry Labels</label>
                                <div class="space-y-4">
                                    <input type="text" name="inquiry_title" value="{{ $settings['inquiry_title'] ?? 'Quick Inquiry' }}" class="premium-input" placeholder="Title">
                                    <input type="text" name="inquiry_tab_text" value="{{ $settings['inquiry_tab_text'] ?? 'Quick Inquiry' }}" class="premium-input" placeholder="Tab Label">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="brand-card">
                        <div class="section-icon bg-emerald-600"><i class="fa fa-chart-bar text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Success Metrics</h3>
                        <p class="text-sm text-zinc-500 mb-8">Numbers shown in the trust bar.</p>
                        
                        <div class="grid grid-cols-1 gap-4">
                            @for($i=1; $i<=4; $i++)
                            <div class="p-3 bg-zinc-50 rounded-2xl border border-zinc-100">
                                <label class="premium-label text-[9px]">Stat {{ $i }}</label>
                                <input type="text" name="stat_{{ $i }}_val" value="{{ $settings['stat_'.$i.'_val'] ?? '' }}" class="premium-input h-10 mb-2 font-black text-[#C5A059] text-xs" placeholder="Value (e.g. 5,000+)">
                                <input type="text" name="stat_{{ $i }}_lab" value="{{ $settings['stat_'.$i.'_lab'] ?? '' }}" class="premium-input h-8 text-[8px] uppercase tracking-wider" placeholder="Label (e.g. Graduates)">
                            </div>
                            @endfor
                        </div>
                    </div>

                    <div class="brand-card lg:col-span-1">
                        <div class="section-icon bg-indigo-600"><i class="fa fa-mountain text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Main Hero Graphic</h3>
                        <p class="text-sm text-zinc-500 mb-8">Central visual for the homepage banner.</p>
                        
                        <div class="space-y-6">
                            <div>
                                <div class="relative rounded-2xl overflow-hidden border-4 border-zinc-50 shadow-inner group">
                                    <img src="{{ asset($settings['hero_image'] ?? 'site/img/carousel-1.png') }}" 
                                         onerror="this.src='{{ asset('site/img/carousel-1.png') }}'"
                                         class="w-full h-40 object-cover group-hover:scale-105 transition-transform">
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <label class="px-4 py-2 bg-white rounded-lg text-xs font-black uppercase cursor-pointer">Change Image
                                            <input type="file" name="hero_image" class="hidden">
                                        </label>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <label class="premium-label">Photo Reference (Vault Path)</label>
                                    <div class="flex gap-2">
                                        <input type="text" name="hero_image_path" id="input_hero_image" value="{{ $settings['hero_image'] ?? '' }}" class="premium-input h-10 text-xs font-mono bg-transparent flex-1" placeholder="site/img/carousel-1.png">
                                        <button type="button" onclick="openPicker('input_hero_image')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all whitespace-nowrap">
                                            <i class="fa fa-images mr-1"></i> Browse
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="brand-card lg:col-span-3">
                        <div class="section-icon bg-zinc-950"><i class="fa fa-magic text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Hero Text & CTA</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div>
                                <label class="premium-label">Main Heading</label>
                                <input type="text" name="hero_title" value="{{ $settings['hero_title'] ?? '' }}" class="premium-input h-14 text-lg font-black" placeholder="e.g. Empowering Your Future">
                            </div>
                            <div>
                                <label class="premium-label">Primary Button Text</label>
                                <input type="text" name="hero_cta_text" value="{{ $settings['hero_cta_text'] ?? 'Enroll Now' }}" class="premium-input h-14 font-black" placeholder="e.g. Get Started">
                            </div>
                            <div class="md:col-span-2">
                                <label class="premium-label">Hero Sub-Heading (Rich Text)</label>
                                <textarea name="hero_subtitle" id="editor_hero_subtitle" rows="3" class="premium-input h-auto py-3">{{ $settings['hero_subtitle'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: PAGE CONTENT --}}
            <div id="tab-content" class="branding-tab-content space-y-10">
                <div class="guide-box">
                    <div class="guide-title"><i class="fas fa-info-circle"></i> Branding Guide: Page Builder</div>
                    <p class="guide-text">Select a page from the dropdown to edit its specific contents. **Tip:** After editing rich text fields (like the About message), use the "Deploy Brand Update" button at the bottom to save all changes at once.</p>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white p-8 rounded-[32px] border border-zinc-100 shadow-sm">
                    <div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Dynamic Page Builder</h3>
                        <p class="text-sm text-zinc-500">Edit content for individual pages across the academy site.</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <button type="button" onclick="initVisibleEditor()" class="text-[10px] font-black uppercase text-zinc-400 hover:text-[#C5A059] transition-all flex items-center gap-1">
                            <i class="fa fa-sync-alt"></i> Visual Refresh
                        </button>
                        <select id="pageEditorSelector" onchange="switchPageEditor(this.value)" class="premium-input w-72 h-12 text-[10px] font-black uppercase border-zinc-100 shadow-sm focus:ring-4 focus:ring-[#C5A059]/10">
                            <option value="page-home-about">🏠 Home & About Summaries</option>
                            <option value="page-about-full">🏢 About Us (Full Details)</option>
                            <option value="page-courses">🎓 Courses Catalogue</option>
                            <option value="page-blog">✍️ Academy Blog</option>
                            <option value="page-faq">❓ FAQ Page Intro</option>
                            <option value="page-contact">📞 Contact Page Content</option>
                            <option value="page-privacy">🛡️ Privacy Policy</option>
                            <option value="page-terms">📜 Terms & Conditions</option>
                        </select>
                        <a id="pagePreviewBtn" href="{{ route('home') }}" target="_blank" class="px-6 py-3 bg-zinc-100 text-zinc-600 rounded-2xl font-black text-[9px] uppercase hover:bg-zinc-200 transition-all border border-zinc-200">
                            <i class="fas fa-external-link-alt mr-2"></i> View Live
                        </a>
                    </div>
                </div>

                {{-- Page: Home/About Summary --}}
                <div id="page-home-about" class="page-editor-section" style="display: block;">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <div class="brand-card">
                            <div class="section-icon bg-zinc-800"><i class="fas fa-id-card text-white"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">About Summary Card</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="premium-label">Summary Photo</label>
                                    <div class="relative rounded-2xl overflow-hidden border-4 border-zinc-50 shadow-inner group">
                                        <img src="{{ asset($settings['about_image'] ?? 'site/img/about.jpg') }}" 
                                             onerror="this.src='{{ asset('site/img/carousel-1.png') }}'"
                                             class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <label class="px-4 py-2 bg-white rounded-lg text-xs font-black uppercase cursor-pointer">Change Image
                                                <input type="file" name="about_image" class="hidden">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <label class="premium-label">Photo Reference (Vault Path)</label>
                                        <div class="flex gap-2">
                                            <input type="text" name="about_image_path" id="input_about_image" value="{{ $settings['about_image'] ?? '' }}" class="premium-input h-10 text-xs font-mono bg-transparent flex-1" placeholder="site/img/about.jpg">
                                            <button type="button" onclick="openPicker('input_about_image')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all whitespace-nowrap">
                                                <i class="fa fa-images"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="premium-label">Section Title</label>
                                    <input type="text" name="about_title" value="{{ $settings['about_title'] ?? '' }}" class="premium-input" placeholder="e.g. Welcome to GoldenEye">
                                </div>
                                <div>
                                    <label class="premium-label">Section Summary Text (Rich Text)</label>
                                    <textarea name="about_text" id="editor_about_summary" rows="4" class="premium-input h-auto py-3 leading-relaxed">{{ $settings['about_text'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="brand-card">
                            <div class="section-icon"><i class="fas fa-list-ul"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Core Strengths</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @for($i=1; $i<=9; $i++)
                                    <div>
                                        <label class="premium-label text-[9px]">Point {{ $i }}</label>
                                        <div class="relative">
                                            <i class="fas fa-check-circle absolute left-3 top-1/2 -translate-y-1/2 text-emerald-500 text-xs"></i>
                                            <input type="text" name="about_point_{{ $i }}" value="{{ $settings['about_point_'.$i] ?? '' }}" class="premium-input h-10 text-xs pl-9">
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="brand-card border-l-8 border-[#C5A059] lg:col-span-2">
                            <div class="section-icon"><i class="fa fa-user-tie"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Founder's Vision Section</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-6">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="premium-label">Founder Name</label>
                                            <input type="text" name="founder_name" value="{{ $settings['founder_name'] ?? '' }}" class="premium-input">
                                        </div>
                                        <div>
                                            <label class="premium-label">Designation</label>
                                            <input type="text" name="founder_position" value="{{ $settings['founder_position'] ?? '' }}" class="premium-input">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="premium-label">The Message (Rich Text)</label>
                                        <textarea name="founder_message" id="editor_founder_message" rows="5" class="premium-input h-auto py-4 leading-relaxed">{{ $settings['founder_message'] ?? '' }}</textarea>
                                    </div>
                                </div>
                                <div class="space-y-6">
                                    <div>
                                        <label class="premium-label">Founder Image</label>
                                        <div class="relative rounded-2xl overflow-hidden border-4 border-zinc-50 shadow-inner h-48 mb-4">
                                            <img src="{{ \App\Support\PublicAsset::url($settings['founder_image'] ?? null, 'site/img/message-chairperson.jpg') }}" 
                                                 onerror="this.src='{{ asset('site/img/carousel-1.png') }}'"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex gap-2">
                                            <input type="text" name="founder_image_path" id="input_founder_image" value="{{ $settings['founder_image'] ?? '' }}" class="premium-input h-10 text-xs font-mono bg-transparent flex-1" placeholder="site/img/message-chairperson.jpg">
                                            <button type="button" onclick="openPicker('input_founder_image')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[10px] font-black uppercase hover:bg-orange-600 hover:text-white transition-all whitespace-nowrap">
                                                <i class="fa fa-images"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Page: About Us Full --}}
                <div id="page-about-full" class="page-editor-section" style="display: none;">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <div class="brand-card">
                            <div class="section-icon"><i class="fa fa-info-circle"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">About Page Headers</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="premium-label">Page Title</label>
                                    <input type="text" name="about_header_title" value="{{ $settings['about_header_title'] ?? 'About Us' }}" class="premium-input">
                                </div>
                                <div>
                                    <label class="premium-label">Section Tagline</label>
                                    <input type="text" name="about_section_tagline" value="{{ $settings['about_section_tagline'] ?? 'Empowering Growth' }}" class="premium-input">
                                </div>
                            </div>
                        </div>

                        <div class="brand-card">
                            <div class="section-icon bg-zinc-900"><i class="fa fa-quote-right text-white"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Founder Section Heading</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="premium-label">Small Tagline</label>
                                    <input type="text" name="founder_section_tagline" value="{{ $settings['founder_section_tagline'] ?? 'Leadership Message' }}" class="premium-input">
                                </div>
                                <div>
                                    <label class="premium-label">Main Title</label>
                                    <input type="text" name="founder_section_title" value="{{ $settings['founder_section_title'] ?? 'From Our Founder' }}" class="premium-input">
                                </div>
                            </div>
                        </div>

                        <div class="brand-card lg:col-span-2">
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">MAIN ABOUT PAGE CONTENT</h3>
                            <div class="border rounded-2xl overflow-hidden shadow-sm">
                                <textarea name="about_page_content" id="editor_about" class="w-full min-h-[400px]">{{ $settings['about_page_content'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Page: Courses Catalogue --}}
                <div id="page-courses" class="page-editor-section" style="display: none;">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                        <div class="brand-card">
                            <div class="section-icon"><i class="fa fa-graduation-cap"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Catalogue Header</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="premium-label">Main Heading</label>
                                    <input type="text" name="courses_title" value="{{ $settings['courses_title'] ?? 'Career Pathways' }}" class="premium-input">
                                </div>
                                <div>
                                    <label class="premium-label">Sub-heading (Rich Text)</label>
                                    <textarea name="courses_subtitle" id="editor_courses_subtitle" rows="3" class="premium-input h-auto py-3">{{ $settings['courses_subtitle'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="brand-card">
                            <div class="section-icon bg-orange-600"><i class="fa fa-award text-white"></i></div>
                            <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Course Highlights</h3>
                            <div class="space-y-4">
                                <input type="text" name="career_highlight_1" value="{{ $settings['career_highlight_1'] ?? '' }}" class="premium-input h-10" placeholder="Highlight 1">
                                <input type="text" name="career_highlight_2" value="{{ $settings['career_highlight_2'] ?? '' }}" class="premium-input h-10" placeholder="Highlight 2">
                                <input type="text" name="career_highlight_3" value="{{ $settings['career_highlight_3'] ?? '' }}" class="premium-input h-10" placeholder="Highlight 3">
                                <input type="text" name="career_highlight_4" value="{{ $settings['career_highlight_4'] ?? '' }}" class="premium-input h-10" placeholder="Highlight 4">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Page: Blog --}}
                <div id="page-blog" class="page-editor-section" style="display: none;">
                    <div class="brand-card max-w-4xl">
                        <div class="section-icon"><i class="fa fa-pen-nib"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-6">Blog Header Content</h3>
                        <div class="space-y-6">
                            <div>
                                <label class="premium-label">Main Title</label>
                                <input type="text" name="blog_title" value="{{ $settings['blog_title'] ?? 'Academy Blog' }}" class="premium-input h-12">
                            </div>
                            <div>
                                <label class="premium-label">Sub-heading (Rich Text)</label>
                                <textarea name="blog_subtitle" id="editor_blog_subtitle" rows="3" class="premium-input h-auto py-3">{{ $settings['blog_subtitle'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Page: FAQ --}}
                <div id="page-faq" class="page-editor-section" style="display: none;">
                    <div class="brand-card">
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">FAQ Page Header Content</h3>
                        <div class="border rounded-2xl overflow-hidden">
                            <textarea name="faq_page_content" id="editor_faq" class="w-full min-h-[300px]">{{ $settings['faq_page_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Page: Contact --}}
                <div id="page-contact" class="page-editor-section" style="display: none;">
                    <div class="brand-card">
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Contact Page Introductory Text</h3>
                        <div class="border rounded-2xl overflow-hidden">
                            <textarea name="contact_page_content" id="editor_contact" class="w-full min-h-[300px]">{{ $settings['contact_page_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Page: Privacy --}}
                <div id="page-privacy" class="page-editor-section" style="display: none;">
                    <div class="brand-card">
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Privacy Policy</h3>
                        <div class="border rounded-2xl overflow-hidden">
                            <textarea name="privacy_policy_content" id="editor_privacy" class="w-full min-h-[400px]">{{ $settings['privacy_policy_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Page: Terms --}}
                <div id="page-terms" class="page-editor-section" style="display: none;">
                    <div class="brand-card">
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Terms & Conditions</h3>
                        <div class="border rounded-2xl overflow-hidden">
                            <textarea name="terms_and_conditions_content" id="editor_terms" class="w-full min-h-[400px]">{{ $settings['terms_and_conditions_content'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: CONTACT & SOCIALS --}}
            <div id="tab-contact" class="branding-tab-content space-y-10">
                <div class="guide-box">
                    <div class="guide-title"><i class="fas fa-info-circle"></i> Branding Guide: Connect Hub</div>
                    <p class="guide-text">Keep your institutional contact details updated. **Google Maps:** Paste only the `src` attribute value from the Google Maps iframe share code for best results. **GA ID:** Supports both `G-` and `UA-` formats.</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                    <div class="brand-card">
                        <div class="section-icon bg-emerald-600"><i class="fas fa-phone-alt text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Connect Hub</h3>
                        <div class="space-y-5">
                            <div>
                                <label class="premium-label">WhatsApp Hotline</label>
                                <input type="text" name="whatsapp_number" value="{{ $settings['whatsapp_number'] ?? '' }}" class="premium-input" placeholder="e.g. 9779856...">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="premium-label">Academy Email</label>
                                    <input type="email" name="site_email" value="{{ $settings['site_email'] ?? '' }}" class="premium-input" placeholder="e.g. info@goldeneye.edu.np">
                                </div>
                                <div>
                                    <label class="premium-label">Phone Number</label>
                                    <input type="text" name="site_phone" value="{{ $settings['site_phone'] ?? '' }}" class="premium-input" placeholder="e.g. +977 1 4XXXXXX">
                                </div>
                            </div>
                            <div>
                                <label class="premium-label">Physical Address</label>
                                <input type="text" name="site_address" value="{{ $settings['site_address'] ?? '' }}" class="premium-input" placeholder="e.g. Putalisadak, Kathmandu">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($isAdmin)
                                <div>
                                    <label class="premium-label">Opening Hours</label>
                                    <input type="text" name="opening_hours" value="{{ $settings['opening_hours'] ?? '' }}" class="premium-input" placeholder="e.g. Sun-Fri, 7AM-6PM">
                                </div>
                                <div>
                                    <label class="premium-label">Geo Latitude</label>
                                    <input type="text" name="geo_latitude" value="{{ $settings['geo_latitude'] ?? '' }}" class="premium-input text-xs" placeholder="e.g. 28.2163">
                                </div>
                                <div>
                                    <label class="premium-label">Geo Longitude</label>
                                    <input type="text" name="geo_longitude" value="{{ $settings['geo_longitude'] ?? '' }}" class="premium-input text-xs" placeholder="e.g. 83.9823">
                                </div>
                                <div>
                                    <label class="premium-label">Maps Embed URL (src only)</label>
                                    <input type="text" name="google_maps_embed" value="{{ $settings['google_maps_embed'] ?? '' }}" class="premium-input" placeholder="https://google.com/maps/embed?...">
                                </div>
                                @else
                                <div class="col-span-2">
                                    <label class="premium-label">Opening Hours</label>
                                    <input type="text" name="opening_hours" value="{{ $settings['opening_hours'] ?? '' }}" class="premium-input" placeholder="e.g. Sun-Fri, 7AM-6PM">
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="brand-card">
                        <div class="section-icon bg-blue-600"><i class="fa fa-fingerprint text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Digital Footprint</h3>
                        <div class="space-y-6">
                            @if($isAdmin)
                            <div>
                                <label class="premium-label text-blue-600">Google Analytics ID</label>
                                <input type="text" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="premium-input bg-zinc-50 border-dashed" placeholder="G-XXXXXXXXXX">
                            </div>
                            @endif
                            <div>
                                <label class="premium-label">Meta Keywords</label>
                                <textarea name="meta_keywords" id="meta_keywords" rows="2" class="premium-input h-auto py-3">{{ $settings['meta_keywords'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="premium-label">Meta Description</label>
                                <textarea name="meta_description" id="meta_description" rows="3" class="premium-input h-auto py-3">{{ $settings['meta_description'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="premium-label">Footer Newsletter Description</label>
                                <input type="text" name="footer_newsletter_desc" value="{{ $settings['footer_newsletter_desc'] ?? '' }}" class="premium-input" placeholder="e.g. Sign up for career insights...">
                            </div>
                            <div>
                                <label class="premium-label">Footer Bio / Summary (Rich Text)</label>
                                <textarea name="footer_about_text" id="editor_footer_bio" rows="3" class="premium-input h-auto py-3">{{ $settings['footer_about_text'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="brand-card lg:col-span-2">
                        <h3 class="text-xs font-black uppercase text-zinc-400 mb-6">Social Connections</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(['facebook', 'instagram', 'linkedin', 'youtube'] as $plat)
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-zinc-100 flex items-center justify-center shrink-0"><i class="fab fa-{{ $plat }}"></i></div>
                                    <input type="url" name="{{ $plat }}_url" value="{{ $settings[$plat.'_url'] ?? '' }}" class="premium-input" placeholder="{{ ucfirst($plat) }} URL">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="brand-card lg:col-span-2">
                        <div class="section-icon bg-zinc-900"><i class="fab fa-google text-white"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">External Review Proof</h3>
                        <p class="text-sm text-zinc-500 mb-8">Add a verified Google Business Profile link or a review screenshot so public trust sections can show external proof without fake rating claims.</p>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label class="premium-label">Google Business Profile URL</label>
                                <input type="url" name="google_business_profile_url" value="{{ $settings['google_business_profile_url'] ?? '' }}" class="premium-input" placeholder="https://g.page/r/...">
                            </div>
                            <div>
                                <label class="premium-label">Review Screenshot Path</label>
                                <div class="flex gap-2">
                                    <input type="text" name="external_review_screenshot_path" id="input_external_review_screenshot" value="{{ $settings['external_review_screenshot'] ?? '' }}" class="premium-input flex-1" placeholder="site/img/reviews/google-review.png">
                                    <button type="button" onclick="openPicker('input_external_review_screenshot')" class="bg-zinc-800 text-[#C5A059] px-4 rounded-xl text-[9px] font-black uppercase hover:bg-[#C5A059] hover:text-white transition-all whitespace-nowrap">
                                        <i class="fa fa-images"></i>
                                    </button>
                                </div>
                                <input type="file" name="external_review_screenshot" class="text-xs mt-3">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="premium-label">Public Review Proof Note</label>
                                <textarea name="external_review_proof_note" rows="3" class="premium-input h-auto py-3" placeholder="Explain how students or parents can verify reviews.">{{ $settings['external_review_proof_note'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAB: MARKETING --}}
            <div id="tab-marketing" class="branding-tab-content space-y-10">
                <div class="guide-box">
                    <div class="guide-title"><i class="fas fa-info-circle"></i> Branding Guide: Marketing Tools</div>
                    <p class="guide-text">Manage the **Flash Notice** (floating popup) and the **Sticky Conversion Bar**. **reCAPTCHA:** Keys are required for contact forms to prevent spam. Keep the **Image Upload Limit** around 2048KB for optimal performance.</p>
                </div>
                <div class="brand-card max-w-4xl bg-orange-50/30 border-orange-100">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="p-3 bg-zinc-950 text-[#C5A059] rounded-2xl"><i class="fa fa-bullhorn"></i></div>
                        <h3 class="text-xl font-black uppercase text-zinc-800">Flash Notice & Sticky Conversion</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="space-y-6">
                            <div>
                                <label class="premium-label">Visibility Status</label>
                                <select name="popup_status" class="premium-input font-black uppercase">
                                    <option value="active" {{ ($settings['popup_status'] ?? '') === 'active' ? 'selected' : '' }}>ACTIVE</option>
                                    <option value="inactive" {{ ($settings['popup_status'] ?? '') === 'inactive' ? 'selected' : '' }}>HIDDEN</option>
                                </select>
                            </div>
                            <div>
                                <label class="premium-label">Graphic Asset</label>
                                <div class="aspect-square bg-white rounded-2xl border-4 border-white shadow-sm overflow-hidden mb-4">
                                    <img src="{{ asset($settings['popup_image'] ?? 'site/img/carousel-1.png') }}" class="w-full h-full object-cover">
                                </div>
                                <div class="flex gap-2">
                                    <input type="text" name="popup_image_path" id="input_popup_image" value="{{ $settings['popup_image'] ?? '' }}" class="premium-input h-10 text-xs flex-1">
                                    <button type="button" onclick="openPicker('input_popup_image')" class="bg-zinc-800 text-[#C5A059] px-3 rounded-xl text-[9px] font-black uppercase">Pick</button>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2 space-y-6">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="premium-label">Notice Title</label>
                                    <input type="text" name="popup_title" value="{{ $settings['popup_title'] ?? '' }}" class="premium-input">
                                </div>
                                <div>
                                    <label class="premium-label">Button Text</label>
                                    <input type="text" name="popup_button_text" value="{{ $settings['popup_button_text'] ?? '' }}" class="premium-input">
                                </div>
                            </div>
                            <div>
                                <label class="premium-label">Link</label>
                                <input type="text" name="popup_register_link" value="{{ $settings['popup_register_link'] ?? '' }}" class="premium-input">
                            </div>
                            <div>
                                <label class="premium-label">Subtitle (Marketing Copy)</label>
                                <textarea name="popup_subtitle" rows="3" class="premium-input h-auto py-3">{{ $settings['popup_subtitle'] ?? '' }}</textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="premium-label">Notice Badge</label>
                                    <input type="text" name="notice_badge_text" value="{{ $settings['notice_badge_text'] ?? 'Important Intake' }}" class="premium-input h-10">
                                </div>
                                <div>
                                    <label class="premium-label">Dismiss Label</label>
                                    <input type="text" name="notice_dismiss_text" value="{{ $settings['notice_dismiss_text'] ?? 'Dismiss' }}" class="premium-input h-10">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-10 border-t border-orange-100 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-xs font-black uppercase text-orange-900 mb-6">Sticky Conversion Bar</h4>
                            <div class="space-y-4">
                                <input type="text" name="sticky_cta_badge" value="{{ $settings['sticky_cta_badge'] ?? '' }}" class="premium-input" placeholder="Badge (e.g. New Intake)">
                                <input type="text" name="sticky_cta_text" value="{{ $settings['sticky_cta_text'] ?? '' }}" class="premium-input" placeholder="Button Text">
                                <input type="text" name="sticky_cta_desc" value="{{ $settings['sticky_cta_desc'] ?? '' }}" class="premium-input" placeholder="Brief Description">
                            </div>
                        </div>
                        @if($isAdmin)
                        <div>
                            <h4 class="text-xs font-black uppercase text-orange-900 mb-6">Security (reCAPTCHA)</h4>
                            <div class="space-y-4">
                                <input type="text" name="recaptcha_site_key" value="{{ $settings['recaptcha_site_key'] ?? '' }}" class="premium-input text-xs" placeholder="Site Key">
                                <input type="password" name="recaptcha_secret_key" value="{{ $settings['recaptcha_secret_key'] ?? '' }}" class="premium-input text-xs" placeholder="Secret Key">
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs font-black uppercase text-orange-900 mb-6">System Constraints</h4>
                            <div class="space-y-4">
                                <label class="premium-label">Global Image Upload Limit (KB)</label>
                                <input type="number" name="image_size_limit" value="{{ $settings['image_size_limit'] ?? '2048' }}" class="premium-input text-xs" placeholder="Default: 2048">
                                <p class="helper-text">Applies to all modules (Courses, Blog, etc.). Recommended: 1024-2048 KB.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TAB: VAULT --}}
            <div id="tab-vault" class="branding-tab-content space-y-10">
                <div class="guide-box bg-zinc-900 border-zinc-800">
                    <div class="guide-title text-[#C5A059]"><i class="fas fa-info-circle"></i> Branding Guide: Media Vault</div>
                    <p class="guide-text text-zinc-400">This is your central storage. Assets marked as **"Active"** are currently linked to a model or branding field. **"Unused"** assets can be safely purged to save server space. You can **"Pick"** assets from here when editing other branding tabs.</p>
                </div>
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 bg-zinc-900 p-8 rounded-[32px] text-white">
                    <div class="flex-1">
                       <h3 class="text-2xl font-black uppercase tracking-tight text-[#C5A059]">Global Media Library</h3>
                       <p class="text-sm text-zinc-400 font-medium">Select or manage assets for your branding fields.</p>
                       <div class="mt-4 relative max-w-md">
                           <i class="fa fa-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-500"></i>
                           <input type="text" id="vaultSearch" onkeyup="filterVault()" placeholder="Filter assets..." class="w-full bg-zinc-800 border-zinc-700 rounded-xl py-3 pl-12 text-xs text-white">
                       </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <form action="{{ route('admin.branding.asset.purge') }}" method="POST" onsubmit="return confirm('Purge all unused assets?')">
                            @csrf
                            <button type="submit" class="px-6 py-4 rounded-2xl bg-red-50 text-red-600 border border-red-100 font-black uppercase text-[10px] hover:bg-red-600 hover:text-white transition-all">
                                <i class="fa fa-broom mr-2"></i> Purge Unused
                            </button>
                        </form>
                        <form action="{{ route('admin.branding.asset.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label class="cursor-pointer bg-[#C5A059] text-[#050C1C] px-10 py-4 rounded-2xl font-black uppercase text-xs hover:bg-white transition-all flex items-center gap-3 shadow-xl">
                                <i class="fa fa-cloud-upload-alt text-lg"></i> Upload
                                <input type="file" name="image" class="hidden" onchange="this.form.submit()">
                            </label>
                        </form>
                    </div>
                </div>

                <div class="brand-hub-grid" id="vaultGrid">
                @forelse($images as $image)
                    <div class="vault-asset-card bg-white border rounded-[24px] overflow-hidden flex flex-col shadow-sm group border-zinc-100" data-name="{{ strtolower($image['name']) }}">
                        <div class="h-48 bg-zinc-50 relative overflow-hidden">
                            <img src="{{ asset($image['path']) }}" class="w-full h-full object-contain p-4 group-hover:scale-105 transition-transform">
                            <div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center p-4 text-center space-y-2">
                                <button type="button" onclick="selectAsset('{{ $image['path'] }}')" class="vault-select-btn hidden w-full py-2 bg-emerald-500 text-white text-[9px] font-black uppercase rounded-lg">Select File</button>
                                <button type="button" onclick="copyToClipboard('{{ $image['path'] }}', this)" class="px-3 py-1 bg-white/10 text-white text-[8px] font-black uppercase rounded-lg border border-white/20">Copy Path</button>
                            </div>
                        </div>
                        <div class="p-4 border-t border-zinc-50 flex-grow">
                            <p class="text-[10px] font-black uppercase text-zinc-800 truncate">{{ $image['name'] }}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-[8px] font-black uppercase {{ $image['is_unused'] ? 'text-amber-500' : 'text-emerald-500' }}">{{ $image['is_unused'] ? 'Unused' : 'Active' }}</span>
                                <span class="text-[8px] text-zinc-400 font-bold uppercase">{{ $image['size'] }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center bg-zinc-50 rounded-[32px] border-2 border-dashed border-zinc-100 text-zinc-400 font-black uppercase text-xs">No Assets Found</div>
                @endforelse
                </div>
            </div>

            {{-- Floating Save Button --}}
            <button type="submit" id="brandingSubmitBtn" class="fixed bottom-10 right-10 z-50 bg-brand-dark text-brand-gold px-10 py-5 rounded-[24px] text-xs font-black uppercase tracking-[3px] shadow-2xl hover:scale-105 transition-all flex items-center gap-3 border-2 border-brand-gold/20 group">
                <i class="fa fa-save text-lg group-hover:rotate-12 transition-transform"></i>
                <span>Deploy Update</span>
            </button>
        </form>
    </div>

    <script>
        window.GoldenEyeBranding = window.GoldenEyeBranding || {
            activePickerInput: null,
            initialized: false,
        };

        window.filterVault = function () {
            const q = document.getElementById('vaultSearch').value.toLowerCase();
            document.querySelectorAll('.vault-asset-card').forEach(card => {
                const name = card.getAttribute('data-name');
                card.style.display = name.includes(q) ? 'flex' : 'none';
            });
        };

        window.openPicker = function (inputId) {
            window.GoldenEyeBranding.activePickerInput = inputId;
            switchTab('vault');
            document.querySelectorAll('.vault-select-btn').forEach(b => {
                b.style.display = 'block';
                b.classList.remove('hidden');
            });
        };

        window.selectAsset = function (path) {
            if (window.GoldenEyeBranding.activePickerInput) {
                const input = document.getElementById(window.GoldenEyeBranding.activePickerInput);
                if (input) {
                    input.value = path;
                    const parent = input.closest('.brand-card, .grid, .group');
                    if (parent) {
                        const img = parent.querySelector('img');
                        if (img) {
                            // Fix: Use absolute-style path relative to project root
                            const baseUrl = "{{ asset('') }}".replace(/\/$/, '');
                            img.src = baseUrl + '/' + path.replace(/^\//, '');
                        }
                    }
                }
                window.GoldenEyeBranding.activePickerInput = null;
                switchTab(window.lastActiveTabBeforePicker || 'visuals');
                document.querySelectorAll('.vault-select-btn').forEach(b => b.style.display = 'none');
            }
        };

        window.switchTab = function (tabId) {
            if (tabId !== 'vault') window.lastActiveTabBeforePicker = tabId;

            document.querySelectorAll('.branding-tab-content').forEach(t => {
                t.style.display = 'none';
                t.classList.remove('active');
            });
            document.querySelectorAll('.brand-hub-tabs button').forEach(b => b.classList.remove('active'));
            
            const target = document.getElementById('tab-' + tabId);
            const btn = document.getElementById('tabBtn-' + tabId);

            if (target) {
                target.style.display = 'block';
                target.classList.add('active');
            }
            if (btn) btn.classList.add('active');
            
            localStorage.setItem('activeBrandingTab', tabId);

            if (tabId === 'content') {
                const activePage = localStorage.getItem('activePageEditor') || 'page-home-about';
                switchPageEditor(activePage, false); 
                setTimeout(() => initVisibleEditor(activePage), 200);
            } else {
                setTimeout(() => initVisibleEditor('tab-' + tabId), 200);
            }
        };

        window.initVisibleEditor = function (containerId = null) {
            if (typeof CKEDITOR === 'undefined') {
                if (typeof window.ckRetryCount === 'undefined') window.ckRetryCount = 0;
                if (window.ckRetryCount < 10) {
                    window.ckRetryCount++;
                    setTimeout(() => initVisibleEditor(containerId), 500);
                } else {
                    console.error("CKEditor failed to load from CDN.");
                }
                return;
            }
            window.ckRetryCount = 0;
            
            const config = { height: 300, removeButtons: 'About,Maximize', versionCheck: false, allowedContent: true, extraAllowedContent: '*(*);*{*}' };
            
            let container = containerId ? document.getElementById(containerId) : document.querySelector('.branding-tab-content.active');
            if (!container) return;

            container.querySelectorAll('textarea').forEach(txt => {
                if (txt.id && txt.id.startsWith('editor_')) {
                    if (CKEDITOR.instances[txt.id]) CKEDITOR.instances[txt.id].destroy(true);
                    CKEDITOR.replace(txt.id, config);
                }
            });
        };

        window.switchPageEditor = function (pageId, shouldInit = true) {
            document.querySelectorAll('.page-editor-section').forEach(s => s.style.display = 'none');
            const target = document.getElementById(pageId);
            if (target) target.style.display = 'block';
            
            localStorage.setItem('activePageEditor', pageId);
            if (shouldInit) setTimeout(() => initVisibleEditor(pageId), 150);

            const previewBtn = document.getElementById('pagePreviewBtn');
            const routes = {
                'page-home-about': '{{ route("home") }}',
                'page-about-full': '{{ route("about-detail") }}',
                'page-courses': '{{ route("courses") }}',
                'page-blog': '{{ route("blog") }}',
                'page-faq': '{{ route("faq") }}',
                'page-contact': '{{ route("contact") }}',
                'page-privacy': '{{ route("privacy-policy") }}',
                'page-terms': '{{ route("terms-and-conditions") }}'
            };
            if (previewBtn && routes[pageId]) previewBtn.href = routes[pageId];
        };

        window.copyToClipboard = function (text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const old = btn.innerText;
                btn.innerText = 'Copied!';
                setTimeout(() => btn.innerText = old, 2000);
            });
        };

        document.getElementById('brandingForm').onsubmit = function() {
            if (typeof CKEDITOR !== 'undefined') {
                for (let instance in CKEDITOR.instances) CKEDITOR.instances[instance].updateElement();
            }
        };

        window.initBrandingHub = function () {
            const lastTab = localStorage.getItem('activeBrandingTab') || 'visuals';
            switchTab(lastTab);
            const lastPage = localStorage.getItem('activePageEditor') || 'page-home-about';
            const sel = document.getElementById('pageEditorSelector');
            if (sel) { sel.value = lastPage; switchPageEditor(lastPage); }
        };

        if (! window.GoldenEyeBranding.initialized) {
            document.addEventListener('DOMContentLoaded', initBrandingHub);
            document.addEventListener('livewire:navigated', initBrandingHub);
            window.GoldenEyeBranding.initialized = true;
        }

        initBrandingHub();
    </script>
</x-layouts::app>

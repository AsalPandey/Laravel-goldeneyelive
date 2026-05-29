<x-layouts::app :title="__('SEO & AI Authority Center')">
    <style>
        .seo-card {
            background: #fff;
            border: 1px solid #f1f5f9;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .seo-card:hover { transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .premium-label { font-size: 11px; font-weight: 900; color: #64748b; text-transform: uppercase; margin-bottom: 8px; display: block; letter-spacing: 0.8px; }
        .premium-input {
            width: 100%; border: 1px solid #e2e8f0; border-radius: 14px; padding: 12px 18px; font-size: 14px; transition: 0.2s;
            background: #fff;
        }
        .premium-input:focus { outline: none; border-color: #C5A059; box-shadow: 0 0 0 4px rgba(197, 160, 89, 0.1); }
        .guide-box {
            background: #f8fafc;
            border-left: 4px solid #C5A059;
            padding: 15px;
            border-radius: 0 12px 12px 0;
            margin-bottom: 20px;
        }
        .guide-title { font-size: 12px; font-weight: 900; color: #0f172a; text-transform: uppercase; margin-bottom: 5px; display: flex; align-items: center; gap: 8px; }
        .guide-text { font-size: 12px; color: #64748b; line-height: 1.6; }
        .section-icon {
            width: 40px; height: 40px; border-radius: 10px; background: #050C1C; color: #C5A059;
            display: flex; align-items: center; justify-content: center; font-size: 18px; margin-bottom: 15px;
        }
    </style>

    <div class="max-w-7xl mx-auto p-8 space-y-10">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 pb-8 border-b border-zinc-100">
            <div>
                <h1 class="text-4xl font-black text-zinc-900 tracking-tighter uppercase leading-none">SEO & AI <span class="text-[#C5A059]">Authority</span></h1>
                <p class="text-zinc-500 text-sm mt-3 font-medium">Command center for Search Engine, Answer Engine, and Generative AI optimization.</p>
            </div>
            <button form="seoForm" type="submit" class="px-10 py-4 bg-[#050C1C] text-[#C5A059] rounded-2xl font-black text-xs uppercase shadow-2xl hover:bg-[#C5A059] hover:text-[#050C1C] transition-all transform active:scale-95">
                <i class="fas fa-rocket mr-2"></i> Deploy SEO Strategy
            </button>
        </div>

        <form id="seoForm" action="{{ route('admin.seo.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                {{-- Global SEO --}}
                <div class="seo-card">
                    <div class="section-icon"><i class="fas fa-globe"></i></div>
                    <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Search Engine Optimization</h3>
                    
                    <div class="guide-box">
                        <div class="guide-title"><i class="fas fa-info-circle"></i> User Guide: Global SEO</div>
                        <p class="guide-text">These settings define how your academy appears in search results like Google and Bing. Ensure your title is punchy and your description contains keywords like "Web Development Pokhara".</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="premium-label">Global Meta Title</label>
                            <input type="text" name="meta_title" value="{{ $settings['meta_title'] ?? '' }}" class="premium-input @error('meta_title') border-rose-500 @enderror" placeholder="GoldenEye Academy — Best IT & Language Training in Pokhara">
                            @error('meta_title') <p class="text-rose-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="premium-label">Global Meta Description</label>
                            <textarea name="meta_description" rows="3" class="premium-input @error('meta_description') border-rose-500 @enderror">{{ $settings['meta_description'] ?? '' }}</textarea>
                            @error('meta_description') <p class="text-rose-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="premium-label">Global Keywords (Comma Separated)</label>
                            <input type="text" name="meta_keywords" value="{{ $settings['meta_keywords'] ?? '' }}" class="premium-input" placeholder="it training, web development, ielts pokhara">
                        </div>
                    </div>
                </div>

                {{-- AI & AEO Optimization --}}
                <div class="seo-card">
                    <div class="section-icon bg-indigo-600 text-white"><i class="fas fa-robot"></i></div>
                    <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">AI & Answer Engine (AEO)</h3>
                    
                    <div class="guide-box">
                        <div class="guide-title"><i class="fas fa-brain"></i> User Guide: AI Mastery</div>
                        <p class="guide-text">Answer Engines like ChatGPT and Perplexity look for concise "AEO Briefs". Describe your academy in 2-3 sentences as if you're answering the question "What is GoldenEye Academy?".</p>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="premium-label">Global AI/AEO Brief</label>
                            <textarea name="aeo_summary" rows="4" class="premium-input" placeholder="GoldenEye Academy is a premier educational institution in Pokhara since 2008, specializing in...">{{ $settings['aeo_summary'] ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="premium-label">Speakable Selectors (CSS)</label>
                            <input type="text" name="speakable_selectors" value="{{ $settings['speakable_selectors'] ?? '.course-description, .about-text' }}" class="premium-input" placeholder=".content-area, #main-description">
                            <p class="text-[10px] text-zinc-400 mt-2 italic">Define CSS classes that Voice Assistants (Alexa, Siri) should prioritize reading.</p>
                        </div>
                    </div>
                </div>

                {{-- Search Verification --}}
                <div class="seo-card">
                    <div class="section-icon bg-emerald-600 text-white"><i class="fas fa-check-double"></i></div>
                    <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Webmaster Verification</h3>
                    
                    <div class="guide-box">
                        <div class="guide-title"><i class="fas fa-shield-alt"></i> User Guide: Verification</div>
                        <p class="guide-text">Paste your verification IDs here to connect your site to Search Console and Bing Webmaster Tools. This allows you to track crawling performance.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="premium-label">Google Search Console ID</label>
                            <input type="text" name="google_search_console_id" value="{{ $settings['google_search_console_id'] ?? '' }}" class="premium-input" placeholder="ID from meta tag">
                        </div>
                        <div>
                            <label class="premium-label">Bing Webmaster ID</label>
                            <input type="text" name="bing_webmaster_id" value="{{ $settings['bing_webmaster_id'] ?? '' }}" class="premium-input" placeholder="ID from meta tag">
                        </div>
                    </div>
                </div>

                {{-- Technical: Robots.txt --}}
                <div class="seo-card">
                    <div class="section-icon bg-zinc-900 text-white"><i class="fas fa-terminal"></i></div>
                    <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">Crawler Controls (Robots.txt)</h3>
                    @php
                        $robotsTxtValue = old('robots_txt', $settings['robots_txt'] ?? '');
                        $robotsTxtBlocksFullSite = \App\Http\Requests\Admin\SEORequest::robotsTxtBlocksFullSite($robotsTxtValue);
                    @endphp
                    
                    <div class="guide-box">
                        <div class="guide-title"><i class="fas fa-compass"></i> User Guide: Robots.txt</div>
                        <p class="guide-text">Tell search engine robots which pages they can or cannot crawl. For production, avoid <code>Disallow: /</code> unless you intentionally want to hide the whole website from search engines.</p>
                    </div>

                    <div>
                        <label class="premium-label">Robots.txt Content</label>
                        <textarea name="robots_txt" rows="6" class="premium-input font-mono text-xs @error('robots_txt') border-rose-500 @enderror">{{ $robotsTxtValue }}</textarea>
                        @error('robots_txt') <p class="text-rose-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror

                        @if($robotsTxtBlocksFullSite)
                            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">
                                {{ $robotsTxtWarning }}
                            </div>
                        @endif

                        <label class="mt-4 flex items-start gap-3 rounded-xl border border-zinc-200 bg-zinc-50 p-4 text-sm font-semibold text-zinc-700">
                            <input type="checkbox" name="robots_txt_deindex_confirm" value="1" @checked(old('robots_txt_deindex_confirm')) class="mt-1 rounded border-zinc-300 text-[#C5A059] focus:ring-[#C5A059]">
                            <span>I understand this robots.txt may block the entire website from Google and other search engines.</span>
                        </label>
                    </div>
                </div>

                {{-- Geo Optimization --}}
                <div class="seo-card lg:col-span-2">
                    <div class="section-icon bg-amber-500 text-white"><i class="fas fa-map-marker-alt"></i></div>
                    <h3 class="text-xl font-black uppercase text-zinc-800 mb-4">GEO (Geographic Search Optimization)</h3>
                    
                    <div class="guide-box">
                        <div class="guide-title"><i class="fas fa-compass"></i> User Guide: Local SEO</div>
                        <p class="guide-text">Defining your exact location helps you appear in "near me" searches in Pokhara. These coordinates are used for LocalBusiness schema.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <label class="premium-label">Institution Name</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? 'GoldenEye' }}" class="premium-input">
                        </div>
                        <div>
                            <label class="premium-label">Brand Suffix</label>
                            <input type="text" name="site_name_suffix" value="{{ $settings['site_name_suffix'] ?? 'Academy' }}" class="premium-input">
                        </div>
                        <div>
                            <label class="premium-label">Geo Latitude</label>
                            <input type="text" name="geo_latitude" value="{{ $settings['geo_latitude'] ?? '' }}" class="premium-input">
                        </div>
                        <div>
                            <label class="premium-label">Geo Longitude</label>
                            <input type="text" name="geo_longitude" value="{{ $settings['geo_longitude'] ?? '' }}" class="premium-input">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-layouts::app>

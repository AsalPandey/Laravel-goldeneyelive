<div class="space-y-8">
    <div class="guide-box">
        <div class="guide-title"><i class="fas fa-users"></i> Audience landing pages</div>
        <p class="guide-text">Edit the existing student, parent, exam/language, and computer/job-skill pages. Hiding a page returns a public 404 and removes it from the sitemap; its saved content is retained.</p>
    </div>

    @foreach($audiencePages as $key => $page)
        @php
            $prefix = $page['prefix'];
        @endphp
        <div class="brand-card space-y-7">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-black uppercase text-zinc-800">{{ $page['label'] }}</h3>
                    <p class="text-sm text-zinc-500">Public route: {{ parse_url(route($page['route']), PHP_URL_PATH) }}</p>
                </div>
                <a href="{{ route($page['route']) }}" target="_blank" rel="noopener" class="px-5 py-3 rounded-xl bg-zinc-100 text-zinc-700 text-[10px] font-black uppercase">View public page</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                @foreach([
                    'status' => 'Whole page',
                    'hero_status' => 'Hero',
                    'guidance_status' => 'Guidance',
                    'support_status' => 'Support and proof',
                    'final_status' => 'Final inquiry',
                ] as $suffix => $label)
                    <div>
                        <label class="premium-label" for="{{ $prefix }}_{{ $suffix }}">{{ $label }}</label>
                        <select id="{{ $prefix }}_{{ $suffix }}" name="{{ $prefix }}_{{ $suffix }}" class="premium-input">
                            <option value="active" @selected(old("{$prefix}_{$suffix}", $settings["{$prefix}_{$suffix}"] ?? 'active') === 'active')>Visible</option>
                            <option value="inactive" @selected(old("{$prefix}_{$suffix}", $settings["{$prefix}_{$suffix}"] ?? 'active') === 'inactive')>Hidden</option>
                        </select>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="premium-label" for="{{ $prefix }}_page_title">SEO page title</label>
                    <input id="{{ $prefix }}_page_title" type="text" name="{{ $prefix }}_page_title" value="{{ old("{$prefix}_page_title", $page['page_title']) }}" class="premium-input">
                </div>
                <div>
                    <label class="premium-label" for="{{ $prefix }}_meta_description">SEO description</label>
                    <textarea id="{{ $prefix }}_meta_description" name="{{ $prefix }}_meta_description" rows="2" class="premium-input h-auto py-3">{{ old("{$prefix}_meta_description", $page['meta_description']) }}</textarea>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="premium-label">Hero image upload</label>
                    <input type="file" name="{{ $prefix }}_image" accept="image/*" class="premium-input h-auto py-3">
                </div>
                <div>
                    <label class="premium-label" for="input_{{ $prefix }}_image">Hero image media path</label>
                    <div class="flex gap-2">
                        <input id="input_{{ $prefix }}_image" type="text" name="{{ $prefix }}_image_path" value="{{ old("{$prefix}_image_path", $page['image']) }}" class="premium-input flex-1">
                        <button type="button" onclick="openPicker('input_{{ $prefix }}_image')" class="px-4 rounded-xl bg-zinc-800 text-brand-gold text-[10px] font-black uppercase">Pick</button>
                    </div>
                    @error("{$prefix}_image_path") <p class="text-xs text-red-600 mt-2">{{ $message }}</p> @enderror
                    <label class="inline-flex items-center gap-2 text-xs font-bold text-red-700 mt-3">
                        <input type="checkbox" name="remove_{{ $prefix }}_image" value="1" @checked(old("remove_{$prefix}_image")) class="rounded border-red-300 text-red-600 focus:ring-red-500">
                        Remove saved audience image and use the safe fallback
                    </label>
                </div>
            </div>

            @php
                $audienceTextFields = [
                    'badge' => 'Hero badge',
                    'headline' => 'Hero headline',
                    'subheadline' => 'Hero description',
                    'problem_tagline' => 'Problem tagline',
                    'problem_title' => 'Problem title',
                    'problem' => 'Problem description',
                    'paths_tagline' => 'Pathway tagline',
                    'paths_title' => 'Pathway title',
                    'support_tagline' => 'Support tagline',
                    'support_title' => 'Support title',
                    'why' => 'Support description',
                    'proof_tagline' => 'Proof tagline',
                    'final_tagline' => 'Final tagline',
                    'final_headline' => 'Final headline',
                    'primary_cta_text' => 'Primary inquiry button',
                    'secondary_cta_text' => 'Secondary button',
                ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($audienceTextFields as $suffix => $label)
                    <div @class(['md:col-span-2' => in_array($suffix, ['subheadline', 'problem', 'why', 'final_headline'], true)])>
                        <label class="premium-label" for="{{ $prefix }}_{{ $suffix }}">{{ $label }}</label>
                        @if(in_array($suffix, ['subheadline', 'problem', 'why', 'final_headline'], true))
                            <textarea id="{{ $prefix }}_{{ $suffix }}" name="{{ $prefix }}_{{ $suffix }}" rows="3" class="premium-input h-auto py-3">{{ old("{$prefix}_{$suffix}", $page[$suffix]) }}</textarea>
                        @else
                            <input id="{{ $prefix }}_{{ $suffix }}" type="text" name="{{ $prefix }}_{{ $suffix }}" value="{{ old("{$prefix}_{$suffix}", $page[$suffix]) }}" class="premium-input">
                        @endif
                    </div>
                @endforeach
                <div>
                    <label class="premium-label" for="{{ $prefix }}_paths">Recommended paths</label>
                    <textarea id="{{ $prefix }}_paths" name="{{ $prefix }}_paths" rows="7" class="premium-input h-auto py-3">{{ old("{$prefix}_paths", $page['paths_text']) }}</textarea>
                    <p class="helper-text">Enter one path per line.</p>
                </div>
                <div>
                    <label class="premium-label" for="{{ $prefix }}_proof">Trust markers</label>
                    <textarea id="{{ $prefix }}_proof" name="{{ $prefix }}_proof" rows="7" class="premium-input h-auto py-3">{{ old("{$prefix}_proof", $page['proof_text']) }}</textarea>
                    <p class="helper-text">Enter one trust marker per line.</p>
                </div>
                <div>
                    <label class="premium-label" for="{{ $prefix }}_secondary_action">Secondary button destination</label>
                    <select id="{{ $prefix }}_secondary_action" name="{{ $prefix }}_secondary_action" class="premium-input">
                        <option value="courses" @selected(old("{$prefix}_secondary_action", $page['secondary_action']) === 'courses')>Course catalogue</option>
                        <option value="whatsapp" @selected(old("{$prefix}_secondary_action", $page['secondary_action']) === 'whatsapp')>WhatsApp</option>
                    </select>
                </div>
            </div>
        </div>
    @endforeach
</div>

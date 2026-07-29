<div class="space-y-8">
    <div class="guide-box">
        <div class="guide-title"><i class="fas fa-home"></i> Homepage content and visibility</div>
        <p class="guide-text">Each section is visible by default. Hide a section only when its public content is not ready; saved content remains available when the section is shown again.</p>
    </div>

    <div class="brand-card">
        <h3 class="text-xl font-black uppercase text-zinc-800 mb-6">Section visibility</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($homepageSectionDefinitions as $section)
                <div class="rounded-2xl border border-zinc-100 bg-zinc-50 p-4">
                    <label class="premium-label" for="{{ $section['key'] }}">{{ $section['label'] }}</label>
                    <select id="{{ $section['key'] }}" name="{{ $section['key'] }}" class="premium-input">
                        <option value="active" @selected(old($section['key'], $settings[$section['key']] ?? 'active') === 'active')>Visible</option>
                        <option value="inactive" @selected(old($section['key'], $settings[$section['key']] ?? 'active') === 'inactive')>Hidden</option>
                    </select>
                    <p class="helper-text">{{ $section['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>

    @php
        $homepageTextFields = [
            'home_meta_title' => ['Page meta title', 'meta_title'],
            'home_meta_description' => ['Page meta description', 'meta_description'],
            'home_audience_tagline' => ['Audience section tagline', 'audience_tagline'],
            'home_audience_title' => ['Audience section title', 'audience_title'],
            'home_courses_tagline' => ['Courses section tagline', 'courses_tagline'],
            'home_courses_title' => ['Courses section title', 'courses_title'],
            'home_courses_cta_text' => ['Courses section button', 'courses_cta_text'],
            'home_categories_tagline' => ['Categories section tagline', 'categories_tagline'],
            'home_categories_title' => ['Categories section title', 'categories_title'],
            'home_why_tagline' => ['Why section tagline', 'why_tagline'],
            'home_why_title' => ['Why section title', 'why_title'],
            'home_why_description' => ['Why section description', 'why_description'],
            'home_testimonials_tagline' => ['Testimonials tagline', 'testimonials_tagline'],
            'home_testimonials_title' => ['Testimonials title', 'testimonials_title'],
            'home_faculty_tagline' => ['Faculty tagline', 'faculty_tagline'],
            'home_faculty_title' => ['Faculty title', 'faculty_title'],
            'home_reviews_tagline' => ['Review proof tagline', 'reviews_tagline'],
            'home_reviews_title' => ['Review proof title', 'reviews_title'],
            'home_parent_tagline' => ['Parent section tagline', 'parent_tagline'],
            'home_parent_title' => ['Parent section title', 'parent_title'],
            'home_parent_description' => ['Parent section description', 'parent_description'],
            'home_faq_tagline' => ['FAQ section tagline', 'faq_tagline'],
            'home_faq_title' => ['FAQ section title', 'faq_title'],
            'home_final_tagline' => ['Final section tagline', 'final_tagline'],
            'home_final_title' => ['Final section title', 'final_title'],
            'home_final_description' => ['Final section description', 'final_description'],
            'home_final_primary_cta_text' => ['Final primary button', 'final_primary_cta_text'],
            'home_final_secondary_cta_text' => ['Final WhatsApp button', 'final_secondary_cta_text'],
        ];
        $homepageListFields = [
            'home_trust_items' => ['Trust strip items', 'trust_items_text'],
            'home_why_items' => ['Why section points', 'why_items_text'],
            'home_parent_items' => ['Parent section points', 'parent_items_text'],
        ];
    @endphp

    <div class="brand-card">
        <h3 class="text-xl font-black uppercase text-zinc-800 mb-6">Homepage headings and actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            @foreach($homepageTextFields as $field => [$label, $contentKey])
                <div @class(['md:col-span-2' => str_contains($field, 'description')])>
                    <label class="premium-label" for="{{ $field }}">{{ $label }}</label>
                    @if(str_contains($field, 'description'))
                        <textarea id="{{ $field }}" name="{{ $field }}" rows="3" class="premium-input h-auto py-3">{{ old($field, $homepageContent[$contentKey]) }}</textarea>
                    @else
                        <input id="{{ $field }}" type="text" name="{{ $field }}" value="{{ old($field, $homepageContent[$contentKey]) }}" class="premium-input">
                    @endif
                </div>
            @endforeach
            @foreach($homepageListFields as $field => [$label, $contentKey])
                <div>
                    <label class="premium-label" for="{{ $field }}">{{ $label }}</label>
                    <textarea id="{{ $field }}" name="{{ $field }}" rows="7" class="premium-input h-auto py-3">{{ old($field, $homepageContent[$contentKey]) }}</textarea>
                    <p class="helper-text">Enter one public item per line.</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="brand-card">
        <h3 class="text-xl font-black uppercase text-zinc-800 mb-2">Homepage audience cards</h3>
        <p class="text-sm text-zinc-500 mb-6">A card is shown only when both this card and its audience page are visible.</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach($homepageContent['audience_cards'] as $key => $card)
                @php
                    $prefix = "home_audience_{$key}";
                @endphp
                <fieldset class="rounded-2xl border border-zinc-100 bg-zinc-50 p-5 space-y-4">
                    <legend class="px-2 text-xs font-black uppercase text-zinc-700">{{ $audiencePages[$key]['label'] }}</legend>
                    <div>
                        <label class="premium-label" for="{{ $prefix }}_status">Card visibility</label>
                        <select id="{{ $prefix }}_status" name="{{ $prefix }}_status" class="premium-input">
                            <option value="active" @selected(old("{$prefix}_status", $settings["{$prefix}_status"] ?? 'active') === 'active')>Visible</option>
                            <option value="inactive" @selected(old("{$prefix}_status", $settings["{$prefix}_status"] ?? 'active') === 'inactive')>Hidden</option>
                        </select>
                    </div>
                    @foreach(['title' => 'Card title', 'problem' => 'Visitor question', 'benefit' => 'Benefit', 'cta_text' => 'Button label'] as $suffix => $label)
                        <div>
                            <label class="premium-label" for="{{ $prefix }}_{{ $suffix }}">{{ $label }}</label>
                            <input id="{{ $prefix }}_{{ $suffix }}" type="text" name="{{ $prefix }}_{{ $suffix }}" value="{{ old("{$prefix}_{$suffix}", $card[$suffix]) }}" class="premium-input">
                        </div>
                    @endforeach
                </fieldset>
            @endforeach
        </div>
    </div>
</div>

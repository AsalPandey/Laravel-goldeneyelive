<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Course;
use Illuminate\Support\Str;

final class StructuredData
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function siteGraph(
        array $settings,
        ?string $pageTitle = null,
        ?string $pageDescription = null,
        ?string $canonicalUrl = null,
    ): array {
        $organization = self::organizationSchema($settings);
        $canonicalUrl ??= CanonicalUrl::current();

        return self::withoutEmptyValues([
            '@context' => 'https://schema.org',
            '@graph' => [
                $organization,
                [
                    '@type' => 'WebSite',
                    '@id' => CanonicalUrl::to('/').'#website',
                    'url' => CanonicalUrl::to('/'),
                    'name' => self::siteName($settings),
                    'publisher' => [
                        '@id' => self::organizationId(),
                    ],
                ],
                self::withoutEmptyValues([
                    '@type' => 'WebPage',
                    '@id' => $canonicalUrl.'#webpage',
                    'url' => $canonicalUrl,
                    'name' => $pageTitle,
                    'description' => $pageDescription,
                    'isPartOf' => [
                        '@id' => CanonicalUrl::to('/').'#website',
                    ],
                    'about' => [
                        '@id' => self::organizationId(),
                    ],
                ]),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function courseSchema(Course $course, array $settings): array
    {
        return self::withoutEmptyValues(array_merge([
            '@context' => 'https://schema.org',
        ], self::defaultCourseSchema($course, $settings)));
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function articleSchema(BlogPost $post, array $settings): array
    {
        $url = CanonicalUrl::route('blog-detail', ['slug' => $post->slug]);
        $description = trim(strip_tags((string) ($post->meta_description ?: $post->content)));
        $publishedAt = $post->published_at ?? $post->created_at;

        return self::withoutEmptyValues([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            '@id' => $url.'#article',
            'url' => $url,
            'mainEntityOfPage' => [
                '@id' => $url,
            ],
            'headline' => $post->title,
            'description' => Str::limit($description, 160, ''),
            'image' => PublicAsset::canonicalUrl($post->image ?? null, 'site/img/carousel-1.png'),
            'author' => filled($post->author) && $post->author !== 'Golden Eye Academy' ? [
                '@type' => 'Person',
                'name' => $post->author,
            ] : [
                '@type' => 'Organization',
                '@id' => self::organizationId(),
                'name' => self::siteName($settings),
            ],
            'publisher' => [
                '@id' => self::organizationId(),
            ],
            'datePublished' => $publishedAt?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * @param  array<int, array{name: string, url?: string|null}>  $items
     * @return array<string, mixed>
     */
    public static function breadcrumbSchema(array $items): array
    {
        $elements = [];

        foreach (array_values($items) as $index => $item) {
            $name = trim(strip_tags((string) ($item['name'] ?? '')));

            if ($name === '') {
                continue;
            }

            $elements[] = self::withoutEmptyValues([
                '@type' => 'ListItem',
                'position' => count($elements) + 1,
                'name' => $name,
                'item' => filled($item['url'] ?? null)
                    ? CanonicalUrl::normalize((string) $item['url'])
                    : null,
            ]);
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $elements,
        ];
    }

    public static function titleWithBrand(?string $title, string $brand = 'Golden Eye Academy'): string
    {
        $cleanTitle = trim(str_replace(
            ['GoldenEye Academy', 'GoldenEye'],
            ['Golden Eye Academy', 'Golden Eye'],
            strip_tags((string) $title),
        ));
        $cleanBrand = trim($brand);

        if ($cleanTitle === '') {
            return $cleanBrand;
        }

        if (Str::contains(Str::lower($cleanTitle), Str::lower($cleanBrand))) {
            return $cleanTitle;
        }

        return $cleanTitle.' - '.$cleanBrand;
    }

    public static function courseMetaDescription(Course $course): string
    {
        $adminDescription = trim(strip_tags((string) $course->meta_description));

        if ($adminDescription !== '') {
            return self::normalizeBrandText($adminDescription);
        }

        $description = trim(strip_tags((string) $course->description));

        if ($description === '') {
            $description = $course->name.' at Golden Eye Academy in Pokhara. Ask the academy team for current class and enrollment details.';
        }

        return self::normalizeBrandText(Str::limit($description, 155, ''));
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function organizationSchema(array $settings): array
    {
        $siteName = self::siteName($settings);
        $socialLinks = array_values(array_filter([
            $settings['facebook_url'] ?? null,
            $settings['instagram_url'] ?? null,
            $settings['linkedin_url'] ?? null,
            $settings['youtube_url'] ?? null,
            $settings['tiktok_url'] ?? null,
        ], 'filled'));
        $hasCoordinates = filled($settings['geo_latitude'] ?? null) && filled($settings['geo_longitude'] ?? null);

        return self::withoutEmptyValues([
            '@type' => 'EducationalOrganization',
            '@id' => self::organizationId(),
            'name' => $siteName,
            'foundingDate' => $settings['founding_year'] ?? null,
            'url' => CanonicalUrl::to('/'),
            'logo' => PublicAsset::canonicalUrl($settings['site_logo'] ?? null, 'site/img/logo.png'),
            'description' => $settings['meta_description'] ?? 'Golden Eye Academy offers IELTS/PTE, Japanese, Korean, English, computer, office, web development, and IT classes in Pokhara, Nepal.',
            'address' => filled($settings['site_address'] ?? null) ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings['site_address'],
                'addressCountry' => 'NP',
            ] : null,
            'geo' => $hasCoordinates ? [
                '@type' => 'GeoCoordinates',
                'latitude' => $settings['geo_latitude'],
                'longitude' => $settings['geo_longitude'],
            ] : null,
            'email' => $settings['site_email'] ?? null,
            'telephone' => $settings['site_phone'] ?? null,
            'sameAs' => $socialLinks,
        ]);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function defaultCourseSchema(Course $course, array $settings): array
    {
        $customDescription = trim(strip_tags((string) $course->meta_description));
        $description = $customDescription !== ''
            ? self::normalizeBrandText($customDescription)
            : trim(strip_tags((string) $course->description));

        return self::withoutEmptyValues([
            '@type' => 'Course',
            '@id' => CanonicalUrl::route('courses-detail', ['slug' => $course->slug]).'#course',
            'url' => CanonicalUrl::route('courses-detail', ['slug' => $course->slug]),
            'name' => $course->name,
            'description' => $description,
            'image' => PublicAsset::canonicalUrl($course->photo ?? null, 'site/img/cat-1.jpg'),
            'provider' => [
                '@id' => self::organizationId(),
            ],
        ]);
    }

    public static function organizationId(): string
    {
        return CanonicalUrl::to('/').'#organization';
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public static function siteName(array $settings): string
    {
        $name = trim((string) ($settings['site_name'] ?? 'Golden Eye'));
        $suffix = trim((string) ($settings['site_name_suffix'] ?? 'Academy'));
        $name = self::normalizeBrandText($name);

        if (Str::lower($name) === 'goldeneye') {
            $name = 'Golden Eye';
        }

        if ($suffix !== '' && Str::endsWith(Str::lower($name), ' '.Str::lower($suffix))) {
            return $name;
        }

        $fullName = trim($name.' '.$suffix);

        return $fullName === '' ? 'Golden Eye Academy' : $fullName;
    }

    private static function normalizeBrandText(string $value): string
    {
        return str_replace(
            ['GoldenEye Academy', 'GoldenEye'],
            ['Golden Eye Academy', 'Golden Eye'],
            $value,
        );
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private static function withoutEmptyValues(array $values): array
    {
        foreach ($values as $key => $value) {
            if (is_array($value)) {
                $value = self::withoutEmptyValues($value);
            }

            if ($value === null || $value === '' || $value === []) {
                unset($values[$key]);

                continue;
            }

            $values[$key] = $value;
        }

        return $values;
    }
}

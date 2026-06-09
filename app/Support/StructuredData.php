<?php

namespace App\Support;

use App\Models\Course;
use Illuminate\Support\Str;

final class StructuredData
{
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function siteGraph(array $settings): array
    {
        $organization = self::organizationSchema($settings);
        $extraSchemas = [];

        foreach (self::schemaNodes(self::decodeSchema($settings['schema_markup'] ?? null)) as $node) {
            if (self::isOrganizationSchema($node)) {
                $organization = self::mergeOrganizationSchema($organization, $node);

                continue;
            }

            $extraSchemas[] = self::withoutContext($node);
        }

        return self::withoutEmptyValues([
            '@context' => 'https://schema.org',
            '@graph' => array_values(array_merge([$organization], $extraSchemas)),
        ]);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function courseSchema(Course $course, array $settings): array
    {
        $adminCourseSchema = null;

        foreach (self::schemaNodes(self::decodeSchema($course->schema_markup)) as $node) {
            if (self::schemaHasType($node, 'Course')) {
                $adminCourseSchema = self::withoutContext($node);

                break;
            }
        }

        $schema = array_replace_recursive($adminCourseSchema ?? [], self::defaultCourseSchema($course, $settings));

        return self::withoutEmptyValues(array_merge([
            '@context' => 'https://schema.org',
        ], $schema));
    }

    public static function titleWithBrand(?string $title, string $brand = 'GoldenEye Academy'): string
    {
        $cleanTitle = trim(strip_tags((string) $title));
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
            return $adminDescription;
        }

        return Str::limit(trim(strip_tags((string) $course->description)), 155, '');
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function organizationSchema(array $settings): array
    {
        $siteName = trim(($settings['site_name'] ?? 'GoldenEye').' '.($settings['site_name_suffix'] ?? 'Academy'));
        $socialLinks = array_values(array_filter([
            $settings['facebook_url'] ?? 'https://www.facebook.com/goldeneyeacademy',
            $settings['instagram_url'] ?? 'https://www.instagram.com/goldeneye.academy/',
            $settings['linkedin_url'] ?? 'https://www.linkedin.com/company/golden-eye-academy/',
        ]));
        $speakableSelectors = array_values(array_filter(array_map('trim', explode(',', (string) ($settings['speakable_selectors'] ?? '')))));

        return self::withoutEmptyValues([
            '@type' => 'EducationalOrganization',
            '@id' => self::organizationId(),
            'name' => $siteName,
            'url' => url('/'),
            'logo' => PublicAsset::url($settings['site_logo'] ?? null, 'site/img/logo.png'),
            'foundingDate' => '2008',
            'description' => $settings['meta_description'] ?? 'GoldenEye Academy helps learners in Pokhara choose study abroad, language, computer, office, and web development courses with guidance before enrollment.',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal',
                'addressLocality' => 'Pokhara',
                'addressRegion' => 'Gandaki',
                'postalCode' => '33700',
                'addressCountry' => 'NP',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => $settings['geo_latitude'] ?? '28.2172',
                'longitude' => $settings['geo_longitude'] ?? '83.9825',
            ],
            'email' => $settings['site_email'] ?? null,
            'telephone' => $settings['site_phone'] ?? '+977-61-572599',
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => $settings['site_phone'] ?? '+977-61-572599',
                'contactType' => 'customer service',
                'areaServed' => 'NP',
                'availableLanguage' => ['English', 'Nepali'],
            ],
            'sameAs' => $socialLinks,
            'speakable' => $speakableSelectors === [] ? null : [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => $speakableSelectors,
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    private static function defaultCourseSchema(Course $course, array $settings): array
    {
        $description = trim(strip_tags((string) $course->description));

        return self::withoutEmptyValues([
            '@type' => 'Course',
            '@id' => url()->current().'#course',
            'name' => $course->name,
            'description' => $description,
            'image' => PublicAsset::url($course->photo ?? null, 'site/img/cat-1.jpg'),
            'provider' => [
                '@id' => self::organizationId(),
            ],
            'hasCourseInstance' => [
                '@type' => 'CourseInstance',
                'courseMode' => 'Onsite',
                'location' => [
                    '@type' => 'Place',
                    'name' => trim(($settings['site_name'] ?? 'GoldenEye').' '.($settings['site_name_suffix'] ?? 'Academy')),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => $settings['site_address'] ?? 'Srijana Chowk, Pokhara, Nepal',
                        'addressLocality' => 'Pokhara',
                        'addressCountry' => 'NP',
                    ],
                ],
                'duration' => $course->duration,
                'instructor' => [
                    '@type' => 'Person',
                    'name' => $course->instructor ?: null,
                ],
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => preg_replace('/[^0-9]/', '', (string) $course->price) ?: '0',
                'priceCurrency' => 'NPR',
                'category' => 'Professional Education',
                'availability' => 'https://schema.org/InStock',
                'url' => url()->current(),
            ],
        ]);
    }

    /**
     * @param  array<string, mixed>  $default
     * @param  array<string, mixed>  $admin
     * @return array<string, mixed>
     */
    private static function mergeOrganizationSchema(array $default, array $admin): array
    {
        return self::withoutEmptyValues(array_replace_recursive($default, self::withoutContext($admin), [
            '@id' => self::organizationId(),
        ]));
    }

    private static function organizationId(): string
    {
        return url('/').'#organization';
    }

    /**
     * @return array<string, mixed>|null
     */
    private static function decodeSchema(mixed $schema): ?array
    {
        if (blank($schema)) {
            return null;
        }

        if (is_array($schema)) {
            return $schema;
        }

        $decoded = json_decode((string) $schema, true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : null;
    }

    /**
     * @param  array<string, mixed>|null  $schema
     * @return array<int, array<string, mixed>>
     */
    private static function schemaNodes(?array $schema): array
    {
        if ($schema === null) {
            return [];
        }

        if (isset($schema['@graph']) && is_array($schema['@graph'])) {
            return array_values(array_filter($schema['@graph'], 'is_array'));
        }

        return [$schema];
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    private static function isOrganizationSchema(array $schema): bool
    {
        foreach (['Organization', 'EducationalOrganization', 'LocalBusiness'] as $type) {
            if (self::schemaHasType($schema, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $schema
     */
    private static function schemaHasType(array $schema, string $type): bool
    {
        $schemaType = $schema['@type'] ?? null;

        if (is_string($schemaType)) {
            return $schemaType === $type;
        }

        if (is_array($schemaType)) {
            return in_array($type, $schemaType, true);
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private static function withoutContext(array $schema): array
    {
        unset($schema['@context']);

        return $schema;
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

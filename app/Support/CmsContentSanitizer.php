<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use Illuminate\Support\Str;

class CmsContentSanitizer
{
    /**
     * @var array<string, array<int, string>>
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'table' => ['class'],
        'thead' => ['class'],
        'tbody' => ['class'],
        'tr' => ['class'],
        'th' => ['class', 'colspan', 'rowspan', 'scope'],
        'td' => ['class', 'colspan', 'rowspan'],
        'p' => ['class'],
        'div' => ['class'],
        'span' => ['class'],
        'ul' => ['class'],
        'ol' => ['class'],
        'li' => ['class'],
        'blockquote' => ['class'],
    ];

    /**
     * @var array<int, string>
     */
    private const ALLOWED_TAGS = [
        'a', 'p', 'b', 'i', 'u', 'ul', 'li', 'ol', 'strong', 'em', 'br',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'div', 'span', 'img',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'blockquote',
    ];

    public static function html(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><div id="cms-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('cms-root');

        if (! $root instanceof DOMElement) {
            return e(strip_tags($html));
        }

        self::sanitizeNode($root);

        $clean = '';
        foreach ($root->childNodes as $child) {
            $clean .= $document->saveHTML($child);
        }

        return $clean;
    }

    public static function jsonLd(?string $json): ?string
    {
        if (blank($json)) {
            return null;
        }

        $decoded = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) {
            return null;
        }

        return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private static function sanitizeNode(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);

            if ($node->getAttribute('id') !== 'cms-root' && ! in_array($tag, self::ALLOWED_TAGS, true)) {
                self::unwrapNode($node);

                return;
            }

            self::sanitizeAttributes($node);
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::sanitizeNode($child);
        }
    }

    private static function sanitizeAttributes(DOMElement $element): void
    {
        $tag = strtolower($element->tagName);
        $allowed = self::ALLOWED_ATTRIBUTES[$tag] ?? [];

        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (! in_array($name, $allowed, true) || Str::startsWith($name, 'on')) {
                $element->removeAttribute($attribute->name);

                continue;
            }

            if (in_array($name, ['href', 'src'], true) && self::hasUnsafeUrl($value)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && ! $element->hasAttribute('loading')) {
            $element->setAttribute('loading', 'lazy');
        }
    }

    private static function hasUnsafeUrl(string $value): bool
    {
        $normalized = strtolower(preg_replace('/\s+/', '', html_entity_decode($value)) ?? '');

        return Str::startsWith($normalized, ['javascript:', 'vbscript:', 'data:text/html']);
    }

    private static function unwrapNode(DOMNode $node): void
    {
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }

        $parent->removeChild($node);
    }
}

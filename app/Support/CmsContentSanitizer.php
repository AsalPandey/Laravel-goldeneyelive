<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;
use Illuminate\Support\Str;
use JsonException;

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

    /**
     * Elements whose contents must not be retained when the element is removed.
     *
     * @var array<int, string>
     */
    private const BLOCKED_TAGS = [
        'script', 'style', 'iframe', 'object', 'embed', 'template', 'svg', 'math',
    ];

    public static function html(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $document = new DOMDocument;

        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="cms-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return e(strip_tags($html));
        }

        $rootNodes = (new DOMXPath($document))->query('//*[@id="cms-root"]');
        $root = $rootNodes !== false ? $rootNodes->item(0) : null;

        if (! $root instanceof DOMElement) {
            return e(strip_tags($html));
        }

        self::sanitizeNode($root, $root);

        $clean = '';
        foreach ($root->childNodes as $child) {
            $serialized = $document->saveHTML($child);

            if ($serialized === false) {
                return e(strip_tags($html));
            }

            $clean .= $serialized;
        }

        return $clean;
    }

    public static function jsonLd(?string $json): ?string
    {
        if (blank($json)) {
            return null;
        }

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }

        if (! is_array($decoded)) {
            return null;
        }

        try {
            return json_encode(
                $decoded,
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            return null;
        }
    }

    private static function sanitizeNode(DOMNode $node, DOMElement $root): void
    {
        if ($node->nodeType === XML_COMMENT_NODE) {
            $node->parentNode?->removeChild($node);

            return;
        }

        if ($node instanceof DOMElement) {
            $tag = strtolower($node->tagName);

            if ($node !== $root && in_array($tag, self::BLOCKED_TAGS, true)) {
                $node->parentNode?->removeChild($node);

                return;
            }

            if ($node !== $root && ! in_array($tag, self::ALLOWED_TAGS, true)) {
                $children = iterator_to_array($node->childNodes);
                self::unwrapNode($node);

                foreach ($children as $child) {
                    self::sanitizeNode($child, $root);
                }

                return;
            }

            if ($node !== $root) {
                self::sanitizeAttributes($node);
            }
        }

        foreach (iterator_to_array($node->childNodes) as $child) {
            self::sanitizeNode($child, $root);
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

            if (in_array($name, ['href', 'src'], true) && self::hasUnsafeUrl($value, $name)) {
                $element->removeAttribute($attribute->name);
            }
        }

        if ($tag === 'a') {
            if ($element->hasAttribute('target') && ! in_array(strtolower($element->getAttribute('target')), ['_blank', '_self', '_parent', '_top'], true)) {
                $element->removeAttribute('target');
            }

            $element->setAttribute('rel', 'noopener noreferrer');
        }

        if ($tag === 'img' && ! $element->hasAttribute('loading')) {
            $element->setAttribute('loading', 'lazy');
        }
    }

    private static function hasUnsafeUrl(string $value, string $attribute): bool
    {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/[\x00-\x20\x7F]+/u', '', $decoded);

        if ($normalized === null) {
            return true;
        }

        if ($normalized === '' || Str::startsWith($normalized, ['#', '?', '/', './', '../'])) {
            return Str::startsWith($normalized, '//');
        }

        $scheme = parse_url($normalized, PHP_URL_SCHEME);

        if ($scheme === false || $scheme === null) {
            return false;
        }

        $allowedSchemes = $attribute === 'href'
            ? ['https', 'mailto', 'tel']
            : ['https'];

        return ! in_array(strtolower($scheme), $allowedSchemes, true);
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

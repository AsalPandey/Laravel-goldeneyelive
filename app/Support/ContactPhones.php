<?php

namespace App\Support;

class ContactPhones
{
    /**
     * @return array<int, array{display: string, href: string}>
     */
    public static function parse(?string $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        $parts = preg_split('/\s*(?:,|;|\||\r?\n)\s*|\s+\/\s+/u', trim($value)) ?: [];
        $phoneNumbers = [];

        foreach ($parts as $part) {
            $display = trim($part);
            $digits = preg_replace('/\D+/', '', $display);

            if ($display === '' || $digits === '') {
                continue;
            }

            $href = (str_starts_with($display, '+') ? '+' : '').$digits;
            $phoneNumbers[$href] = [
                'display' => $display,
                'href' => $href,
            ];
        }

        return array_values($phoneNumbers);
    }
}

<?php

namespace App\Services;

class ReviewTextSanitizer
{
    public static function sanitize(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/(?:https?:\/\/|www\.)[^\s]+/iu', ' ', $text) ?? $text;
        $text = preg_replace('/(?<!\d)(?:\+?62|0)[\d\s().-]{7,}\d(?!\d)/u', ' ', $text) ?? $text;

        return trim(preg_replace('/\s+/u', ' ', $text) ?? $text);
    }

    public static function containsForbiddenTerm(string $text): bool
    {
        foreach (config('review-moderation.forbidden_terms', []) as $term) {
            $pattern = '/(?<![\p{L}\p{N}])'.str_replace(' ', '\\s+', preg_quote($term, '/')).'(?![\p{L}\p{N}])/iu';

            if (preg_match($pattern, $text) === 1) {
                return true;
            }
        }

        return false;
    }
}
<?php

use App\Models\Setting;
use App\Support\Seo;

if (! function_exists('setting')) {
    /** Read a site setting (from the settings table), with a fallback default. */
    function setting(string $key, mixed $default = null): mixed
    {
        $value = Setting::allCached()[$key] ?? null;

        return ($value === null || $value === '') ? $default : $value;
    }
}

if (! function_exists('upload_url')) {
    /** Public URL for a stored upload path; falls back to a placeholder image. */
    function upload_url(?string $path, ?string $fallback = null): ?string
    {
        if (! $path) {
            return $fallback;
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        return asset('uploads/'.ltrim($path, '/'));
    }
}

if (! function_exists('upload_small_url')) {
    /** URL of the small (640px) variant created at upload time, if present. */
    function upload_small_url(?string $path): ?string
    {
        if (! $path || str_starts_with($path, 'http')) {
            return null;
        }
        $small = preg_replace('/\.(\w+)$/', '-sm.$1', $path);

        return is_file(public_path('uploads/'.$small)) ? asset('uploads/'.$small) : null;
    }
}

if (! function_exists('seo')) {
    function seo(): Seo
    {
        return app(Seo::class);
    }
}

if (! function_exists('inr')) {
    /** Format an amount in Indian Rupees with Indian digit grouping. */
    function inr(float|int|string|null $amount, bool $decimals = false): string
    {
        $amount = (float) $amount;
        $neg = $amount < 0;
        $amount = abs($amount);
        $whole = (string) floor($amount);
        $last3 = substr($whole, -3);
        $rest = substr($whole, 0, -3);
        if ($rest !== '') {
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $rest).',';
        }
        $out = '₹'.$rest.$last3;
        if ($decimals) {
            $out .= '.'.str_pad((string) round(($amount - floor($amount)) * 100), 2, '0', STR_PAD_LEFT);
        }

        return ($neg ? '-' : '').$out;
    }
}

if (! function_exists('hex_rgb')) {
    /** "#1f7a4d" → "31, 122, 77" for use in rgba(var(--x-rgb), a). */
    function hex_rgb(?string $hex, string $fallback = '31, 122, 77'): string
    {
        if (! $hex || ! preg_match('/^#?([0-9a-f]{6})$/i', $hex, $m)) {
            return $fallback;
        }

        return implode(', ', array_map('hexdec', str_split($m[1], 2)));
    }
}

if (! function_exists('full_address')) {
    function full_address(bool $multiline = false): string
    {
        $parts = array_filter([setting('address'), trim(setting('city').', '.setting('state').' '.setting('pincode'), ', ')]);

        return implode($multiline ? "\n" : ', ', $parts);
    }
}

if (! function_exists('phone_link')) {
    function phone_link(?string $phone): string
    {
        $digits = preg_replace('/\D/', '', (string) $phone);

        return strlen($digits) === 10 ? '+91'.$digits : '+'.$digits;
    }
}

if (! function_exists('whatsapp_link')) {
    function whatsapp_link(?string $phone, string $text = ''): string
    {
        $digits = ltrim(phone_link($phone), '+');

        return 'https://wa.me/'.$digits.($text ? '?text='.rawurlencode($text) : '');
    }
}

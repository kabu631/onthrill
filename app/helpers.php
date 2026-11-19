<?php

use App\Support\Settings;
use App\Support\SiteTexts;
use Illuminate\Support\HtmlString;

if (! function_exists('setting')) {
    /** Read a site setting (Admin > Site Settings), falling back to config/site.php. */
    function setting(string $key, mixed $default = null): mixed
    {
        return Settings::get($key, $default);
    }
}

if (! function_exists('wording')) {
    /**
     * A piece of website wording (Admin > Page Content), with its :placeholders
     * filled in: wording('dest_about_title', ['name' => 'Muktinath']) gives "About Muktinath".
     */
    function wording(string $key, array $replace = []): string
    {
        $text = SiteTexts::get($key);

        return $replace === [] ? $text : strtr($text, collect($replace)->mapWithKeys(fn ($value, $name) => [':'.$name => (string) $value])->all());
    }
}

if (! function_exists('wording_html')) {
    /** Like wording(), escaped for HTML, with the filled-in values in bold. */
    function wording_html(string $key, array $replace = []): HtmlString
    {
        $text = e(SiteTexts::get($key));
        $pairs = collect($replace)->mapWithKeys(fn ($value, $name) => [':'.$name => '<strong class="text-ink">'.e((string) $value).'</strong>'])->all();

        return new HtmlString(strtr($text, $pairs));
    }
}

if (! function_exists('media_url')) {
    /** Public URL for a file on the public storage disk, or null when there is none. */
    function media_url(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }
}

if (! function_exists('lakh_format')) {
    /** Digit grouping used in India and Nepal: 125000 becomes 1,25,000. */
    function lakh_format(float|int|string|null $amount): string
    {
        $amount = round((float) $amount, 2);
        $negative = $amount < 0;
        [$whole, $fraction] = explode('.', number_format(abs($amount), 2, '.', ''));

        if (strlen($whole) > 3) {
            $last3 = substr($whole, -3);
            $rest = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($whole, 0, -3));
            $whole = $rest.','.$last3;
        }

        return ($negative ? '-' : '').$whole.($fraction === '00' ? '' : '.'.$fraction);
    }
}

if (! function_exists('inr')) {
    /** Indian rupees: ₹1,25,000 */
    function inr(float|int|string|null $amount): string
    {
        return '₹'.lakh_format($amount);
    }
}

if (! function_exists('npr')) {
    /** Nepali rupees: NPR 2,00,000 ("NPR" so it is never mistaken for Indian rupees) */
    function npr(float|int|string|null $amount): string
    {
        return 'NPR '.lakh_format($amount);
    }
}

if (! function_exists('money')) {
    function money(float|int|string|null $amount, string $currency): string
    {
        return $currency === 'NPR' ? npr($amount) : inr($amount);
    }
}

if (! function_exists('visitor_currency')) {
    /**
     * The currency prices are shown in: the visitor's own choice (the INR / NPR
     * switch in the header, kept in a cookie), otherwise the site default.
     */
    function visitor_currency(): string
    {
        $chosen = strtoupper((string) request()->cookie('currency'));

        return in_array($chosen, ['INR', 'NPR'], true) ? $chosen : (setting('default_currency') === 'NPR' ? 'NPR' : 'INR');
    }
}

if (! function_exists('phone_digits')) {
    /** Digits only, for tel: and wa.me links. */
    function phone_digits(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(?string $message = null): string
    {
        // The WhatsApp number, or the phone number when no separate WhatsApp number is set.
        $url = 'https://wa.me/'.phone_digits(setting('whatsapp') ?: setting('phone'));

        return $message ? $url.'?text='.rawurlencode($message) : $url;
    }
}

if (! function_exists('clean_html')) {
    /**
     * Allow-list sanitiser for rich text entered in the admin panel, applied on
     * output so stored content can never inject scripts into the storefront.
     */
    function clean_html(?string $html): string
    {
        if (blank($html)) {
            return '';
        }

        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html);
        $html = strip_tags($html, '<p><br><strong><b><em><i><u><s><h2><h3><h4><ul><ol><li><a><img><blockquote><hr><table><thead><tbody><tr><td><th><figure><figcaption><span>');

        // Drop every attribute except a safe href/src/alt/title.
        return preg_replace_callback('/<([a-z0-9]+)(\s[^>]*)?>/i', function ($m) {
            $tag = strtolower($m[1]);
            $keep = '';

            if (! empty($m[2]) && preg_match_all('/\s(href|src|alt|title|target)\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $m[2], $attrs, PREG_SET_ORDER)) {
                foreach ($attrs as $attr) {
                    $name = strtolower($attr[1]);
                    $value = $attr[3] !== '' ? $attr[3] : ($attr[4] ?? '');

                    if (in_array($name, ['href', 'src'], true) && ! preg_match('#^(https?:|mailto:|tel:|/|\#)#i', trim($value))) {
                        continue;
                    }

                    $keep .= ' '.$name.'="'.e(html_entity_decode($value, ENT_QUOTES)).'"';
                }
            }

            if ($tag === 'a' && str_contains($keep, 'target=')) {
                $keep .= ' rel="noopener"';
            }
            if ($tag === 'img') {
                $keep .= ' loading="lazy"';
            }

            return '<'.$tag.$keep.'>';
        }, $html);
    }
}

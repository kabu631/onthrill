<?php

namespace App\Support;

/**
 * Tidies rich text before it is stored.
 *
 * Content imported from the old WordPress/Elementor site, or pasted into the
 * admin editor from Word or a page builder, tends to carry junk that would
 * otherwise show up on the page as visible text: stylesheet rules, builder
 * shortcodes, links that go nowhere, empty headings. This removes it.
 *
 * It is about tidiness, not security: every field is still passed through the
 * clean_html() allow-list when it is displayed.
 */
class ContentCleaner
{
    /** Leftover page-builder buttons; the site renders its own call/WhatsApp buttons. */
    private const BUTTON_LABELS = ['whatsapp', 'whatsapp now', 'whatsapp us', 'call us', 'call now', 'book now', 'read more', 'know more'];

    /**
     * @param  bool  $stripAttributes  also drop class/style/data-* attributes (used for imports;
     *                                 left off for the admin editor, which needs its own attributes)
     */
    public static function clean(?string $html, bool $stripAttributes = false): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        // Whole elements that never belong in content.
        $html = preg_replace('#<(script|style|noscript|iframe|form|object|embed|svg)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|link|meta|input|button)\b[^>]*/?>#i', '', $html);
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Markdown links that were pasted as plain text: [label](https://…)
        $html = preg_replace_callback('/\[([^\[\]<>]{1,120})\]\s?\(((?:https?:\/\/|mailto:|tel:)[^()\s<>]+)\)/i', function ($m) {
            return '<a href="'.htmlspecialchars($m[2], ENT_QUOTES).'">'.$m[1].'</a>';
        }, $html);
        $html = str_replace('**', '', $html);

        // Stylesheet text that lost its <style> tag. Only text that really looks like
        // CSS is removed: a "/*! … */" banner, or a ".class{prop:value}" / "#id{…}" /
        // "@media{…}" rule, so ordinary writing with braces is left alone.
        $html = preg_replace('#/\*!?.*?\*/#s', '', $html);
        $html = preg_replace_callback('/(?<=>|^)([^<>]+)(?=<|$)/', function ($m) {
            return preg_match('/(?:^|[\s};,])[.#@][a-z][\w-]*[^{}<>]*\{[^{}]*:[^{}]*\}/i', $m[1]) ? ' ' : $m[1];
        }, $html);

        // Page-builder shortcodes: [forminator_form id="240"], [/vc_row] …
        $html = preg_replace('/\[\/?[a-z][a-z0-9_-]*(?:\s+[a-z0-9_-]+=(?:"[^"]*"|\'[^\']*\'|[^\s\]]+))*\s*\/?\]/i', '', $html);

        $html = static::fixLinks($html);

        if ($stripAttributes) {
            $html = preg_replace_callback('/<([a-z][a-z0-9]*)\b([^>]*)>/i', function ($m) {
                preg_match_all('/\s(href|src|alt|title)\s*=\s*("[^"]*"|\'[^\']*\')/i', $m[2], $keep);
                $selfClose = str_ends_with(rtrim($m[2]), '/') ? ' /' : '';

                return '<'.strtolower($m[1]).implode('', $keep[0]).$selfClose.'>';
            }, $html);
            $html = preg_replace('#</?(div|span|section|article|font|center)\b[^>]*>#i', '', $html);
            $html = static::plainPunctuation($html);
        }

        $html = preg_replace('#<(/?)h1\b([^>]*)>#i', '<$1h2$2>', $html); // one <h1> per page: the title
        $html = str_replace(["\u{00A0}", '&nbsp;'], ' ', $html);

        // Empty leftovers, repeated until nothing changes (removing one can empty its parent).
        do {
            $before = $html;
            $html = preg_replace('#<(p|h[1-6]|li|ul|ol|strong|b|em|i|u|a|blockquote)\b[^>]*>(?:\s|<br\s*/?>)*</\1>#i', '', $html);
        } while ($html !== $before);

        // Line breaks hugging the edges of a block, and long runs of them.
        $html = preg_replace('#(<(?:p|li|h[1-6])\b[^>]*>)(?:\s|<br\s*/?>)+#i', '$1', $html);
        $html = preg_replace('#(?:\s|<br\s*/?>)+(</(?:p|li|h[1-6])>)#i', '$1', $html);
        $html = preg_replace('#(?:<br\s*/?>\s*){3,}#i', '<br><br>', $html);

        return trim(preg_replace('/[ \t]{2,}/', ' ', $html));
    }

    /**
     * Imported text only. The old site's articles used pictographs as bullets
     * (pointing hands, stars, telephones), stray "##" heading markers and long
     * dashes; these become ordinary punctuation.
     */
    private static function plainPunctuation(string $html): string
    {
        // A bare "Introduction" / "Overview" label as the very first line.
        $html = preg_replace('#^\s*<p>\s*(?:Introduction|Overview)\s*(?:<br\s*/?>\s*|</p>\s*<p>)#i', '<p>', $html);

        // Pictographs and their invisible modifiers.
        $html = preg_replace('/[\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{FE0F}\x{200D}]\s*/u', '', $html);
        $html = preg_replace('/(?<=>|^|\s)#{2,6}\s+/', '', $html);

        // A dash left dangling at the end of a line goes; one between words reads as a comma.
        $html = preg_replace('#\s*—\s*(?=</|<br|$)#u', '', $html);

        return preg_replace('/\s*—\s*/u', ', ', $html);
    }

    private static function fixLinks(string $html): string
    {
        return preg_replace_callback('#<a\b([^>]*)>(.*?)</a>#is', function ($m) {
            [$tag, $attributes, $inner] = $m;
            $label = strtolower(trim(preg_replace('/\s+/', ' ', strip_tags($inner))));

            if (! preg_match('/\shref\s*=\s*("([^"]*)"|\'([^\']*)\')/i', $attributes, $href)) {
                return $inner; // a link with no destination is just text
            }

            $url = html_entity_decode(trim($href[2] !== '' ? $href[2] : ($href[3] ?? '')), ENT_QUOTES);

            // Old page-builder "WhatsApp" / "Call Us" buttons pointing at a phone or chat.
            // (A deliberate "Book Now" link to a page is kept.)
            if (in_array($label, self::BUTTON_LABELS, true)
                && preg_match('#^(tel:|https?://\+?\d|https?://(api\.)?wa(\.me|tsapp\.com)|https?://api\.whatsapp)#i', $url)) {
                return '';
            }

            $fixed = static::fixUrl($url);

            if ($fixed === null) {
                return $inner;
            }

            return $fixed === $url ? $tag : str_replace($href[0], ' href="'.htmlspecialchars($fixed, ENT_QUOTES).'"', $tag);
        }, $html);
    }

    /** A usable URL, or null when the link cannot lead anywhere. */
    private static function fixUrl(string $url): ?string
    {
        // Phone numbers saved as web addresses: http://+919458914488
        if (preg_match('#^https?://(\+?\d[\d\s-]{6,})/?$#', $url, $m)) {
            return 'tel:'.preg_replace('/[^\d+]/', '', $m[1]);
        }

        // Tracking parameters added by tools the text was copied from.
        $url = preg_replace('/([?&])utm_[a-z]+=[^&#]*&?/i', '$1', $url);
        $url = rtrim($url, '?&');

        // Links to this site's own domain become relative, so they keep working on any domain.
        $url = preg_replace('#^https?://(www\.)?onthrill\.com#i', '', $url);

        if ($url === '' || $url === '#' || str_starts_with($url, 'javascript:')) {
            return null;
        }

        return preg_match('#^(https?://|mailto:|tel:|/|\#)#i', $url) ? $url : null;
    }
}

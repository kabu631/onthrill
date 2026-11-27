<?php

namespace App\Support;

/**
 * The stylesheet and script tags of the Angular build (frontend/ → public/app).
 * Angular writes them, with content hashes in the file names, into
 * public/app/index.html; they are copied from there into every page shell.
 */
class SpaAssets
{
    public const DIR = 'app';

    private static ?array $tags = null;

    /** @return list<string> HTML tags for <head>, or [] when the app has not been built yet */
    public static function tags(): array
    {
        if (self::$tags !== null) {
            return self::$tags;
        }

        $index = public_path(self::DIR.'/index.html');
        if (! is_file($index)) {
            return self::$tags = [];
        }

        $html = (string) file_get_contents($index);
        preg_match_all('#<link\b[^>]*\brel="(?:stylesheet|modulepreload)"[^>]*>|<script\b[^>]*\bsrc="[^"]+"[^>]*></script>#i', $html, $matches);

        return self::$tags = array_map(
            fn (string $tag) => preg_replace_callback('#\b(href|src)="(?!https?:|/)([^"]+)"#i', fn ($m) => $m[1].'="'.asset(self::DIR.'/'.$m[2]).'"', $tag),
            $matches[0],
        );
    }

    public static function built(): bool
    {
        return static::tags() !== [];
    }
}

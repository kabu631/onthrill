<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\Page;
use App\Models\TourCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Builds the site navigation from the content, so the menu always mirrors
 * what is managed in the admin panel. Cached; flushed whenever a destination,
 * tour type, package or page is saved or deleted.
 *
 * Links are stored as paths (/destinations/...), not full URLs: the cache is
 * shared by every request, so a full URL would carry whichever host or port
 * built it to every other visitor.
 */
class Menu
{
    private const CACHE_KEY = 'site.menu';

    /** Country slugs used in URLs (/destinations/country/nepal). */
    public const COUNTRY_SLUGS = [
        'nepal' => 'Nepal',
        'india' => 'India',
        'tibet' => 'Tibet (China)',
    ];

    public static function get(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addHour(), fn () => [
            'countries' => static::countryItems(),
            'tour_types' => static::tourTypeItems(),
            'header_pages' => static::pageItems('show_in_header'),
            'footer_pages' => static::pageItems('show_in_footer'),
        ]);
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function countrySlug(string $country): string
    {
        return array_search($country, self::COUNTRY_SLUGS, true) ?: Str::slug($country);
    }

    /** One entry per country that has destinations, listing them. */
    private static function countryItems(): array
    {
        $destinations = Destination::active()->orderBy('sort_order')->orderBy('name')->get(['name', 'slug', 'country']);

        return collect(self::COUNTRY_SLUGS)
            ->map(fn ($country, $slug) => [
                'name' => Str::before($country, ' ('),
                'url' => route('destinations.country', $slug, false),
                'children' => $destinations->where('country', $country)
                    ->map(fn (Destination $d) => ['name' => $d->name, 'url' => route('destinations.show', $d, false)])
                    ->values()->all(),
            ])
            ->filter(fn ($item) => $item['children'] !== [])
            ->values()
            ->all();
    }

    private static function tourTypeItems(): array
    {
        return TourCategory::active()
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (TourCategory $c) => ['name' => $c->name, 'url' => route('packages.category', $c, false)])
            ->all();
    }

    /** CMS pages placed in the header menu or footer (Website > Pages). */
    private static function pageItems(string $placement): array
    {
        return Page::active()
            ->where($placement, true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get()
            ->map(fn (Page $page) => ['name' => $page->menuLabel(), 'url' => route('pages.show', $page, false)])
            ->all();
    }
}

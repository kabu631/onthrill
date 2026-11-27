<?php

namespace App\Support;

use Illuminate\Support\Facades\Gate;

/**
 * Everything the Angular website needs before it draws its first page: the
 * public settings, every piece of wording (Admin > Page Content), the menu and
 * who is visiting. Embedded in each page shell, and served at /api/site.
 *
 * Only the settings listed here leave the server: mail passwords, payment
 * accounts and the like are never part of it.
 */
class SiteData
{
    /** Settings shown as they are. */
    private const PLAIN = [
        'site_name', 'tagline', 'footer_about', 'phone', 'whatsapp', 'email', 'address', 'business_hours',
        'map_embed_url', 'map_location', 'facebook', 'instagram', 'youtube', 'twitter', 'default_currency',
        'advance_percent', 'home_sections', 'hero_eyebrow', 'hero_text', 'hero_primary_label', 'hero_primary_url',
        'hero_secondary_label', 'hero_secondary_url', 'destinations_title', 'destinations_text', 'home_packages_title',
        'why_title', 'why_text', 'how_title', 'how_text', 'how_button_label', 'how_button_url', 'testimonials_title',
        'home_gallery_title', 'faq_title', 'faq_text', 'cta_title', 'cta_text', 'package_trust_points',
        'geo_latitude', 'geo_longitude', 'meta_title', 'meta_description',
    ];

    /** Settings that hold an image path, sent as a full URL. */
    private const IMAGES = ['logo', 'logo_footer', 'page_banner', 'footer_background', 'share_image'];

    public static function payload(): array
    {
        $user = auth()->user();

        return [
            'site' => static::settings(),
            'texts' => SiteTexts::all(),
            'menu' => Menu::get(),
            'currency' => visitor_currency(),
            'user' => Present::user($user),
            'can_book' => Gate::allows('book'),
        ];
    }

    private static function settings(): array
    {
        $site = [];
        foreach (self::PLAIN as $key) {
            $site[$key] = setting($key);
        }
        foreach (self::IMAGES as $key) {
            $site[$key] = media_url(setting($key));
        }

        $site['advance_percent'] = (float) $site['advance_percent'];
        $site['home_sections'] = array_values((array) $site['home_sections']);
        $site['package_trust_points'] = array_values(array_filter((array) $site['package_trust_points']));

        // Homepage slideshow. Without slides: one photo, with rotating words in the heading.
        $slides = collect((array) setting('hero_slides'))->filter(fn ($s) => filled($s['image'] ?? null))->values();
        if ($slides->isEmpty() && filled(setting('hero_background'))) {
            $slides = collect([['image' => setting('hero_background')]]);
        }
        $site['hero_slides'] = $slides->map(fn ($s) => [
            'image' => Present::wideImage($s['image']),
            'title' => $s['title'] ?? null,
            'place' => $s['place'] ?? null,
            'link' => $s['link'] ?? null,
            'credit' => $s['credit'] ?? null,
        ])->all();
        $site['hero_words'] = array_values(array_filter((array) setting('hero_words')));

        $site['why_items'] = array_values(array_filter((array) setting('why_items'), fn ($i) => filled($i['title'] ?? null)));
        $site['why_image'] = Present::image(setting('why_image'), 800);
        $site['why_image_small'] = Present::image(setting('why_image_small'), 400);
        $site['how_steps'] = array_values(array_map(
            fn ($s) => ['title' => $s['title'], 'text' => $s['text'] ?? '', 'icon' => media_url($s['icon'] ?? null)],
            array_filter((array) setting('how_steps'), fn ($s) => filled($s['title'] ?? null)),
        ));
        $site['faqs'] = array_values(array_filter((array) setting('faqs'), fn ($f) => filled($f['question'] ?? null) && filled($f['answer'] ?? null)));
        $site['cta_image'] = Present::image(setting('cta_image'), 1200);
        $site['budgets'] = Catalog::budgetOptions();

        return $site;
    }
}

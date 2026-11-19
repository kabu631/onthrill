<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\Package;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Str;

/**
 * Structured data (schema.org JSON-LD) for search engines and AI assistants.
 * Each method returns a plain array; json() turns it into a safe <script> body.
 * Only facts the site actually holds are included: no invented ratings.
 */
class Seo
{
    public static function json(array $data): string
    {
        return json_encode(array_filter($data, fn ($v) => $v !== null && $v !== []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG);
    }

    /** The business, on every page. */
    public static function organization(): array
    {
        $sameAs = collect(['facebook', 'instagram', 'youtube', 'twitter'])->map(fn ($n) => setting($n))->filter()->values()->all();
        $lat = setting('geo_latitude');
        $lng = setting('geo_longitude');

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'TravelAgency',
            '@id' => url('/').'#organization',
            'name' => setting('organization_name') ?: setting('site_name'),
            'url' => url('/'),
            'description' => setting('meta_description'),
            'logo' => media_url(setting('logo')) ?: null,
            'image' => media_url(setting('share_image')),
            'email' => setting('email') ?: null,
            'telephone' => setting('phone') ?: null,
            'address' => filled(setting('address')) ? ['@type' => 'PostalAddress', 'streetAddress' => setting('address'), 'addressLocality' => setting('geo_placename') ?: null] : null,
            'geo' => filled($lat) && filled($lng) ? ['@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng] : null,
            'areaServed' => ['Nepal', 'India', 'Tibet'],
            'currenciesAccepted' => 'INR, NPR',
            'sameAs' => $sameAs ?: null,
        ]);
    }

    /** The site itself, with its search box, on the homepage. */
    public static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            '@id' => url('/').'#website',
            'name' => setting('site_name'),
            'url' => url('/'),
            'publisher' => ['@id' => url('/').'#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => ['@type' => 'EntryPoint', 'urlTemplate' => route('search').'?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    /** @param array<string, string|null> $crumbs label => url (null for the current page) */
    public static function breadcrumbs(array $crumbs): array
    {
        $items = [['@type' => 'ListItem', 'position' => 1, 'name' => wording('breadcrumb_home'), 'item' => route('home')]];
        foreach ($crumbs as $label => $link) {
            $items[] = array_filter(['@type' => 'ListItem', 'position' => count($items) + 1, 'name' => $label, 'item' => $link ?: url()->current()]);
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    public static function destination(Destination $d): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => ['TouristDestination', 'PlaceOfWorship'],
            'name' => $d->name,
            'description' => $d->summary,
            'url' => $d->url(),
            'image' => media_url($d->image),
            'address' => ['@type' => 'PostalAddress', 'addressRegion' => $d->region, 'addressCountry' => Destination::COUNTRIES[$d->country] ?? $d->country],
            'geo' => $d->latitude && $d->longitude
                ? array_filter(['@type' => 'GeoCoordinates', 'latitude' => $d->latitude, 'longitude' => $d->longitude, 'elevation' => $d->altitude_m ? $d->altitude_m.' m' : null])
                : null,
            'touristType' => ['Pilgrims', 'Cultural tourists'],
            'isAccessibleForFree' => true,
        ];
    }

    public static function package(Package $p): array
    {
        $offers = $p->isOnRequest() ? null : collect(['INR', 'NPR'])->map(fn ($currency) => [
            '@type' => 'Offer',
            'price' => $p->price($currency),
            'priceCurrency' => $currency,
            'availability' => 'https://schema.org/InStock',
            'url' => $p->url(),
            'priceSpecification' => ['@type' => 'UnitPriceSpecification', 'price' => $p->price($currency), 'priceCurrency' => $currency, 'unitText' => 'per person'],
        ])->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $p->name,
            'description' => $p->summary,
            'url' => $p->url(),
            'image' => media_url($p->image),
            'touristType' => ['Pilgrims', 'Families'],
            'provider' => ['@id' => url('/').'#organization'],
            'itinerary' => [
                '@type' => 'ItemList',
                'numberOfItems' => count($p->itinerary ?? []),
                'itemListElement' => collect($p->itinerary ?? [])->values()->map(fn ($day, $i) => [
                    '@type' => 'ListItem',
                    'position' => $i + 1,
                    'item' => ['@type' => 'TouristAttraction', 'name' => 'Day '.($i + 1).': '.($day['title'] ?? ''), 'description' => Str::limit($day['description'] ?? '', 300)],
                ])->all(),
            ],
            'subjectOf' => $p->destinations->map(fn ($d) => ['@type' => 'TouristDestination', 'name' => $d->name, 'url' => $d->url()])->all() ?: null,
            'offers' => $offers,
        ];
    }

    /** @param list<array{question?: string, answer?: string}> $faqs */
    public static function faq(array $faqs): ?array
    {
        $faqs = array_values(array_filter($faqs, fn ($f) => filled($f['question'] ?? null) && filled($f['answer'] ?? null)));

        return $faqs ? [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($f) => [
                '@type' => 'Question',
                'name' => $f['question'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($f['answer'])],
            ], $faqs),
        ] : null;
    }

    /** A list of links, e.g. the packages on a listing page. */
    public static function itemList(string $name, iterable $items): array
    {
        // A paginated list describes the records on the current page.
        $list = collect($items instanceof Paginator ? $items->items() : $items)->values();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'numberOfItems' => $list->count(),
            'itemListElement' => $list->map(fn ($item, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => $item->url(), 'name' => $item->name])->all(),
        ];
    }
}

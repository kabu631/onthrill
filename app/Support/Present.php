<?php

namespace App\Support;

use App\Filament\Resources\Packages\PackageResource;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\TourCategory;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Turns models into the plain arrays the Angular website reads from /api.
 * Links are paths (/packages/...), so the app can route them without a reload.
 * Images come ready to use: a right-sized `src`, plus a sharper `srcset`.
 */
class Present
{
    /** @return array{src: string, srcset: ?string}|null */
    public static function image(?string $path, int $width): ?array
    {
        if (blank($path)) {
            return null;
        }

        $sources = Thumbnails::sources($path, $width);

        return $sources['src'] ? $sources : null;
    }

    /** Responsive set for full-width photographs (hero slides, banners). */
    public static function wideImage(?string $path): ?array
    {
        if (blank($path)) {
            return null;
        }

        $set = collect([800, 1200])->map(fn ($w) => Thumbnails::url($path, $w).' '.$w.'w')->push(media_url($path).' 1920w');

        return ['src' => media_url($path), 'srcset' => $set->unique()->implode(', ')];
    }

    public static function countryName(string $country): string
    {
        return Str::before($country, ' (');
    }

    /** Price per person in both currencies; null when the price is on request. */
    public static function prices(Package $p): ?array
    {
        if ($p->isOnRequest()) {
            return null;
        }

        return collect(['INR', 'NPR'])->mapWithKeys(fn ($currency) => [$currency => [
            'now' => $p->price($currency),
            'was' => $p->isOnSale($currency) ? $p->regularPrice($currency) : null,
            'off' => $p->discountPercent($currency),
        ]])->all();
    }

    public static function category(?TourCategory $c): ?array
    {
        return $c ? ['name' => $c->name, 'slug' => $c->slug, 'url' => route('packages.category', $c, false)] : null;
    }

    public static function packageCard(Package $p): array
    {
        $places = $p->relationLoaded('destinations') ? $p->destinations : collect();

        return [
            'id' => $p->id,
            'slug' => $p->slug,
            'name' => $p->name,
            'url' => route('packages.show', $p, false),
            'summary' => $p->summary,
            'duration' => $p->durationLabel(),
            'days' => $p->duration_days,
            'image' => static::image($p->image, 400),
            'category' => static::category($p->relationLoaded('category') ? $p->category : null),
            'places' => $places->pluck('name')->values()->all(),
            'start_point' => $p->start_point,
            'group_size' => $p->group_size,
            'max_altitude_m' => $p->max_altitude_m,
            'prices' => static::prices($p),
        ];
    }

    public static function destinationCard(Destination $d, int $width = 400): array
    {
        return [
            'id' => $d->id,
            'slug' => $d->slug,
            'name' => $d->name,
            'url' => route('destinations.show', $d, false),
            'country' => static::countryName($d->country),
            'country_url' => route('destinations.country', Menu::countrySlug($d->country), false),
            'region' => Str::before((string) $d->region, ','),
            'altitude_m' => $d->altitude_m,
            'summary' => $d->summary,
            'image' => static::image($d->image, $width),
            'tours' => $d->packages_count ?? null,
        ];
    }

    /** Photos for a grid that opens in a lightbox: a card-sized copy and a large one. */
    public static function photos(array $photos): array
    {
        return array_map(fn ($photo, $i) => [
            'image' => static::image($photo['path'], $i === 0 ? 800 : 400),
            'large' => Thumbnails::url($photo['path'], 1200),
            'alt' => $photo['alt'] ?? '',
            'credit' => $photo['credit'] ?? null,
            'credit_url' => $photo['credit_url'] ?? null,
        ], $photos, array_keys($photos));
    }

    public static function faqs(?array $faqs): array
    {
        return array_map(fn ($f) => ['question' => $f['question'], 'answer' => (string) ($f['answer'] ?? '')], Catalog::faqs($faqs));
    }

    public static function packageDetail(Package $p, ?User $user): array
    {
        return [
            ...static::packageCard($p),
            'end_point' => $p->end_point,
            'best_season' => $p->best_season,
            'difficulty' => $p->difficulty,
            'overview' => clean_html($p->overview),
            'highlights' => array_values(array_slice((array) $p->highlights, 0, 10)),
            'itinerary' => array_values(array_map(fn ($day) => ['title' => $day['title'] ?? '', 'description' => $day['description'] ?? null], (array) $p->itinerary)),
            'inclusions' => array_values((array) $p->inclusions),
            'exclusions' => array_values((array) $p->exclusions),
            'notes' => clean_html($p->notes),
            'faqs' => static::faqs($p->faqs),
            'photos' => static::photos(Catalog::packagePhotos($p)),
            'destinations' => $p->destinations->map(fn (Destination $d) => static::destinationCard($d, 160))->values()->all(),
            'updated' => $p->updated_at?->format('d F Y'),
            'edit_url' => $user?->can('update', $p) ? PackageResource::getUrl('edit', ['record' => $p]) : null,
        ];
    }

    public static function destinationDetail(Destination $d): array
    {
        $mapUrl = $d->latitude && $d->longitude
            ? 'https://www.google.com/maps/search/?api=1&query='.$d->latitude.','.$d->longitude
            : 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($d->name.', '.$d->region);

        return [
            ...static::destinationCard($d),
            'full_region' => $d->region,
            'banner' => static::wideImage($d->image),
            'image_credit' => $d->image_credit,
            'image_credit_url' => $d->image_credit_url,
            'description' => clean_html($d->description),
            'facts' => array_values(array_filter([
                ['icon' => 'mountain', 'key' => 'dest_fact_altitude', 'value' => $d->altitudeLabel()],
                ['icon' => 'sun', 'key' => 'dest_fact_best_time', 'value' => $d->best_time],
                ['icon' => 'clock', 'key' => 'dest_fact_hours', 'value' => $d->opening_hours],
                ['icon' => 'map', 'key' => 'dest_fact_airport', 'value' => $d->nearest_airport],
            ], fn ($fact) => filled($fact['value']))),
            'how_to_reach' => $d->how_to_reach,
            'map_url' => $mapUrl,
            'faqs' => static::faqs($d->faqs),
            'photos' => static::photos(Catalog::destinationPhotos($d)),
            'updated' => $d->updated_at?->format('d F Y'),
        ];
    }

    public static function testimonial(Testimonial $t): array
    {
        return [
            'name' => $t->name,
            'location' => $t->location,
            'content' => $t->content,
            'rating' => (int) $t->rating,
            'photo' => Thumbnails::url($t->photo, 96),
        ];
    }

    public static function galleryImage(GalleryImage $g, int $width = 400): array
    {
        return [
            'title' => $g->title,
            'image' => static::image($g->image, $width),
            'large' => Thumbnails::url($g->image, 1200),
            'credit' => $g->credit,
            'credit_url' => $g->credit_url,
        ];
    }

    public static function pageLink(Page $page): array
    {
        return ['title' => $page->menuLabel(), 'slug' => $page->slug, 'url' => route('pages.show', $page, false)];
    }

    public static function user(?User $user): ?array
    {
        return $user ? [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'is_staff' => $user->isStaff(),
        ] : null;
    }

    /** A booking with how to pay its advance, in the traveller's currency. */
    public static function booking(Booking $b): array
    {
        $amount = $b->advance ?? $b->total;

        return [
            'reference' => $b->reference,
            'name' => $b->name,
            'first_name' => Str::before($b->name, ' '),
            'email' => $b->email,
            'phone' => $b->phone,
            'package_name' => $b->package_name,
            'package_url' => $b->package ? route('packages.show', $b->package, false) : null,
            'travel_date' => $b->travel_date?->format('l, d F Y'),
            'travel_date_short' => $b->travel_date?->format('d M Y'),
            'adults' => (int) $b->adults,
            'children' => (int) $b->children,
            'travellers' => $b->travellers(),
            'nationality' => Booking::NATIONALITIES[$b->nationality] ?? $b->nationality,
            'travelling_from' => $b->travelling_from,
            'currency' => $b->currency,
            'price_per_person' => $b->price_per_person !== null ? (float) $b->price_per_person : null,
            'total' => $b->total !== null ? (float) $b->total : null,
            'advance' => $b->advance !== null ? (float) $b->advance : null,
            'status' => $b->status,
            'status_label' => $b->statusLabel(),
            'payment_status' => $b->payment_status,
            'payment_label' => Booking::PAYMENT_STATUSES[$b->payment_status] ?? $b->payment_status,
            'is_paid' => $b->isPaid(),
            'payment' => ! $b->isPaid() && $b->status !== 'cancelled' ? static::payment($b, $amount) : null,
        ];
    }

    /** Indian travellers (INR) pay by UPI or bank; Nepali travellers (NPR) by eSewa, Khalti or bank. */
    private static function payment(Booking $b, $amount): array
    {
        $india = $b->currency === 'INR';

        if ($india) {
            $qr = media_url(setting('upi_qr'));
            $ids = array_filter(['UPI ID' => trim((string) setting('upi_id')), 'UPI number' => trim((string) setting('upi_number'))]);
            $payee = trim((string) setting('upi_name')) ?: setting('site_name');
            $bank = trim((string) setting('bank_details_india'));
            // Opens the traveller's UPI app (phones only) with payee, amount and booking reference filled in.
            $upiLink = filled(setting('upi_id')) && $amount ? 'upi://pay?'.http_build_query([
                'pa' => trim((string) setting('upi_id')), 'pn' => $payee, 'am' => number_format((float) $amount, 2, '.', ''),
                'cu' => 'INR', 'tn' => 'Booking '.$b->reference,
            ], '', '&', PHP_QUERY_RFC3986) : null;
        } else {
            $qr = media_url(setting('nepal_qr'));
            $ids = array_filter(['eSewa ID' => trim((string) setting('esewa_id')), 'Khalti ID' => trim((string) setting('khalti_id'))]);
            $payee = setting('site_name');
            $bank = trim((string) setting('bank_details_nepal'));
            $upiLink = null;
        }

        return [
            'india' => $india,
            'amount' => $amount !== null ? (float) $amount : null,
            'qr' => $qr,
            'ids' => collect($ids)->map(fn ($value, $label) => ['label' => $label, 'value' => $value])->values()->all(),
            'payee' => $payee,
            'bank' => $bank,
            'upi_link' => $upiLink,
            'note' => trim((string) setting('payment_note')),
            'screenshot_message' => "Namaste, I have paid the advance for booking {$b->reference}. Sharing the payment screenshot.",
        ];
    }

    /** One page of results, with what the pagination links need. */
    public static function paginated(LengthAwarePaginator $page, callable $map): array
    {
        return [
            'data' => collect($page->items())->map($map)->values()->all(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'last_page' => $page->lastPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
            ],
        ];
    }
}

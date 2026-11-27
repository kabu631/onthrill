<?php

namespace App\Support;

use App\Models\Destination;
use App\Models\Package;
use App\Models\TourCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * The queries behind the public website, shared by the JSON API (read by the
 * Angular app) and the page shells (read by search engines).
 */
class Catalog
{
    public const SORTS = ['recommended', 'duration', 'price_low', 'price_high'];

    /** ?duration= value => [fewest days, most days] */
    public const DURATIONS = [
        'short' => [1, 5],
        'medium' => [6, 10],
        'long' => [11, null],
    ];

    /** ?budget= value => [lowest, highest] price per person in Indian rupees. */
    public const BUDGETS = [
        'under-15000' => [null, 15000],
        '15000-30000' => [15000, 30000],
        '30000-60000' => [30000, 60000],
        'over-60000' => [60000, null],
    ];

    /** What the traveller pays per person in INR: the offer price when there is one. */
    private const PRICE_INR = 'COALESCE(sale_price_inr, price_inr)';

    /** Budget choices with their wording in both currencies (NPR at the rate in Site Settings). */
    public static function budgetOptions(): array
    {
        $label = function (?int $from, ?int $to, string $currency) {
            $amount = fn (int $inr) => money($currency === 'NPR' ? Package::toNpr($inr) : $inr, $currency);

            return match (true) {
                $from === null => wording('budget_under', ['amount' => $amount($to)]),
                $to === null => wording('budget_over', ['amount' => $amount($from)]),
                default => wording('budget_between', ['from' => $amount($from), 'to' => $amount($to)]),
            };
        };

        return collect(self::BUDGETS)->map(fn ($range, $value) => [
            'value' => $value,
            'INR' => $label($range[0], $range[1], 'INR'),
            'NPR' => $label($range[0], $range[1], 'NPR'),
        ])->values()->all();
    }

    /** The words of a search, lower-cased; at most five, each at least two letters. */
    public static function words(string $term): array
    {
        $words = preg_split('/[\s,]+/u', mb_strtolower(trim($term)), -1, PREG_SPLIT_NO_EMPTY);

        return array_slice(array_values(array_filter($words, fn ($w) => mb_strlen($w) >= 2)), 0, 5);
    }

    private static function like(string $word): string
    {
        return '%'.addcslashes($word, '%_\\').'%';
    }

    /**
     * Packages matching every word of the search, in the name, summary or start,
     * a place the tour visits (name, region, country) or its tour type. Spaces are
     * ignored too, so "chardham" finds "Char Dham".
     */
    public static function matchPackages(Builder $query, string $term): Builder
    {
        foreach (static::words($term) as $word) {
            $like = static::like($word);
            $query->where(fn ($q) => $q->where('name', 'like', $like)
                ->orWhere('summary', 'like', $like)
                ->orWhere('start_point', 'like', $like)
                ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", [$like])
                ->orWhereHas('destinations', fn ($d) => $d->where('is_active', true)->where(fn ($d) => $d
                    ->where('name', 'like', $like)->orWhere('region', 'like', $like)->orWhere('country', 'like', $like)
                    ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", [$like])))
                ->orWhereHas('category', fn ($c) => $c->where('name', 'like', $like)->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", [$like])));
        }

        return $query;
    }

    /** Destinations matching every word, in the name, summary, region or country. */
    public static function matchDestinations(Builder $query, string $term): Builder
    {
        foreach (static::words($term) as $word) {
            $like = static::like($word);
            $query->where(fn ($q) => $q->where('name', 'like', $like)
                ->orWhere('summary', 'like', $like)
                ->orWhere('region', 'like', $like)
                ->orWhere('country', 'like', $like)
                ->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", [$like]));
        }

        return $query;
    }

    /** Matches in the name first. */
    private static function nameFirst(Builder $query, string $term): Builder
    {
        $first = static::words($term)[0] ?? '';

        return $query->orderByRaw('CASE WHEN name LIKE ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END', [$first.'%', static::like($first)])
            ->orderBy('sort_order');
    }

    /** Packages with what a package card shows. */
    public static function packages(): Builder
    {
        return Package::active()->with(['category', 'destinations' => fn ($q) => $q->active()]);
    }

    /** Destinations with the number of visible tours that visit each. */
    public static function destinations(): Builder
    {
        return Destination::active()->orderBy('sort_order')->withCount(['packages' => fn ($q) => $q->active()]);
    }

    /** Country => destinations, in menu order; countries without destinations are left out. */
    public static function grouped(Collection $destinations): array
    {
        return collect(array_keys(Destination::COUNTRIES))
            ->mapWithKeys(fn ($country) => [$country => $destinations->where('country', $country)->values()])
            ->filter(fn ($list) => $list->isNotEmpty())
            ->all();
    }

    /**
     * All packages or one tour type, optionally only those visiting one destination
     * (?destination=slug) and/or of one length (?duration=short|medium|long).
     *
     * Also by words (?q=) and price per person (?budget=).
     *
     * @return array{category: ?TourCategory, destination: ?Destination, duration: ?string, budget: ?string, term: string, sort: string, packages: LengthAwarePaginator}
     */
    public static function listing(Request $request, ?TourCategory $category): array
    {
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'recommended';
        $destination = $request->filled('destination')
            ? Destination::active()->where('slug', (string) $request->query('destination'))->first()
            : null;
        $duration = array_key_exists((string) $request->query('duration'), self::DURATIONS) ? (string) $request->query('duration') : null;
        [$minDays, $maxDays] = $duration ? self::DURATIONS[$duration] : [null, null];
        $budget = array_key_exists((string) $request->query('budget'), self::BUDGETS) ? (string) $request->query('budget') : null;
        [$minPrice, $maxPrice] = $budget ? self::BUDGETS[$budget] : [null, null];
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);
        $term = static::words($term) ? $term : '';

        $query = static::packages()
            ->when($term !== '', fn ($q) => static::matchPackages($q, $term))
            ->when($budget, fn ($q) => $q->whereNotNull('price_inr'))
            ->when($minPrice, fn ($q) => $q->whereRaw(self::PRICE_INR.' >= ?', [$minPrice]))
            ->when($maxPrice, fn ($q) => $q->whereRaw(self::PRICE_INR.' <= ?', [$maxPrice]))
            ->when($category, fn ($q) => $q->where('tour_category_id', $category->id))
            ->when($destination, fn ($q) => $q->whereHas('destinations', fn ($d) => $d->whereKey($destination->id)))
            ->when($minDays, fn ($q) => $q->where('duration_days', '>=', $minDays))
            ->when($maxDays, fn ($q) => $q->where('duration_days', '<=', $maxDays));

        match ($sort) {
            'duration' => $query->orderBy('duration_days'),
            // Packages without a price ("on request") come last either way.
            'price_low' => $query->orderByRaw('price_inr IS NULL')->orderByRaw(self::PRICE_INR),
            'price_high' => $query->orderByRaw('price_inr IS NULL')->orderByRaw(self::PRICE_INR.' DESC'),
            // A search puts the closest matches first.
            default => $term !== '' ? static::nameFirst($query, $term) : $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };

        return [
            'category' => $category,
            'destination' => $destination,
            'duration' => $duration,
            'budget' => $budget,
            'term' => $term,
            'sort' => $sort,
            'packages' => $query->paginate(12)->withQueryString(),
        ];
    }

    /**
     * Packages, destinations and tour types matching every word of the search,
     * closest matches first.
     *
     * @return array{0: Collection, 1: Collection, 2: Collection}
     */
    public static function search(string $term, int $limit): array
    {
        if (! static::words($term)) {
            return [collect(), collect(), collect()];
        }

        $types = TourCategory::active()->withCount(['packages' => fn ($q) => $q->active()]);
        foreach (static::words($term) as $word) {
            $like = static::like($word);
            $types->where(fn ($q) => $q->where('name', 'like', $like)->orWhereRaw("REPLACE(name, ' ', '') LIKE ?", [$like]));
        }

        return [
            static::nameFirst(static::matchPackages(static::packages(), $term), $term)->limit($limit)->get(),
            static::nameFirst(static::matchDestinations(Destination::active()->withCount(['packages' => fn ($q) => $q->active()]), $term), $term)->limit($limit)->get(),
            $types->orderBy('sort_order')->limit(3)->get(),
        ];
    }

    /** The package's own photos, then those of the places it visits (each with its credit). */
    public static function packagePhotos(Package $p): array
    {
        return collect([$p->image, ...($p->gallery ?? [])])->filter()
            ->map(fn ($path) => ['path' => $path, 'alt' => $p->name, 'destination' => $p->destinations->firstWhere('image', $path)])
            ->concat($p->destinations->filter(fn ($d) => filled($d->image))->map(fn ($d) => ['path' => $d->image, 'alt' => $d->name, 'destination' => $d]))
            ->unique('path')->take(8)->values()
            ->map(fn ($photo) => [
                'path' => $photo['path'],
                'alt' => $photo['destination']?->name ?? $photo['alt'],
                'credit' => $photo['destination']?->image_credit,
                'credit_url' => $photo['destination']?->image_credit_url,
            ])->all();
    }

    public static function destinationPhotos(Destination $d): array
    {
        return collect([$d->image, ...($d->gallery ?? [])])->filter()->unique()->values()
            ->map(fn ($path, $i) => [
                'path' => $path,
                'alt' => $d->name,
                'credit' => $i === 0 ? $d->image_credit : null,
                'credit_url' => $i === 0 ? $d->image_credit_url : null,
            ])->all();
    }

    /** Question-and-answer pairs that have a question. */
    public static function faqs(?array $faqs): array
    {
        return array_values(array_filter((array) $faqs, fn ($f) => filled($f['question'] ?? null)));
    }
}

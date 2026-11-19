<?php

namespace App\Models;

use App\Models\Concerns\CleansRichText;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A tour package. Prices are per person, in Indian rupees; the Nepali rupee
 * price is worked out with the rate in Site Settings unless it is set here.
 */
#[Fillable([
    'tour_category_id', 'name', 'slug', 'duration_days', 'duration_nights', 'start_point', 'end_point', 'summary',
    'overview', 'highlights', 'itinerary', 'inclusions', 'exclusions', 'notes', 'faqs', 'price_inr', 'sale_price_inr',
    'price_npr', 'sale_price_npr', 'best_season', 'group_size', 'difficulty', 'max_altitude_m', 'image', 'gallery',
    'meta_title', 'meta_description', 'is_featured', 'is_active', 'sort_order',
])]
class Package extends Model
{
    use CleansRichText, HasSlug;

    public const DIFFICULTIES = [
        'Easy' => 'Easy',
        'Moderate' => 'Moderate',
        'Challenging' => 'Challenging',
    ];

    public function richTextFields(): array
    {
        return ['overview', 'notes'];
    }

    protected function casts(): array
    {
        return [
            'highlights' => 'array',
            'itinerary' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'faqs' => 'array',
            'gallery' => 'array',
            'price_inr' => 'decimal:2',
            'sale_price_inr' => 'decimal:2',
            'price_npr' => 'decimal:2',
            'sale_price_npr' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TourCategory::class, 'tour_category_id');
    }

    public function destinations(): BelongsToMany
    {
        return $this->belongsToMany(Destination::class)->withPivot('sort_order')->orderByPivot('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function url(): string
    {
        return route('packages.show', $this);
    }

    /** "5 Nights / 6 Days" */
    public function durationLabel(): string
    {
        $days = $this->duration_days.' '.($this->duration_days === 1 ? 'Day' : 'Days');

        return $this->duration_nights > 0
            ? $this->duration_nights.' '.($this->duration_nights === 1 ? 'Night' : 'Nights').' / '.$days
            : $days;
    }

    /** No price set: the team quotes on request. */
    public function isOnRequest(): bool
    {
        return $this->price_inr === null;
    }

    /** Regular price per person in the currency. */
    public function regularPrice(string $currency): ?float
    {
        if ($this->isOnRequest()) {
            return null;
        }

        return $currency === 'NPR'
            ? (float) ($this->price_npr ?? static::toNpr((float) $this->price_inr))
            : (float) $this->price_inr;
    }

    /** What the traveller pays per person: the offer price when there is one. */
    public function price(string $currency): ?float
    {
        if ($this->isOnRequest()) {
            return null;
        }

        $sale = $currency === 'NPR'
            ? ($this->sale_price_npr ?? ($this->sale_price_inr !== null ? static::toNpr((float) $this->sale_price_inr) : null))
            : $this->sale_price_inr;

        return $sale !== null && (float) $sale < $this->regularPrice($currency) ? (float) $sale : $this->regularPrice($currency);
    }

    public function isOnSale(string $currency = 'INR'): bool
    {
        return ! $this->isOnRequest() && $this->price($currency) < $this->regularPrice($currency);
    }

    public function discountPercent(string $currency = 'INR'): int
    {
        return $this->isOnSale($currency) ? (int) round(100 - $this->price($currency) / $this->regularPrice($currency) * 100) : 0;
    }

    /** Indian rupees to Nepali rupees at the rate in Site Settings, rounded to the nearest 10. */
    public static function toNpr(float $inr): float
    {
        return round($inr * (float) (setting('npr_per_inr') ?: 1.6), -1);
    }
}

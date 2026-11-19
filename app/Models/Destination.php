<?php

namespace App\Models;

use App\Models\Concerns\CleansRichText;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** A temple, shrine or sacred place that tours visit. */
#[Fillable([
    'name', 'slug', 'country', 'region', 'summary', 'description', 'altitude_m', 'best_time', 'opening_hours',
    'nearest_airport', 'how_to_reach', 'latitude', 'longitude', 'faqs', 'image', 'gallery', 'image_credit',
    'image_credit_url', 'meta_title', 'meta_description', 'is_featured', 'is_active', 'sort_order',
])]
class Destination extends Model
{
    use CleansRichText, HasSlug;

    /** Countries in menu order, with the ISO code used in structured data. */
    public const COUNTRIES = [
        'Nepal' => 'NP',
        'India' => 'IN',
        'Tibet (China)' => 'CN',
    ];

    public function richTextFields(): array
    {
        return ['description'];
    }

    protected function casts(): array
    {
        return [
            'faqs' => 'array',
            'gallery' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class)->withPivot('sort_order');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function url(): string
    {
        return route('destinations.show', $this);
    }

    /** "3,710 m (12,172 ft)" */
    public function altitudeLabel(): ?string
    {
        return $this->altitude_m ? number_format($this->altitude_m).' m ('.number_format(round($this->altitude_m * 3.28084)).' ft)' : null;
    }

    /** Key facts in display order; empty ones are left out. */
    public function facts(): array
    {
        return array_filter([
            'Location' => trim(implode(', ', array_filter([$this->region, $this->country])), ', '),
            'Altitude' => $this->altitudeLabel(),
            'Best time to visit' => $this->best_time,
            'Opening hours' => $this->opening_hours,
            'Nearest airport' => $this->nearest_airport,
        ]);
    }
}

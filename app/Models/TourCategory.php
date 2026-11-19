<?php

namespace App\Models;

use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** A kind of tour, e.g. "Nepal Pilgrimage Tours" or "Kailash Mansarovar Yatra". */
#[Fillable(['name', 'slug', 'description', 'image', 'meta_title', 'meta_description', 'is_active', 'show_in_menu', 'sort_order'])]
class TourCategory extends Model
{
    use HasSlug;

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'show_in_menu' => 'boolean'];
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function url(): string
    {
        return route('packages.category', $this);
    }
}

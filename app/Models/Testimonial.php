<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'location', 'content', 'rating', 'photo', 'is_active', 'sort_order'])]
class Testimonial extends Model
{
    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'rating' => 'integer'];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }
}

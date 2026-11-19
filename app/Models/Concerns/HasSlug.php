<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Fills a unique slug from the name/title when none is given.
 */
trait HasSlug
{
    public static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            $source = $model->slug ?: ($model->name ?? $model->title ?? '');
            $base = Str::slug($source) ?: Str::lower(Str::random(8));

            $slug = $base;
            $i = 2;
            while (static::where('slug', $slug)->whereKeyNot($model->getKey())->exists()) {
                $slug = $base.'-'.$i++;
            }

            $model->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}

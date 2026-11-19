<?php

namespace App\Models;

use App\Models\Concerns\CleansRichText;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'menu_label', 'slug', 'content', 'is_active', 'show_in_header', 'show_in_footer', 'sort_order', 'meta_title', 'meta_description'])]
class Page extends Model
{
    use CleansRichText, HasSlug;

    public function richTextFields(): array
    {
        return ['content'];
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_in_header' => 'boolean',
            'show_in_footer' => 'boolean',
        ];
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /** Short name used in menus; falls back to the title. */
    public function menuLabel(): string
    {
        return $this->menu_label ?: $this->title;
    }
}

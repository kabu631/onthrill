<?php

namespace App\Models\Concerns;

use App\Support\ContentCleaner;

/**
 * Runs the model's rich-text fields through ContentCleaner whenever it is
 * saved, so junk pasted into the admin editor never reaches the website.
 * The model lists its fields in a `richTextFields()` method.
 */
trait CleansRichText
{
    public static function bootCleansRichText(): void
    {
        static::saving(function ($model) {
            foreach ($model->richTextFields() as $field) {
                if ($model->isDirty($field)) {
                    $model->{$field} = ContentCleaner::clean($model->{$field});
                }
            }
        });
    }

    /** @return list<string> */
    abstract public function richTextFields(): array;
}

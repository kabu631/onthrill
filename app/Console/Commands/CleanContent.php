<?php

namespace App\Console\Commands;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Page;
use App\Support\ContentCleaner;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('content:clean {--dry-run : Only report what would change}')]
#[Description('Remove leftover page-builder code, broken links and empty tags from stored content')]
class CleanContent extends Command
{
    public function handle(): int
    {
        $changed = 0;

        foreach ([Destination::class, Package::class, Page::class] as $modelClass) {
            foreach ($modelClass::all() as $model) {
                $updates = [];

                foreach ($model->richTextFields() as $field) {
                    // Imported content is also stripped of class/style/data attributes.
                    $clean = ContentCleaner::clean($model->{$field}, stripAttributes: true);
                    if ($clean !== $model->{$field}) {
                        $updates[$field] = $clean;
                    }
                }

                if ($updates) {
                    $changed++;
                    $this->line(sprintf('  %-8s %-55s %s', class_basename($model), $model->slug, implode(', ', array_keys($updates))));

                    if (! $this->option('dry-run')) {
                        // Bypass the saving hook: the cleaning is already done (with attribute stripping).
                        $model->forceFill($updates)->saveQuietly();
                    }
                }
            }
        }

        $this->info($this->option('dry-run') ? "{$changed} records would be cleaned." : "{$changed} records cleaned.");

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\TourCategory;
use App\Support\Settings;
use App\Support\Thumbnails;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('images:thumbs')]
#[Description('Make the small/medium/large copies of every uploaded image (run after deploying or importing images)')]
class GenerateThumbnails extends Command
{
    public function handle(): int
    {
        $paths = static::allImagePaths();
        $made = 0;

        $this->withProgressBar($paths, function (string $path) use (&$made) {
            $made += Thumbnails::generateAll($path);
        });

        $this->newLine();
        $this->info(count($paths)." images checked, {$made} new copies made.");

        return self::SUCCESS;
    }

    /** @return list<string> every image path the website shows */
    public static function allImagePaths(): array
    {
        $paths = collect()
            ->merge(Destination::pluck('image'))
            ->merge(Destination::pluck('gallery')->flatten())
            ->merge(Package::pluck('image'))
            ->merge(Package::pluck('gallery')->flatten())
            ->merge(TourCategory::pluck('image'))
            ->merge(GalleryImage::pluck('image'))
            ->merge(Testimonial::pluck('photo'))
            ->merge([Settings::get('upi_qr'), Settings::get('nepal_qr')])
            // Photos chosen in Page Content (homepage slideshow, "why" and call-to-action photos)
            ->merge(collect((array) Settings::get('hero_slides'))->pluck('image'))
            ->merge(collect((array) Settings::get('how_steps'))->pluck('icon'))
            ->merge([Settings::get('hero_background'), Settings::get('why_image'), Settings::get('why_image_small'), Settings::get('cta_image')]);

        return $paths->filter(fn ($p) => is_string($p) && $p !== '')->unique()->values()->all();
    }
}

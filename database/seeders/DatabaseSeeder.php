<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Page;
use App\Models\TourCategory;
use App\Models\User;
use App\Support\Access;
use App\Support\Menu;
use App\Support\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Installs the starting content: destinations, tour types and packages, pages,
 * a photo gallery and the first admin user.
 * Safe to re-run: existing records are matched by slug and updated.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyAssets();
        Access::sync(); // roles & permissions
        $this->seedAdmin();
        $this->seedDestinations();
        $this->seedPackages();
        $this->seedPages();
        $this->seedGallery();

        Menu::flush();
        Settings::flush();

        // Small/medium/large copies of every image, so pages load fast from the first visit.
        $this->command?->call('images:thumbs');
    }

    /** Copy bundled images into storage/app/public so they are served from /storage. */
    private function copyAssets(): void
    {
        $disk = Storage::disk('public');

        foreach (File::directories(__DIR__.'/assets') as $dir) {
            foreach (File::files($dir) as $file) {
                $target = basename($dir).'/'.$file->getFilename();
                if (! $disk->exists($target)) {
                    $disk->put($target, File::get($file->getPathname()));
                }
            }
        }
    }

    private function seedAdmin(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@onthrill.com');

        if (User::where('email', $email)->exists()) {
            return;
        }

        $password = env('ADMIN_PASSWORD') ?: Str::password(16, symbols: false);

        $admin = new User;
        $admin->forceFill([
            'name' => 'Admin',
            'email' => $email,
            'password' => $password,
            'email_verified_at' => now(),
        ])->save();
        $admin->assignRole(Access::SUPER_ADMIN);

        $this->command?->warn("Admin account created: {$email} / {$password}  (change it after first login)");
    }

    /** Credit line for a bundled photo, from data/photos.php. */
    private function credit(string $path): array
    {
        static $credits = null;
        $credits ??= require __DIR__.'/data/photos.php';

        return $credits[$path] ?? ['credit' => null, 'url' => null];
    }

    private function seedDestinations(): void
    {
        foreach (require __DIR__.'/data/destinations.php' as $order => $data) {
            $main = "destinations/{$data['image']}.webp";
            $gallery = collect(range(1, 3))
                ->map(fn ($n) => "destinations/{$data['image']}-{$n}.webp")
                ->filter(fn ($path) => File::exists(__DIR__.'/assets/'.$path))
                ->values()->all();
            $credit = $this->credit($main);

            Destination::updateOrCreate(['slug' => $data['slug']], [
                'name' => $data['name'],
                'country' => $data['country'],
                'region' => $data['region'],
                'summary' => $data['summary'],
                'description' => $data['description'],
                'altitude_m' => $data['altitude_m'] ?? null,
                'best_time' => $data['best_time'] ?? null,
                'opening_hours' => $data['opening_hours'] ?? null,
                'nearest_airport' => $data['nearest_airport'] ?? null,
                'how_to_reach' => $data['how_to_reach'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'faqs' => $data['faqs'] ?: null,
                'image' => $main,
                'gallery' => $gallery ?: null,
                'image_credit' => $credit['credit'],
                'image_credit_url' => $credit['url'],
                'is_featured' => $data['featured'],
                'is_active' => true,
                'sort_order' => $order,
            ]);
        }
    }

    private function seedPackages(): void
    {
        $data = require __DIR__.'/data/packages.php';

        $categories = [];
        foreach ($data['categories'] as $order => $category) {
            $categories[$category['slug']] = TourCategory::updateOrCreate(['slug' => $category['slug']], [
                'name' => $category['name'],
                'description' => $category['description'],
                'sort_order' => $order,
                'is_active' => true,
                'show_in_menu' => true,
            ]);
        }

        $destinations = Destination::pluck('id', 'slug');

        foreach ($data['packages'] as $order => $p) {
            // A package shows the photo of its first destination.
            $first = Destination::where('slug', $p['destinations'][0])->first();

            $package = Package::updateOrCreate(['slug' => $p['slug']], [
                'tour_category_id' => $categories[$p['category']]->id,
                'name' => $p['name'],
                'duration_days' => $p['days'],
                'duration_nights' => $p['nights'],
                'start_point' => $p['start'],
                'end_point' => $p['end'],
                'summary' => $p['summary'],
                'overview' => $p['overview'],
                'highlights' => $p['highlights'],
                'itinerary' => $p['itinerary'],
                'inclusions' => $p['inclusions'],
                'exclusions' => $p['exclusions'],
                'notes' => $p['notes'] ?? null,
                'faqs' => $p['faqs'] ?: null,
                'price_inr' => $p['price'],
                'sale_price_inr' => $p['sale'],
                'best_season' => $p['best_season'],
                'group_size' => $p['group_size'],
                'difficulty' => $p['difficulty'],
                'max_altitude_m' => $p['max_altitude'],
                'image' => $first?->image,
                'is_featured' => $p['featured'],
                'is_active' => true,
                'sort_order' => $order,
            ]);

            $package->destinations()->sync(collect($p['destinations'])
                ->mapWithKeys(fn ($slug, $i) => [$destinations[$slug] => ['sort_order' => $i]])
                ->all());
        }

        // Tour type cards use the photo of their first package.
        foreach ($categories as $category) {
            $category->update(['image' => $category->packages()->orderBy('sort_order')->value('image')]);
        }
    }

    private function seedPages(): void
    {
        foreach (require __DIR__.'/data/pages.php' as $page) {
            Page::updateOrCreate(['slug' => $page['slug']], [
                'title' => $page['title'],
                'content' => $page['content'],
                'meta_description' => $page['meta_description'] ?? null,
                'menu_label' => $page['menu_label'] ?? null,
                'show_in_header' => $page['show_in_header'] ?? false,
                'show_in_footer' => $page['show_in_footer'] ?? true,
                'sort_order' => $page['sort_order'] ?? 0,
                'is_active' => true,
            ]);
        }

        // Every bundled photo is freely licensed and needs its photographer credited.
        $rows = collect(require __DIR__.'/data/photos.php')
            ->unique('url')
            ->map(fn ($c, $path) => '<li><a href="'.e($c['url']).'" target="_blank">'.e(Str::after($c['credit'], 'Photo: ')).'</a>'
                .($c['license_url'] ? ' (<a href="'.e($c['license_url']).'" target="_blank">licence</a>)' : '').'</li>')
            ->implode("\n");

        Page::updateOrCreate(['slug' => 'photo-credits'], [
            'title' => 'Photo Credits',
            'content' => "<p>The photographs on this website are published on Wikimedia Commons under free licences. We thank the photographers listed below.</p>\n<ul>\n{$rows}\n</ul>",
            'meta_description' => 'Credits for the photographs used on this website.',
            'show_in_header' => false,
            'show_in_footer' => true,
            'sort_order' => 10,
            'is_active' => true,
        ]);
    }

    /** The gallery starts with the second photo of each destination. */
    private function seedGallery(): void
    {
        if (GalleryImage::exists()) {
            return;
        }

        foreach (Destination::orderBy('sort_order')->get() as $i => $destination) {
            $path = $destination->gallery[0] ?? null;
            if (! $path) {
                continue;
            }
            $credit = $this->credit($path);
            GalleryImage::create([
                'title' => $destination->name,
                'image' => $path,
                'credit' => $credit['credit'],
                'credit_url' => $credit['url'],
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}

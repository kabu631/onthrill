<?php

namespace Tests\Feature;

use App\Models\Destination;
use App\Models\Package;
use App\Support\Thumbnails;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ThumbnailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    private function image(string $path, int $width, int $height): string
    {
        $img = imagecreatetruecolor($width, $height);
        imagefill($img, 0, 0, imagecolorallocate($img, 255, 131, 53));
        ob_start();
        imagewebp($img);
        Storage::disk('public')->put($path, ob_get_clean());

        return $path;
    }

    public function test_serves_right_sized_copies_with_a_sharper_2x_option(): void
    {
        $path = $this->image('destinations/big.webp', 1000, 700);

        $sources = Thumbnails::sources($path, 400);

        $this->assertStringEndsWith('/storage/thumbs/400/destinations/big.webp', $sources['src']);
        $this->assertStringContainsString('/storage/thumbs/800/destinations/big.webp 2x', $sources['srcset']);
        Storage::disk('public')->assertExists('thumbs/400/destinations/big.webp');
        $this->assertSame(400, getimagesize(Storage::disk('public')->path('thumbs/400/destinations/big.webp'))[0]);
    }

    public function test_never_upscales_and_handles_requests_beyond_the_largest_size(): void
    {
        $small = $this->image('destinations/small.webp', 300, 200);
        $this->assertStringEndsWith('/storage/destinations/small.webp', Thumbnails::url($small, 400)); // original is already small

        $big = $this->image('destinations/big.webp', 1000, 700);
        $this->assertStringEndsWith('/storage/destinations/big.webp', Thumbnails::url($big, 2000)); // larger than every size
    }

    public function test_a_page_view_makes_only_a_few_copies_and_serves_originals_for_the_rest(): void
    {
        $paths = [];
        foreach (range(1, 6) as $i) {
            $paths[] = $this->image("destinations/p{$i}.webp", 1000, 700);
        }

        $urls = array_map(fn ($p) => Thumbnails::url($p, 400), $paths);

        $made = count(array_filter($urls, fn ($u) => str_contains($u, '/thumbs/')));
        $this->assertSame(4, $made);                                         // budget for one request
        $this->assertStringEndsWith('/storage/destinations/p6.webp', $urls[5]); // the rest: original, still works

        Thumbnails::resetRequestBudget();                                    // next page view…
        $this->assertStringContainsString('/thumbs/400/', Thumbnails::url($paths[5], 400));
    }

    public function test_missing_or_external_images_fall_back_safely(): void
    {
        $this->assertNull(Thumbnails::url(null, 400));
        $this->assertSame('https://example.com/x.jpg', Thumbnails::url('https://example.com/x.jpg', 400));
        $this->assertStringEndsWith('/storage/destinations/missing.webp', Thumbnails::url('destinations/missing.webp', 400));
    }

    public function test_detail_pages_render_with_image_galleries(): void
    {
        $main = $this->image('destinations/main.webp', 1600, 1000);
        $extra = $this->image('destinations/extra.webp', 1000, 700);
        $destination = Destination::create(['name' => 'Manakamana', 'country' => 'Nepal', 'image' => $main, 'gallery' => [$extra], 'is_active' => true]);
        $package = Package::create(['name' => 'Manakamana Darshan', 'duration_days' => 1, 'price_inr' => 4500, 'image' => $this->image('packages/m.webp', 1200, 800), 'is_active' => true]);
        $package->destinations()->attach($destination);

        // Copies are made when images are uploaded (or by this command after an import).
        $this->artisan('images:thumbs')->assertSuccessful();

        // Photo grid: the main photo at 800 px (the largest copy for sharp screens), the others at 400 px.
        $this->get('/destinations/manakamana')->assertOk()
            ->assertSee('thumbs/800/destinations/main.webp', false)
            ->assertSee('thumbs/1200/destinations/main.webp 2x', false)
            ->assertSee('thumbs/400/destinations/extra.webp', false)
            ->assertSee('thumbs/800/destinations/extra.webp 2x', false);
        $this->get('/packages/manakamana-darshan')->assertOk()->assertSee('thumbs/800/packages/m.webp', false);
        $this->get('/')->assertOk();
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Testimonial;
use App\Models\TourCategory;
use App\Support\Catalog;
use App\Support\Menu;
use App\Support\Present;
use App\Support\SiteData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/** Read-only content for the website: home, destinations and tour packages. */
class CatalogController extends Controller
{
    public function site(): JsonResponse
    {
        return response()->json(SiteData::payload());
    }

    public function home(): JsonResponse
    {
        $destinations = Catalog::destinations()->get();

        return response()->json([
            // Every destination for the tour search; the featured ones for the cards.
            'search' => $destinations->groupBy(fn ($d) => Present::countryName($d->country))
                ->map(fn ($list) => $list->map(fn ($d) => ['name' => $d->name, 'slug' => $d->slug])->values())->all(),
            'destination_count' => $destinations->count(),
            'countries' => $destinations->pluck('country')->unique()->map(fn ($c) => Present::countryName($c))->values()->all(),
            'destinations' => $destinations->where('is_featured', true)->take(7)->map(fn ($d) => Present::destinationCard($d))->values()->all(),
            'packages' => Catalog::packages()->where('is_featured', true)->orderBy('sort_order')->limit(6)->get()
                ->map(fn ($p) => Present::packageCard($p))->all(),
            'package_count' => Package::active()->count(),
            'testimonials' => Testimonial::active()->orderBy('sort_order')->get()->map(fn ($t) => Present::testimonial($t))->all(),
            'gallery' => GalleryImage::active()->orderBy('sort_order')->limit(6)->get()
                ->map(fn ($g, $i) => Present::galleryImage($g, $i === 0 ? 800 : 400))->all(),
        ]);
    }

    /** All destinations, or one country's (?country=nepal|india|tibet), grouped by country. */
    public function destinations(Request $request): JsonResponse
    {
        $slug = (string) $request->query('country');
        abort_if($slug !== '' && ! isset(Menu::COUNTRY_SLUGS[$slug]), 404);
        $country = $slug !== '' ? Menu::COUNTRY_SLUGS[$slug] : null;

        $destinations = Catalog::destinations()->when($country, fn ($q) => $q->where('country', $country))->get();

        return response()->json([
            'country' => $country ? ['name' => Present::countryName($country), 'slug' => $slug] : null,
            'countries' => collect(Menu::COUNTRY_SLUGS)->map(fn ($name, $s) => ['name' => Present::countryName($name), 'slug' => $s])->values()->all(),
            'groups' => collect(Catalog::grouped($destinations))->map(fn ($list, $name) => [
                'name' => Present::countryName($name),
                'slug' => Menu::countrySlug($name),
                'destinations' => $list->map(fn ($d) => Present::destinationCard($d))->all(),
            ])->values()->all(),
        ]);
    }

    public function destination(Destination $destination): JsonResponse
    {
        abort_unless($destination->is_active, 404);

        $destination->loadCount(['packages' => fn ($q) => $q->active()]);

        return response()->json([
            'destination' => Present::destinationDetail($destination),
            'packages' => $destination->packages()->active()->with(['category', 'destinations' => fn ($q) => $q->active()])
                ->orderBy('sort_order')->get()->map(fn ($p) => Present::packageCard($p))->all(),
            'nearby' => Catalog::destinations()->where('country', $destination->country)->whereKeyNot($destination->id)
                ->limit(4)->get()->map(fn ($d) => Present::destinationCard($d))->all(),
        ]);
    }

    /**
     * Packages, filtered by ?type= (tour type slug), ?destination=, ?duration=,
     * ?budget= and words (?q=), sorted by ?sort=, 12 to a page.
     */
    public function packages(Request $request): JsonResponse
    {
        $category = null;
        if ($request->filled('type')) {
            $category = TourCategory::active()->where('slug', (string) $request->query('type'))->first();
            abort_unless($category, 404);
        }

        ['destination' => $destination, 'duration' => $duration, 'budget' => $budget, 'term' => $term, 'sort' => $sort, 'packages' => $packages] = Catalog::listing($request, $category);

        return response()->json([
            'category' => $category ? [...Present::category($category), 'description' => $category->description] : null,
            'destination' => $destination ? ['name' => $destination->name, 'slug' => $destination->slug] : null,
            'duration' => $duration,
            'budget' => $budget,
            'q' => $term,
            'sort' => $sort,
            'categories' => TourCategory::active()->orderBy('sort_order')->withCount(['packages' => fn ($q) => $q->active()])->get()
                ->map(fn ($c) => [...Present::category($c), 'count' => $c->packages_count])->all(),
            'destinations' => Destination::active()->orderBy('sort_order')->get(['name', 'slug', 'country'])
                ->groupBy(fn ($d) => Present::countryName($d->country))
                ->map(fn ($list, $country) => ['country' => $country, 'options' => $list->map(fn ($d) => ['name' => $d->name, 'slug' => $d->slug])->values()])
                ->values()->all(),
            'packages' => Present::paginated($packages, fn ($p) => Present::packageCard($p)),
        ]);
    }

    public function package(Request $request, Package $package): JsonResponse
    {
        abort_unless($package->is_active, 404);

        $package->load(['category', 'destinations' => fn ($q) => $q->active()]);

        return response()->json([
            'package' => Present::packageDetail($package, $request->user()),
            'related' => Catalog::packages()->where('tour_category_id', $package->tour_category_id)->whereKeyNot($package->id)
                ->orderBy('sort_order')->limit(3)->get()->map(fn ($p) => Present::packageCard($p))->all(),
        ]);
    }
}

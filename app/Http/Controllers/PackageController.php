<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\TourCategory;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public const SORTS = [
        'recommended' => 'Recommended',
        'duration' => 'Shortest first',
        'price_low' => 'Price: low to high',
        'price_high' => 'Price: high to low',
    ];

    /** ?duration= value => [label, fewest days, most days] */
    public const DURATIONS = [
        'short' => ['Up to 5 days', 1, 5],
        'medium' => ['6 to 10 days', 6, 10],
        'long' => ['11 days or more', 11, null],
    ];

    public function index(Request $request)
    {
        // The homepage tour search sends ?type=; the tour type has its own page.
        if ($request->filled('type') && $type = TourCategory::active()->where('slug', $request->query('type'))->first()) {
            return redirect()->route('packages.category', array_filter([
                'category' => $type,
                'destination' => $request->query('destination'),
                'duration' => $request->query('duration'),
            ]));
        }

        return $this->listing($request, null);
    }

    public function category(Request $request, TourCategory $category)
    {
        abort_unless($category->is_active, 404);

        return $this->listing($request, $category);
    }

    public function show(Package $package)
    {
        abort_unless($package->is_active, 404);

        $package->load(['category', 'destinations' => fn ($q) => $q->active()]);

        return view('packages.show', [
            'package' => $package,
            'currency' => visitor_currency(),
            'related' => Package::active()->with(['category', 'destinations' => fn ($q) => $q->active()])
                ->where('tour_category_id', $package->tour_category_id)->whereKeyNot($package->id)
                ->orderBy('sort_order')->limit(3)->get(),
        ]);
    }

    /**
     * All packages or one tour type, optionally only those visiting one destination
     * (?destination=slug) and/or of one length (?duration=short|medium|long).
     */
    private function listing(Request $request, ?TourCategory $category)
    {
        $sort = array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'recommended';
        $destination = $request->filled('destination')
            ? Destination::active()->where('slug', $request->query('destination'))->first()
            : null;
        $duration = array_key_exists((string) $request->query('duration'), self::DURATIONS) ? $request->query('duration') : null;
        [, $minDays, $maxDays] = $duration ? self::DURATIONS[$duration] : [null, null, null];

        $query = Package::active()->with(['category', 'destinations' => fn ($q) => $q->active()])
            ->when($category, fn ($q) => $q->where('tour_category_id', $category->id))
            ->when($destination, fn ($q) => $q->whereHas('destinations', fn ($d) => $d->whereKey($destination->id)))
            ->when($minDays, fn ($q) => $q->where('duration_days', '>=', $minDays))
            ->when($maxDays, fn ($q) => $q->where('duration_days', '<=', $maxDays));

        match ($sort) {
            'duration' => $query->orderBy('duration_days'),
            // Packages without a price ("on request") come last either way.
            'price_low' => $query->orderByRaw('price_inr IS NULL')->orderByRaw('COALESCE(sale_price_inr, price_inr)'),
            'price_high' => $query->orderByRaw('price_inr IS NULL')->orderByRaw('COALESCE(sale_price_inr, price_inr) DESC'),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order'),
        };

        return view('packages.index', [
            'category' => $category,
            'destination' => $destination,
            'duration' => $duration,
            'destinationOptions' => Destination::active()->orderBy('sort_order')->get(['name', 'slug', 'country'])->groupBy('country'),
            'categories' => TourCategory::active()->orderBy('sort_order')->withCount(['packages' => fn ($q) => $q->active()])->get(),
            'packages' => $query->paginate(12)->withQueryString(),
            'sort' => $sort,
            'sorts' => self::SORTS,
            'durations' => self::DURATIONS,
            'currency' => visitor_currency(),
        ]);
    }
}

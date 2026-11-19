<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Support\Menu;

class DestinationController extends Controller
{
    public function index()
    {
        return view('destinations.index', [
            'country' => null,
            'groups' => $this->grouped(Destination::active()->orderBy('sort_order')->withCount(['packages' => fn ($q) => $q->active()])->get()),
        ]);
    }

    /** /destinations/country/nepal */
    public function country(string $country)
    {
        $name = Menu::COUNTRY_SLUGS[$country];

        return view('destinations.index', [
            'country' => $name,
            'groups' => $this->grouped(Destination::active()->where('country', $name)->orderBy('sort_order')
                ->withCount(['packages' => fn ($q) => $q->active()])->get()),
        ]);
    }

    public function show(Destination $destination)
    {
        abort_unless($destination->is_active, 404);

        return view('destinations.show', [
            'destination' => $destination,
            'packages' => $destination->packages()->active()->with(['category', 'destinations' => fn ($q) => $q->active()])->orderBy('sort_order')->get(),
            'nearby' => Destination::active()->where('country', $destination->country)->whereKeyNot($destination->id)
                ->withCount(['packages' => fn ($q) => $q->active()])->orderBy('sort_order')->limit(4)->get(),
        ]);
    }

    /** Country => destinations, in menu order. */
    private function grouped($destinations): array
    {
        return collect(array_keys(Destination::COUNTRIES))
            ->mapWithKeys(fn ($country) => [$country => $destinations->where('country', $country)->values()])
            ->filter(fn ($list) => $list->isNotEmpty())
            ->all();
    }
}

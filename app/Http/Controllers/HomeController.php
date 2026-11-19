<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        $destinations = Destination::active()->orderBy('sort_order')
            ->withCount(['packages' => fn ($q) => $q->active()])->get();

        return view('home', [
            'destinations' => $destinations,
            // Destination cards show the featured ones (plus a "see all" tile); the tour search lists them all.
            'featuredDestinations' => $destinations->where('is_featured', true)->take(7)->values(),
            'packages' => Package::active()->with(['category', 'destinations' => fn ($q) => $q->active()])
                ->where('is_featured', true)->orderBy('sort_order')->limit(6)->get(),
            'packageCount' => Package::active()->count(),
            'testimonials' => Testimonial::active()->orderBy('sort_order')->get(),
            'gallery' => GalleryImage::active()->orderBy('sort_order')->limit(6)->get(),
            'currency' => visitor_currency(),
        ]);
    }
}

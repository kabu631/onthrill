<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\InquiryAcknowledgement;
use App\Mail\InquiryReceived;
use App\Models\GalleryImage;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Page;
use App\Support\Catalog;
use App\Support\Present;
use App\Support\Thumbnails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Throwable;

/** Information pages, gallery, search, the contact page and enquiries. */
class ContentController extends Controller
{
    /** CMS pages: about-us, travel-information, privacy-policy, ... */
    public function page(Page $page): JsonResponse
    {
        abort_unless($page->is_active, 404);

        return response()->json([
            'page' => [
                'title' => $page->title,
                'slug' => $page->slug,
                'content' => clean_html($page->content),
                'updated' => $page->updated_at?->format('d F Y'),
            ],
            // Sidebar: the other information pages and a few popular tours.
            'pages' => Page::active()->orderBy('sort_order')->orderBy('title')->get()->map(fn ($p) => Present::pageLink($p))->all(),
            'tours' => Catalog::packages()->where('is_featured', true)->orderBy('sort_order')->limit(3)->get()
                ->map(fn ($p) => [...Present::packageCard($p), 'image' => Present::image($p->image, 160)])->all(),
        ]);
    }

    public function gallery(): JsonResponse
    {
        return response()->json(Present::paginated(
            GalleryImage::active()->orderBy('sort_order')->paginate(24),
            fn ($g) => Present::galleryImage($g),
        ));
    }

    /**
     * ?q= words. With ?limit=5: a few suggestions per kind, for the search boxes
     * (as the visitor types); otherwise the full results page.
     */
    public function search(Request $request): JsonResponse
    {
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);
        $quick = $request->query('limit') === '5';
        [$packages, $destinations, $types] = Catalog::search($term, $quick ? 5 : 24);

        if ($quick) {
            return response()->json([
                'term' => $term,
                'destinations' => $destinations->map(fn ($d) => [
                    'name' => $d->name,
                    'slug' => $d->slug,
                    'meta' => trim(implode(', ', array_filter([Str::before((string) $d->region, ','), Present::countryName($d->country)])), ', '),
                    'url' => route('destinations.show', $d, false),
                    'image' => Thumbnails::url($d->image, 96),
                    'tours' => $d->packages_count,
                ])->all(),
                'packages' => $packages->map(fn (Package $p) => [
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'meta' => $p->durationLabel(),
                    'url' => route('packages.show', $p, false),
                    'image' => Thumbnails::url($p->image, 96),
                    'prices' => Present::prices($p),
                ])->all(),
                'types' => $types->map(fn ($c) => [
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'url' => route('packages.category', $c, false),
                    'tours' => $c->packages_count,
                ])->all(),
            ]);
        }

        return response()->json([
            'term' => $term,
            'packages' => $packages->map(fn ($p) => Present::packageCard($p))->all(),
            'destinations' => $destinations->map(fn ($d) => Present::destinationCard($d))->all(),
            'types' => $types->map(fn ($c) => [...Present::category($c), 'count' => $c->packages_count])->all(),
        ]);
    }

    /** Tours offered in the contact form's "Interested in" list. */
    public function contact(): JsonResponse
    {
        return response()->json([
            'packages' => Package::active()->orderBy('name')->get(['id', 'name'])->map(fn ($p) => ['id' => $p->id, 'name' => $p->name])->all(),
        ]);
    }

    /** The contact form and the enquiry boxes on package and destination pages. */
    public function enquire(Request $request): JsonResponse
    {
        // Honeypot: bots fill every field; people never see this one.
        if ($request->filled('website')) {
            return response()->json(['message' => wording('inquiry_success')]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'email' => ['nullable', 'email', 'max:160'],
            'subject' => ['nullable', 'string', 'max:160'],
            'package_id' => ['nullable', 'integer', 'exists:packages,id'],
            'destination_id' => ['nullable', 'integer', 'exists:destinations,id'],
            'message' => ['required', 'string', 'max:3000'],
        ], [
            'phone.regex' => 'Please enter a valid phone number.',
            'name.required' => 'Please tell us your name.',
            'phone.required' => 'Please enter your phone or WhatsApp number so we can reach you.',
            'message.required' => 'Please write a short message.',
        ]);

        $inquiry = Inquiry::create($data);
        $inquiry->load('package', 'destination');

        // Mail problems are logged, never shown to the visitor: the enquiry is already saved.
        try {
            if ($admin = setting('booking_notify_email')) {
                Mail::to($admin)->send(new InquiryReceived($inquiry));
            }
        } catch (Throwable $e) {
            Log::error('Inquiry notification failed', ['inquiry' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        try {
            if ($inquiry->email && setting('inquiry_autoreply')) {
                Mail::to($inquiry->email)->send(new InquiryAcknowledgement($inquiry));
            }
        } catch (Throwable $e) {
            Log::error('Inquiry acknowledgement failed', ['inquiry' => $inquiry->id, 'error' => $e->getMessage()]);
        }

        return response()->json(['message' => wording('inquiry_success')], 201);
    }
}

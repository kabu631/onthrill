<?php

namespace App\Http\Controllers;

use App\Mail\InquiryAcknowledgement;
use App\Mail\InquiryReceived;
use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Page;
use App\Support\Thumbnails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PageController extends Controller
{
    public function contact()
    {
        return view('pages.contact', [
            'packages' => Package::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function gallery()
    {
        return view('pages.gallery', [
            'images' => GalleryImage::active()->orderBy('sort_order')->paginate(24),
        ]);
    }

    /** INR / NPR switch in the header: remembered for a year, then back to the page. */
    public function currency(Request $request, string $code)
    {
        Cookie::queue('currency', strtoupper($code), 60 * 24 * 365);

        return redirect()->back(fallback: route('home'));
    }

    public function search(Request $request)
    {
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);
        $like = '%'.addcslashes($term, '%_\\').'%';
        // The search pop-up asks for JSON and shows only the first few matches.
        $limit = $request->expectsJson() ? 5 : 24;

        // Matches in the name come before matches only in the summary.
        $find = fn (string $model) => mb_strlen($term) < 2 ? collect() : $model::active()
            ->where(fn ($q) => $q->where('name', 'like', $like)->orWhere('summary', 'like', $like))
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', [$like])->orderBy('sort_order')->limit($limit)->get();

        $packages = $find(Package::class)->load(['category', 'destinations' => fn ($q) => $q->active()]);
        $destinations = $find(Destination::class);
        $currency = visitor_currency();

        if ($request->expectsJson()) {
            $results = $packages->map(fn (Package $p) => [
                'name' => $p->name,
                'meta' => 'Package · '.$p->durationLabel(),
                'url' => $p->url(),
                'image' => Thumbnails::url($p->image, 96),
                'price' => $p->isOnRequest() ? 'On request' : money($p->price($currency), $currency),
            ])->concat($destinations->map(fn (Destination $d) => [
                'name' => $d->name,
                'meta' => 'Destination · '.$d->country,
                'url' => $d->url(),
                'image' => Thumbnails::url($d->image, 96),
                'price' => '',
            ]));

            return response()->json([
                'term' => $term,
                'url' => route('search', ['q' => $term]),
                'results' => $results->values(),
            ]);
        }

        return view('pages.search', compact('term', 'packages', 'destinations'));
    }

    /**
     * Contact form and per-package / per-destination enquiry forms. Both success
     * and validation errors return the visitor to the form itself (#enquiry-form),
     * so the result is always in view.
     */
    public function inquire(Request $request)
    {
        // Honeypot: bots fill every field; humans never see this one.
        if ($request->filled('website')) {
            return back()->with('inquiry_sent', wording('inquiry_success'))->withFragment('enquiry-form');
        }

        $validator = Validator::make($request->all(), [
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

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->withFragment('enquiry-form');
        }

        $inquiry = Inquiry::create($validator->validated());
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

        return back()->with('inquiry_sent', wording('inquiry_success'))->withFragment('enquiry-form');
    }

    /** CMS pages: /about-us, /travel-information, /privacy-policy, ... */
    public function show(Page $page)
    {
        abort_unless($page->is_active, 404);

        return view('pages.show', [
            'page' => $page,
            // Sidebar: the other information pages and a few popular tours.
            'pages' => Page::query()->where('is_active', true)->orderBy('sort_order')->orderBy('title')->get(['id', 'title', 'slug', 'menu_label']),
            'tours' => Package::active()->with(['destinations' => fn ($q) => $q->active()])->where('is_featured', true)
                ->orderBy('sort_order')->limit(3)->get(),
            'currency' => visitor_currency(),
        ]);
    }
}

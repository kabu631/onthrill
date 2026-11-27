<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\AccountController as AccountApi;
use App\Http\Controllers\Api\BookingController as BookingApi;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContentController;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Package;
use App\Models\Page;
use App\Models\TourCategory;
use App\Support\Catalog;
use App\Support\Menu;
use App\Support\Present;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * The pages of the website. The Angular app draws every page in the browser;
 * this controller sends what must be there before it runs: the page title,
 * description, canonical link, sharing tags and structured data for search
 * engines, a plain outline of the content for crawlers that do not run
 * JavaScript, and the page's data so the app can draw it without waiting.
 */
class ShellController extends Controller
{
    public function home()
    {
        $data = $this->data(app(CatalogController::class)->home());

        return $this->shell([
            'api' => '/api/home',
            'data' => $data,
            'schemas' => [Seo::website()],
            'heading' => trim(setting('hero_eyebrow').' '.(collect((array) setting('hero_slides'))->pluck('title')->filter()->first() ?? '')),
            'intro' => setting('hero_text'),
            'items' => collect($data['packages'])->map(fn ($p) => [$p['name'], $p['url'], $p['duration']])
                ->concat(collect($data['destinations'])->map(fn ($d) => [$d['name'], $d['url'], $d['country']]))->all(),
        ]);
    }

    public function destinations(Request $request, ?string $country = null)
    {
        $request->query->set('country', $country);
        $data = $this->data(app(CatalogController::class)->destinations($request));

        $allTitle = wording('destinations_page_title');
        $countryName = $data['country']['name'] ?? null;
        $title = $countryName ? wording('country_page_title', ['country' => $countryName]) : $allTitle;
        $intro = $country ? wording('country_intro_'.$country) : wording('destinations_page_intro');
        $all = collect($data['groups'])->flatMap(fn ($g) => $g['destinations']);

        return $this->shell([
            'api' => '/api/destinations'.($country ? '?country='.$country : ''),
            'data' => $data,
            'title' => $title,
            'description' => $intro,
            'schemas' => [
                Seo::breadcrumbs($country ? [$allTitle => route('destinations.index'), $countryName => null] : [$allTitle => null]),
                $this->itemList($title, $all),
            ],
            'heading' => $title,
            'intro' => $intro,
            'items' => $all->map(fn ($d) => [$d['name'], $d['url'], $d['region'].', '.$d['country']])->all(),
        ]);
    }

    public function destination(Destination $destination)
    {
        abort_unless($destination->is_active, 404);
        $data = $this->data(app(CatalogController::class)->destination($destination));
        $d = $destination;
        $countryName = Present::countryName($d->country);

        return $this->shell([
            'api' => '/api/destinations/'.$d->slug,
            'data' => $data,
            'title' => $d->meta_title ?: $d->name.', '.$countryName,
            'description' => $d->meta_description ?: $d->summary,
            'image' => media_url($d->image),
            'type' => 'article',
            'schemas' => [
                Seo::breadcrumbs([
                    wording('destinations_page_title') => route('destinations.index'),
                    $countryName => route('destinations.country', Menu::countrySlug($d->country)),
                    $d->name => null,
                ]),
                Seo::destination($d),
                Seo::faq(Catalog::faqs($d->faqs)),
            ],
            'heading' => $d->name,
            'intro' => $d->summary,
            'html' => clean_html($d->description),
            'items' => collect($data['packages'])->map(fn ($p) => [$p['name'], $p['url'], $p['duration']])->all(),
        ]);
    }

    public function packages(Request $request, ?TourCategory $category = null)
    {
        // The homepage tour search sends ?type=; each tour type has its own page.
        if (! $category && $request->filled('type') && $type = TourCategory::active()->where('slug', (string) $request->query('type'))->first()) {
            return redirect()->route('packages.category', array_filter([
                'category' => $type,
                'destination' => $request->query('destination'),
                'duration' => $request->query('duration'),
                'budget' => $request->query('budget'),
                'q' => $request->query('q'),
            ]));
        }
        abort_if($category && ! $category->is_active, 404);

        $query = array_filter([
            'destination' => $request->query('destination'),
            'duration' => $request->query('duration'),
            'budget' => $request->query('budget'),
            'page' => $request->integer('page') > 1 ? (string) $request->integer('page') : null,
            'q' => $request->query('q'),
            'sort' => $request->query('sort'),
            'type' => $category?->slug,
        ], fn ($v) => is_string($v) && $v !== '');
        ksort($query);
        $request->query->replace($query);
        $data = $this->data(app(CatalogController::class)->packages($request));

        $allTitle = wording('packages_title');
        $destination = $data['destination'];
        $title = $category?->name ?? ($destination ? wording('list_destination_title', ['name' => $destination['name']]) : $allTitle);
        $intro = $category?->description ?: ($destination ? wording('list_destination_intro', ['name' => $destination['name']]) : wording('packages_intro'));
        $crumbs = [$allTitle => ($category || $destination) ? route('packages.index') : null];
        if ($category) {
            $crumbs[$category->name] = null;
        } elseif ($destination) {
            $crumbs[$destination['name']] = null;
        }

        return $this->shell([
            'api' => '/api/packages'.($query ? '?'.http_build_query($query, '', '&', PHP_QUERY_RFC3986) : ''),
            'data' => $data,
            'title' => $category?->meta_title ?: $title,
            'description' => $category?->meta_description ?: $intro,
            'noindex' => $destination || $data['duration'] || $data['budget'] || $data['q'] !== '',
            'schemas' => [Seo::breadcrumbs($crumbs), $this->itemList($title, collect($data['packages']['data']))],
            'heading' => $title,
            'intro' => $intro,
            'items' => collect($data['packages']['data'])->map(fn ($p) => [$p['name'], $p['url'], $p['duration'].' · '.$this->priceLine($p)])->all(),
        ]);
    }

    public function package(Request $request, Package $package)
    {
        abort_unless($package->is_active, 404);
        $data = $this->data(app(CatalogController::class)->package($request, $package));
        $p = $package;

        $crumbs = [wording('packages_title') => route('packages.index')];
        if ($p->category) {
            $crumbs[$p->category->name] = route('packages.category', $p->category);
        }
        $crumbs[$p->name] = null;

        return $this->shell([
            'api' => '/api/packages/'.$p->slug,
            'data' => $data,
            'title' => $p->meta_title ?: $p->name,
            'description' => $p->meta_description ?: $p->summary,
            'image' => media_url($p->image),
            'schemas' => [Seo::breadcrumbs($crumbs), Seo::package($p), Seo::faq(Catalog::faqs($p->faqs))],
            'heading' => $p->name,
            'intro' => $p->durationLabel().' · '.$this->priceLine($data['package']).'. '.$p->summary,
            'html' => clean_html($p->overview),
            'items' => collect($data['package']['itinerary'])->map(fn ($day, $i) => [wording('pkg_day').' '.($i + 1).': '.$day['title'], null, $day['description']])->all(),
        ]);
    }

    public function booking(Request $request, Booking $booking)
    {
        $data = $this->data(app(BookingApi::class)->show($request, $booking));

        return $this->shell([
            'api' => '/api/bookings/'.$booking->reference,
            'data' => $data,
            'title' => wording('bookingpage_title', ['reference' => $booking->reference]),
            'noindex' => true,
            'heading' => wording('bookingpage_title', ['reference' => $booking->reference]),
        ]);
    }

    public function contact()
    {
        return $this->shell([
            'api' => '/api/contact',
            'data' => $this->data(app(ContentController::class)->contact()),
            'title' => wording('contact_title'),
            'description' => wording('contact_intro'),
            'schemas' => [Seo::breadcrumbs([wording('contact_title') => null])],
            'heading' => wording('contact_title'),
            'intro' => wording('contact_intro'),
        ]);
    }

    public function gallery(Request $request)
    {
        $page = $request->integer('page');

        return $this->shell([
            'api' => '/api/gallery'.($page > 1 ? '?page='.$page : ''),
            'data' => $this->data(app(ContentController::class)->gallery()),
            'title' => wording('gallery_title'),
            'description' => wording('gallery_intro'),
            'schemas' => [Seo::breadcrumbs([wording('gallery_title') => null])],
            'heading' => wording('gallery_title'),
            'intro' => wording('gallery_intro'),
        ]);
    }

    public function search(Request $request)
    {
        $term = mb_substr(trim((string) $request->query('q')), 0, 100);
        $data = $this->data(app(ContentController::class)->search($request));

        return $this->shell([
            'api' => '/api/search?'.http_build_query(['q' => $term], '', '&', PHP_QUERY_RFC3986),
            'data' => $data,
            'title' => $term ? wording('search_title').': '.$term : wording('search_title'),
            'noindex' => true,
            'heading' => wording('search_title'),
            'items' => collect($data['packages'])->map(fn ($p) => [$p['name'], $p['url'], $p['duration']])
                ->concat(collect($data['destinations'])->map(fn ($d) => [$d['name'], $d['url'], $d['country']]))->all(),
        ]);
    }

    public function page(Page $page)
    {
        abort_unless($page->is_active, 404);

        return $this->shell([
            'api' => '/api/pages/'.$page->slug,
            'data' => $this->data(app(ContentController::class)->page($page)),
            'title' => $page->meta_title ?: $page->title,
            'description' => $page->meta_description ?: Str::limit(strip_tags((string) $page->content), 160),
            'schemas' => [Seo::breadcrumbs([$page->title => null])],
            'heading' => $page->title,
            'html' => clean_html($page->content),
        ]);
    }

    public function account(Request $request)
    {
        return $this->shell([
            'api' => '/api/account',
            'data' => $this->data(app(AccountApi::class)->show($request)),
            'title' => wording('account_title'),
            'noindex' => true,
            'heading' => wording('account_title'),
        ]);
    }

    public function accountBooking(Request $request, Booking $booking)
    {
        return $this->shell([
            'api' => '/api/account/bookings/'.$booking->reference,
            'data' => $this->data(app(AccountApi::class)->booking($request, $booking)),
            'title' => wording('bookingpage_title', ['reference' => $booking->reference]),
            'noindex' => true,
            'heading' => wording('bookingpage_title', ['reference' => $booking->reference]),
        ]);
    }

    /** Sign in, register, forgotten and new password: drawn entirely by the app. */
    public function form(string $titleKey)
    {
        return $this->shell(['title' => wording($titleKey), 'noindex' => true, 'heading' => wording($titleKey)]);
    }

    /** Pages that do not exist: the app shows its "not found" page. */
    public static function notFound()
    {
        return response()->view('app', static::meta([
            'title' => wording('e404_title'),
            'noindex' => true,
            'heading' => wording('e404_title'),
            'intro' => wording('e404_text'),
        ]), 404);
    }

    private function shell(array $meta)
    {
        return response()->view('app', static::meta($meta));
    }

    private static function meta(array $meta): array
    {
        return [
            'title' => null, 'description' => null, 'image' => null, 'noindex' => false, 'type' => 'website',
            'schemas' => [], 'heading' => null, 'intro' => null, 'html' => null, 'items' => [], 'api' => null, 'data' => null,
            ...$meta,
        ];
    }

    private function data(JsonResponse $response): array
    {
        return $response->getData(true);
    }

    private function priceLine(array $package): string
    {
        $currency = visitor_currency();

        return $package['prices']
            ? wording('price_from').' '.money($package['prices'][$currency]['now'], $currency).' '.wording('price_per_person')
            : wording('price_on_request');
    }

    private function itemList(string $name, $items): array
    {
        $list = collect($items)->values();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $name,
            'numberOfItems' => $list->count(),
            'itemListElement' => $list->map(fn ($item, $i) => ['@type' => 'ListItem', 'position' => $i + 1, 'url' => url($item['url']), 'name' => $item['name']])->all(),
        ];
    }
}

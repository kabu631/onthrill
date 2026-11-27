<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Package;
use App\Models\Page;
use App\Models\TourCategory;
use App\Support\Menu;
use Illuminate\Support\Str;

/** sitemap.xml, robots.txt and llms.txt, all built from the current content. */
class SeoController extends Controller
{
    public function sitemap()
    {
        $entry = fn (string $url, $updated = null, array $images = [], string $priority = '0.6') => compact('url', 'updated', 'images', 'priority');
        $images = fn ($model, $paths) => collect($paths)->filter()->map(fn ($p) => ['loc' => media_url($p), 'title' => $model->name])->values()->all();

        $urls = collect([
            $entry(route('home'), null, [], '1.0'),
            $entry(route('destinations.index'), Destination::max('updated_at'), [], '0.9'),
            $entry(route('packages.index'), Package::max('updated_at'), [], '0.9'),
            $entry(route('gallery')),
            $entry(route('contact')),
        ])
            ->merge(collect(Menu::COUNTRY_SLUGS)->keys()->map(fn ($slug) => $entry(route('destinations.country', $slug), null, [], '0.7')))
            ->merge(TourCategory::active()->get()->map(fn ($c) => $entry($c->url(), $c->updated_at, [], '0.8')))
            ->merge(Destination::active()->get()->map(fn ($d) => $entry($d->url(), $d->updated_at, $images($d, [$d->image, ...($d->gallery ?? [])]), '0.8')))
            ->merge(Package::active()->get()->map(fn ($p) => $entry($p->url(), $p->updated_at, $images($p, [$p->image, ...($p->gallery ?? [])]), '0.9')))
            ->merge(Page::active()->get()->map(fn ($p) => $entry(route('pages.show', $p), $p->updated_at, [], '0.5')));

        return response()->view('seo.sitemap', compact('urls'))->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /my-account',
            'Disallow: /booking/',
            'Disallow: /search',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            '',
            'Sitemap: '.route('sitemap'),
        ];

        return response(implode("\n", $lines)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * llms.txt: a plain summary of the site for AI assistants and answer engines
     * (see llmstxt.org), with links to every destination and package.
     */
    public function llms()
    {
        $site = setting('site_name');
        $out = ["# {$site}", '', '> '.setting('meta_description'), ''];
        $out[] = "{$site} plans pilgrimage and cultural tours for travellers from India and Nepal. Prices are per person in Indian rupees (INR) and Nepali rupees (NPR). Bookings are requested on the website and confirmed by phone before any payment.";
        $out[] = '';

        foreach (array_keys(Destination::COUNTRIES) as $country) {
            $destinations = Destination::active()->where('country', $country)->orderBy('sort_order')->get();
            if ($destinations->isEmpty()) {
                continue;
            }
            $out[] = "## Destinations in {$country}";
            $out[] = '';
            foreach ($destinations as $d) {
                $facts = collect([$d->altitudeLabel() ? 'altitude '.$d->altitudeLabel() : null, $d->best_time ? 'best time: '.$d->best_time : null])->filter()->implode('; ');
                $out[] = "- [{$d->name}]({$d->url()}): {$d->summary}".($facts ? " ({$facts})" : '');
            }
            $out[] = '';
        }

        $out[] = '## Tour packages';
        $out[] = '';
        foreach (Package::active()->orderBy('sort_order')->get() as $p) {
            $price = $p->isOnRequest() ? 'price on request' : 'from '.inr($p->price('INR')).' / '.npr($p->price('NPR')).' per person';
            $out[] = "- [{$p->name}]({$p->url()}): {$p->durationLabel()}, {$price}. ".Str::limit((string) $p->summary, 200);
        }
        $out[] = '';

        $out[] = '## Information';
        $out[] = '';
        foreach (Page::active()->orderBy('sort_order')->get() as $page) {
            $out[] = "- [{$page->title}](".route('pages.show', $page).')';
        }
        $out[] = '- [Contact]('.route('contact').')';

        return response(implode("\n", $out)."\n")->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}

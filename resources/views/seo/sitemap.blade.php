{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
@foreach ($urls as $entry)
    <url>
        <loc>{{ $entry['url'] }}</loc>
@if ($entry['updated'])
        <lastmod>{{ \Illuminate\Support\Carbon::parse($entry['updated'])->toAtomString() }}</lastmod>
@endif
        <priority>{{ $entry['priority'] }}</priority>
@foreach ($entry['images'] as $image)
        <image:image>
            <image:loc>{{ $image['loc'] }}</image:loc>
            <image:title>{{ $image['title'] }}</image:title>
        </image:image>
@endforeach
    </url>
@endforeach
</urlset>

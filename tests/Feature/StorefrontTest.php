<?php

namespace Tests\Feature;

use App\Mail\BookingReceived;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Page;
use App\Models\TourCategory;
use App\Support\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/** The public website: pages, prices in both currencies, booking requests, and what search engines read. */
class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    private Destination $pashupatinath;

    private Destination $kedarnath;

    private Package $package;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pashupatinath = Destination::create([
            'name' => 'Pashupatinath Temple', 'country' => 'Nepal', 'region' => 'Kathmandu, Bagmati Province',
            'summary' => 'The most important Shiva temple in Nepal, on the Bagmati river.', 'altitude_m' => 1350,
            'latitude' => 27.7104, 'longitude' => 85.3488, 'best_time' => 'October to March',
            'faqs' => [['question' => 'Can non-Hindus enter the main temple?', 'answer' => 'No, but they can see it from across the river.']],
            'is_active' => true, 'is_featured' => true,
        ]);
        $this->kedarnath = Destination::create(['name' => 'Kedarnath', 'country' => 'India', 'region' => 'Uttarakhand', 'is_active' => true]);

        $type = TourCategory::create(['name' => 'Nepal Pilgrimage Tours']);
        $this->package = Package::create([
            'name' => 'Pashupatinath Darshan Tour', 'tour_category_id' => $type->id, 'duration_days' => 3, 'duration_nights' => 2,
            'summary' => 'Three days in Kathmandu with darshan at Pashupatinath.', 'price_inr' => 12000,
            'itinerary' => [['title' => 'Arrive in Kathmandu', 'description' => 'Evening aarti at Pashupatinath.'], ['title' => 'Temple day', 'description' => null], ['title' => 'Depart', 'description' => null]],
            'faqs' => [['question' => 'Is the airport pickup included?', 'answer' => 'Yes.']],
            'is_active' => true, 'is_featured' => true,
        ]);
        $this->package->destinations()->attach($this->pashupatinath);
    }

    private function bookingForm(array $overrides = []): array
    {
        return [
            'travel_date' => now()->addMonth()->toDateString(), 'adults' => 2, 'children' => 1, 'nationality' => 'indian',
            'name' => 'Gita Sharma', 'phone' => '+91 98765 43210', 'email' => 'gita@example.com', 'travelling_from' => 'Varanasi', ...$overrides,
        ];
    }

    public function test_every_public_page_loads(): void
    {
        Page::create(['title' => 'About Us', 'content' => '<p>We plan pilgrimages.</p>', 'is_active' => true]);

        foreach ([
            '/', '/destinations', '/destinations/country/nepal', '/destinations/country/india', '/destinations/pashupatinath-temple',
            '/packages', '/packages/type/nepal-pilgrimage-tours', '/packages/pashupatinath-darshan-tour',
            '/contact-us', '/gallery', '/search?q=pashu', '/about-us', '/login', '/register', '/forgot-password',
            '/sitemap.xml', '/robots.txt', '/llms.txt',
        ] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/destinations/country/tibet')->assertOk(); // no destinations yet, still a valid page
        $this->get('/destinations/nowhere')->assertNotFound();
        $this->get('/packages/nothing-here')->assertNotFound();
    }

    public function test_hidden_tours_are_not_shown_anywhere(): void
    {
        $this->package->update(['is_active' => false]);
        $this->kedarnath->update(['is_active' => false]);

        $this->get('/packages/pashupatinath-darshan-tour')->assertNotFound();
        $this->get('/destinations/kedarnath')->assertNotFound();
        $this->get('/packages')->assertDontSee('Pashupatinath Darshan Tour');
        $this->get('/sitemap.xml')->assertDontSee('/packages/pashupatinath-darshan-tour')->assertDontSee('/destinations/kedarnath');
        $this->post('/packages/pashupatinath-darshan-tour/book', $this->bookingForm())->assertNotFound();
    }

    public function test_menu_groups_destinations_by_country(): void
    {
        $this->get('/')->assertSee('/destinations/pashupatinath-temple')->assertSee('/destinations/kedarnath')
            ->assertSee('/destinations/country/nepal')->assertSee('/packages/type/nepal-pilgrimage-tours');
        $this->get('/destinations/country/india')->assertSee('Kedarnath')->assertDontSee('Pashupatinath Temple</h3>', false);
    }

    public function test_prices_switch_between_indian_and_nepali_rupees(): void
    {
        $this->get('/packages/pashupatinath-darshan-tour')->assertSee('₹12,000');

        // The header switch stores the choice in a cookie and sends the visitor back.
        $this->from('/packages')->get('/currency/npr')->assertRedirect('/packages')->assertCookie('currency', 'NPR');

        $this->withCookie('currency', 'NPR')->get('/packages/pashupatinath-darshan-tour')->assertSee('NPR 19,200'); // 12,000 × 1.6

        // A package's own NPR price wins over the exchange rate.
        $this->package->update(['price_npr' => 18500]);
        $this->withCookie('currency', 'NPR')->get('/packages/pashupatinath-darshan-tour')->assertSee('NPR 18,500');

        // So does a new rate set in the admin panel.
        $this->package->update(['price_npr' => null]);
        Settings::set(['npr_per_inr' => 1.602]);
        $this->assertSame(19220.0, $this->package->fresh()->price('NPR'));
    }

    public function test_a_traveller_can_send_a_booking_request(): void
    {
        Mail::fake();
        Settings::set(['upi_id' => 'onthrill@sbi', 'booking_notify_email' => 'bookings@onthrill.com', 'advance_percent' => 25]);

        $response = $this->post('/packages/pashupatinath-darshan-tour/book', $this->bookingForm());

        $booking = Booking::sole();
        $response->assertRedirect(route('bookings.show', $booking));
        $this->assertMatchesRegularExpression('/^OT-\d{6}-[0-9A-F]{5}$/', $booking->reference);
        $this->assertSame('INR', $booking->currency);
        $this->assertEquals(36000, $booking->total);   // 3 travellers × ₹12,000
        $this->assertEquals(9000, $booking->advance);  // 25%
        $this->assertSame('new', $booking->status);

        Mail::assertSent(BookingReceived::class, fn ($mail) => $mail->hasTo('gita@example.com'));
        Mail::assertSent(BookingReceived::class, fn ($mail) => $mail->hasTo('bookings@onthrill.com'));

        $this->get(route('bookings.show', $booking))->assertOk()
            ->assertSee($booking->reference)->assertSee('₹36,000')->assertSee('onthrill@sbi')
            ->assertSee('<meta name="robots" content="noindex', false);

        // Nobody else can open it.
        $this->flushSession();
        $this->get(route('bookings.show', $booking))->assertNotFound();
    }

    public function test_nepali_travellers_are_quoted_and_paid_in_nepali_rupees(): void
    {
        Mail::fake();
        Settings::set(['esewa_id' => '9801234567', 'upi_id' => 'onthrill@sbi']);

        $this->post('/packages/pashupatinath-darshan-tour/book', $this->bookingForm(['nationality' => 'nepali', 'children' => 0]));

        $booking = Booking::sole();
        $this->assertSame('NPR', $booking->currency);
        $this->assertEquals(38400, $booking->total); // 2 × NPR 19,200

        $this->get(route('bookings.show', $booking))->assertSee('NPR 38,400')->assertSee('9801234567')->assertDontSee('onthrill@sbi');

        // The confirmation email carries the same payment details.
        $email = (new BookingReceived($booking))->render();
        $this->assertStringContainsString('9801234567', $email);
        $this->assertStringContainsString('NPR 9,600', $email); // 25% advance
        $this->assertStringNotContainsString('onthrill@sbi', $email);
        $indian = $booking->replicate()->forceFill(['currency' => 'INR', 'reference' => 'OT-1']);
        $this->assertMatchesRegularExpression('#UPI ID: <strong[^>]*>onthrill@sbi</strong>#', (new BookingReceived($indian))->render());
    }

    public function test_price_on_request_packages_can_still_be_booked(): void
    {
        Mail::fake();
        $kailash = Package::create(['name' => 'Kailash Mansarovar Yatra', 'duration_days' => 12, 'is_active' => true]);

        $this->get('/packages/kailash-mansarovar-yatra')->assertOk()->assertSee('Price on request');
        $this->post('/packages/kailash-mansarovar-yatra/book', $this->bookingForm())->assertRedirect();

        $booking = Booking::where('package_id', $kailash->id)->sole();
        $this->assertNull($booking->total);
        $this->assertNull($booking->advance);
    }

    public function test_booking_requests_are_checked(): void
    {
        $this->post('/packages/pashupatinath-darshan-tour/book', $this->bookingForm([
            'travel_date' => now()->subDay()->toDateString(), 'adults' => 0, 'phone' => 'call me', 'nationality' => 'martian',
        ]))->assertSessionHasErrors(['travel_date', 'adults', 'phone', 'nationality'], errorBag: 'booking');

        $this->assertSame(0, Booking::count());
    }

    public function test_enquiries_remember_which_package_they_are_about(): void
    {
        Mail::fake();

        $this->post('/enquiry', ['name' => 'Ram Thapa', 'phone' => '9801234567', 'message' => 'Is the tour open in July?', 'package_id' => $this->package->id])
            ->assertRedirect();

        $this->assertSame('Pashupatinath Darshan Tour', Inquiry::sole()->topic());
    }

    public function test_search_finds_packages_and_destinations(): void
    {
        $this->getJson('/search?q=pashupati')->assertOk()
            ->assertJsonFragment(['name' => 'Pashupatinath Darshan Tour'])
            ->assertJsonFragment(['name' => 'Pashupatinath Temple']);

        $this->get('/search?q=kedar')->assertOk()->assertSee('Kedarnath');
    }

    public function test_pages_carry_the_tags_search_engines_need(): void
    {
        $html = $this->get('/packages/pashupatinath-darshan-tour')->assertOk()->getContent();

        $this->assertStringContainsString('<link rel="canonical" href="'.url('/packages/pashupatinath-darshan-tour').'">', $html);
        $this->assertMatchesRegularExpression('#<title>[^<]*Pashupatinath Darshan Tour#', $html);
        $this->assertStringContainsString('<meta name="description" content="Three days in Kathmandu with darshan at Pashupatinath.', $html);
        $this->assertStringContainsString('property="og:image"', $html);
        $this->assertStringContainsString('"@type":"TouristTrip"', $html);
        $this->assertStringContainsString('"priceCurrency":"INR"', $html);
        $this->assertStringContainsString('"priceCurrency":"NPR"', $html);
        $this->assertStringContainsString('"@type":"BreadcrumbList"', $html);
        $this->assertStringContainsString('"@type":"FAQPage"', $html);
        $this->assertStringContainsString('Day 1: Arrive in Kathmandu', $html);
        $this->assertSame(1, substr_count($html, '<h1'), 'one main heading per page');

        $html = $this->get('/destinations/pashupatinath-temple')->getContent();
        $this->assertStringContainsString('"@type":["TouristDestination","PlaceOfWorship"]', $html);
        $this->assertStringContainsString('"latitude":27.7104', $html);
        $this->assertStringContainsString('"addressCountry":"NP"', $html);
        $this->assertStringContainsString('Can non-Hindus enter the main temple?', $html);
        $this->assertSame(1, substr_count($html, '<h1'));

        $html = $this->get('/')->getContent();
        $this->assertStringContainsString('"@type":"TravelAgency"', $html);
        $this->assertStringContainsString('"@type":"WebSite"', $html);
        $this->assertStringContainsString('"urlTemplate":"'.url('/search').'?q={search_term_string}"', $html);
        $this->assertSame(1, substr_count($html, '<h1'));
    }

    public function test_sitemap_robots_and_llms_txt(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee(url('/destinations/pashupatinath-temple'), false)
            ->assertSee(url('/packages/pashupatinath-darshan-tour'), false)
            ->assertSee(url('/destinations/country/nepal'), false)
            ->assertSee('<lastmod>', false);

        $this->get('/robots.txt')->assertOk()
            ->assertSee('Disallow: /admin')->assertSee('Disallow: /booking/')->assertSee('Sitemap: '.url('/sitemap.xml'));

        $this->get('/llms.txt')->assertOk()
            ->assertSee('## Destinations in Nepal')->assertSee('Pashupatinath Temple')
            ->assertSee('## Tour packages')->assertSee('Pashupatinath Darshan Tour');
    }

    public function test_private_pages_are_kept_out_of_search_results(): void
    {
        foreach (['/login', '/register', '/search?q=temple'] as $url) {
            $this->get($url)->assertSee('<meta name="robots" content="noindex', false);
        }

        $this->get('/packages/pashupatinath-darshan-tour')->assertDontSee('content="noindex', false);
    }
}

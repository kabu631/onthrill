<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent;
use App\Filament\Pages\SiteSettings;
use App\Filament\Resources\Bookings\Pages\EditBooking;
use App\Filament\Resources\Bookings\Pages\ListBookings;
use App\Filament\Resources\Destinations\Pages\CreateDestination;
use App\Filament\Resources\Destinations\Pages\ListDestinations;
use App\Filament\Resources\Inquiries\Pages\ListInquiries;
use App\Filament\Resources\Packages\Pages\CreatePackage;
use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Resources\Pages\Pages\ListPages;
use App\Filament\Resources\TourCategories\Pages\ManageTourCategories;
use App\Filament\Widgets\LatestBookings;
use App\Models\Booking;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Testimonial;
use App\Models\TourCategory;
use App\Models\User;
use App\Support\Access;
use App\Support\Settings;
use Filament\Livewire\GlobalSearch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create()->assignRole(Access::SUPER_ADMIN);
    }

    private function booking(array $attributes = []): Booking
    {
        return Booking::create([
            'reference' => Booking::nextReference(), 'package_name' => 'Muktinath Yatra', 'name' => 'Gita Sharma',
            'email' => 'g@example.com', 'phone' => '9876543210', 'nationality' => 'indian', 'adults' => 2,
            'currency' => 'INR', 'price_per_person' => 20000, 'total' => 40000, 'advance' => 10000, ...$attributes,
        ]);
    }

    public function test_guests_and_customers_cannot_open_the_admin_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }

    public function test_admin_can_create_a_destination_and_it_appears_on_the_site(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreateDestination::class)
            ->fillForm([
                'name' => 'Janaki Mandir',
                'country' => 'Nepal',
                'region' => 'Janakpur, Madhesh Province',
                'summary' => 'The temple of Sita in Janakpur.',
                'altitude_m' => 74,
                'faqs' => [['question' => 'When is Vivah Panchami?', 'answer' => 'In November or December.']],
                'is_active' => true,
                'is_featured' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $destination = Destination::sole();
        $this->assertSame('janaki-mandir', $destination->slug);
        $this->get('/destinations/janaki-mandir')->assertOk()->assertSee('The temple of Sita in Janakpur.')->assertSee('74 m');
        $this->get('/destinations/country/nepal')->assertSee('Janaki Mandir');
        // The menu is rebuilt from the destinations.
        $this->get('/')->assertSee('/destinations/janaki-mandir');
    }

    public function test_admin_can_create_a_package_with_prices_in_both_currencies(): void
    {
        $destination = Destination::create(['name' => 'Muktinath', 'country' => 'Nepal', 'is_active' => true]);
        $type = TourCategory::create(['name' => 'Nepal Pilgrimage Tours']);
        $this->actingAs($this->admin());

        Livewire::test(CreatePackage::class)
            ->fillForm([
                'name' => 'Muktinath Yatra by Air',
                'tour_category_id' => $type->id,
                'destinations' => [$destination->id],
                'duration_days' => 5,
                'duration_nights' => 4,
                'price_inr' => 30000,
                'sale_price_inr' => 27500,
                'itinerary' => [['title' => 'Arrive in Kathmandu', 'description' => 'Transfer to the hotel.'], ['title' => 'Fly to Pokhara', 'description' => null]],
                'is_active' => true,
                'is_featured' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $package = Package::sole();
        $this->assertSame(['Muktinath'], $package->destinations->pluck('name')->all());
        $this->assertSame(27500.0, $package->price('INR'));
        $this->assertSame(44000.0, $package->price('NPR')); // 27,500 × 1.6

        $this->get('/packages/muktinath-yatra-by-air')->assertOk()
            ->assertSee('₹27,500')->assertSee('Arrive in Kathmandu');
        $this->get('/destinations/muktinath')->assertSee('Muktinath Yatra by Air');
        $this->get('/packages/type/nepal-pilgrimage-tours')->assertSee('Muktinath Yatra by Air');
        $this->get('/')->assertSee('Muktinath Yatra by Air');
    }

    public function test_offer_price_must_be_below_regular_price(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(CreatePackage::class)
            ->fillForm(['name' => 'Manakamana Day Trip', 'duration_days' => 1, 'price_inr' => 5000, 'sale_price_inr' => 6000])
            ->call('create')
            ->assertHasFormErrors(['sale_price_inr']);
    }

    public function test_admin_can_add_payment_details_for_india_and_nepal(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(SiteSettings::class)
            ->fillForm(['upi_id' => 'onthrill@sbi', 'upi_number' => '98765 43210', 'esewa_id' => '9801234567', 'npr_per_inr' => 1.6, 'advance_percent' => 30])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('onthrill@sbi', setting('upi_id'));
        $this->assertSame('9801234567', setting('esewa_id'));
        $this->assertEquals(30, setting('advance_percent'));

        // A mistyped UPI ID is caught before travellers see it.
        Livewire::test(SiteSettings::class)
            ->fillForm(['upi_id' => 'onthrill sbi'])
            ->call('save')
            ->assertHasFormErrors(['upi_id']);
    }

    public function test_admin_can_save_contact_details_and_they_appear_on_the_site(): void
    {
        $this->actingAs($this->admin());
        $this->get('/contact-us')->assertDontSee('+977 98510 00000');

        Livewire::test(SiteSettings::class)
            ->fillForm(['phone' => '+977 98510 00000', 'address' => 'Thamel, Kathmandu'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('+977 98510 00000', setting('phone'));
        $this->get('/')->assertSee('+977 98510 00000');
        $this->get('/contact-us')->assertSee('Thamel, Kathmandu');
    }

    public function test_admin_can_edit_page_content_and_it_shows_on_the_site(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(PageContent::class)
            ->fillForm([
                'contact_title' => 'Reach Us',
                'contact_intro' => 'We reply within one hour.',
                'gallery_title' => 'Temple Photos',
                'why_title' => 'Why Families Choose Us',
                'faqs' => [['question' => 'Is breakfast included?', 'answer' => 'Yes, on every hotel night.']],
                'home_sections' => ['hero', 'why', 'faq'],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // In the page sent to search engines, and in the wording the website app shows.
        $this->get('/contact-us')->assertSee('<title>Reach Us', false)->assertSee('We reply within one hour.');
        $this->get('/gallery')->assertSee('<h1>Temple Photos</h1>', false);
        $this->getJson('/api/site')
            ->assertJsonPath('texts.contact_title', 'Reach Us')
            ->assertJsonPath('site.why_title', 'Why Families Choose Us')
            ->assertJsonPath('site.faqs.0.question', 'Is breakfast included?')
            ->assertJsonPath('site.home_sections', ['hero', 'why', 'faq']);
    }

    public function test_smtp_settings_from_admin_are_used_for_sending(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(SiteSettings::class)
            ->fillForm(['mail_host' => 'smtp.example.com', 'mail_port' => 465, 'mail_encryption' => 'ssl', 'mail_username' => 'info@onthrill.com', 'mail_password' => 'app-password', 'mail_from_address' => 'info@onthrill.com'])
            ->call('save')
            ->assertHasNoFormErrors();

        Settings::applyMailConfig();

        $this->assertSame('smtp', config('mail.default'));
        $this->assertSame('smtp.example.com', config('mail.mailers.smtp.host'));
        $this->assertSame(465, config('mail.mailers.smtp.port'));
        $this->assertSame('smtps', config('mail.mailers.smtp.scheme'));
        $this->assertSame('app-password', config('mail.mailers.smtp.password'));
        $this->assertSame('info@onthrill.com', config('mail.from.address'));
        $this->assertNotSame('app-password', Setting::find('mail_password')->value); // encrypted at rest

        // Saving again with the password left empty keeps it.
        Livewire::test(SiteSettings::class)->call('save');
        $this->assertSame('app-password', setting('mail_password'));
    }

    public function test_admin_can_update_a_booking(): void
    {
        $booking = $this->booking();
        $this->actingAs($this->admin());

        Livewire::test(EditBooking::class, ['record' => $booking->id])
            ->fillForm(['status' => 'confirmed', 'payment_method' => 'upi', 'payment_reference' => 'UTR123', 'total' => 38000])
            ->call('save')
            ->assertHasNoFormErrors();

        $booking->refresh();
        $this->assertSame('confirmed', $booking->status);
        $this->assertSame('UTR123', $booking->payment_reference);
        $this->assertEquals(38000, $booking->total);

        // The "Advance received" shortcut.
        Livewire::test(EditBooking::class, ['record' => $booking->id])->callAction('advancePaid');
        $this->assertSame('advance_paid', $booking->fresh()->payment_status);
    }

    /**
     * Bookings use their reference and tours their slug in the public web address,
     * but the admin panel looks records up by id. Every record link the admin
     * panel shows must therefore use the id.
     */
    public function test_every_record_link_in_the_admin_panel_opens(): void
    {
        $type = TourCategory::create(['name' => 'Chardham Yatra']);
        $destination = Destination::create(['name' => 'Kedarnath', 'country' => 'India']);
        Package::create(['name' => 'Do Dham Yatra', 'tour_category_id' => $type->id, 'duration_days' => 6, 'price_inr' => 25000]);
        Page::create(['title' => 'About Us', 'content' => '<p>Hi</p>']);
        Inquiry::create(['name' => 'Sita', 'phone' => '9876543210', 'message' => 'Hello']);
        $booking = $this->booking();

        $this->actingAs($this->admin());

        $html = Livewire::test(LatestBookings::class)->html();
        foreach ([
            ListBookings::class,
            ListPackages::class,
            ListDestinations::class,
            ManageTourCategories::class,
            ListPages::class,
            ListInquiries::class,
        ] as $listPage) {
            $html .= Livewire::test($listPage)->html();
        }
        // The search box at the top of the admin panel.
        foreach ([$booking->reference, 'Dham', 'Kedar', 'About'] as $term) {
            $html .= Livewire::test(GlobalSearch::class)->set('search', $term)->html();
        }

        preg_match_all('#href="https?://[^/"]+(/admin/(?:bookings|packages|destinations|pages|tour-categories|inquiries)/[^"?]+)"#', $html, $matches);
        $links = array_unique($matches[1]);

        $this->assertContains("/admin/bookings/{$booking->id}/edit", $links, 'dashboard "Open" link uses the booking id');
        $this->assertContains("/admin/destinations/{$destination->id}/edit", $links, 'search finds destinations');
        $this->assertNotEmpty(preg_grep('#^/admin/packages/\d+/edit$#', $links), 'search finds packages');
        foreach ($links as $link) {
            $this->get($link)->assertOk();
        }

        // After creating a record, the admin lands on its edit screen.
        $redirect = Livewire::test(CreatePackage::class)
            ->fillForm(['name' => 'Kedarnath by Helicopter', 'duration_days' => 2, 'price_inr' => 60000])
            ->call('create')
            ->effects['redirect'] ?? null;
        $this->assertNotNull($redirect);
        $this->get(parse_url($redirect, PHP_URL_PATH))->assertOk();
    }

    public function test_every_admin_screen_loads(): void
    {
        $type = TourCategory::create(['name' => 'Chardham Yatra']);
        $destination = Destination::create(['name' => 'Kedarnath', 'country' => 'India', 'faqs' => [['question' => 'Q', 'answer' => 'A']]]);
        $package = Package::create(['name' => 'Do Dham Yatra', 'tour_category_id' => $type->id, 'duration_days' => 6, 'price_inr' => 25000, 'itinerary' => [['title' => 'Day', 'description' => 'Text']], 'highlights' => ['One']]);
        $package->destinations()->attach($destination);
        $page = Page::create(['title' => 'About Us', 'content' => '<p>Hi</p>']);
        Testimonial::create(['name' => 'Ram', 'content' => 'Great']);
        $inquiry = Inquiry::create(['name' => 'Sita', 'phone' => '9876543210', 'message' => 'Hello', 'package_id' => $package->id]);
        $booking = $this->booking(['package_id' => $package->id, 'package_name' => $package->name]);

        $admin = $this->admin();
        $this->actingAs($admin);

        foreach ([
            '/admin',
            '/admin/packages', '/admin/packages/create', "/admin/packages/{$package->id}/edit",
            '/admin/destinations', '/admin/destinations/create', "/admin/destinations/{$destination->id}/edit",
            '/admin/tour-categories',
            '/admin/bookings', "/admin/bookings/{$booking->id}/edit", "/admin/bookings/{$booking->id}",
            '/admin/inquiries', "/admin/inquiries/{$inquiry->id}/edit", "/admin/inquiries/{$inquiry->id}",
            '/admin/pages', '/admin/pages/create', "/admin/pages/{$page->id}/edit",
            '/admin/testimonials', '/admin/gallery-images',
            '/admin/users', '/admin/users/create', "/admin/users/{$admin->id}/edit",
            '/admin/roles', '/admin/roles/create', '/admin/roles/'.Role::findByName('manager')->id.'/edit',
            '/admin/site-settings', '/admin/page-content',
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }
}

<?php

namespace Tests\Feature;

use App\Filament\Pages\PageContent;
use App\Models\Destination;
use App\Models\Inquiry;
use App\Models\Package;
use App\Models\Page;
use App\Models\User;
use App\Support\Access;
use App\Support\Settings;
use App\Support\SiteTexts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

/** Wording managed from the admin panel, and the page layouts that use it. */
class SiteContentTest extends TestCase
{
    use RefreshDatabase;

    private Package $package;

    private Destination $destination;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();

        $this->destination = Destination::create(['name' => 'Muktinath Temple', 'country' => 'Nepal', 'region' => 'Mustang', 'is_active' => true, 'is_featured' => true]);
        $this->package = Package::create(['name' => 'Muktinath Yatra by Air', 'duration_days' => 6, 'duration_nights' => 5, 'price_inr' => 38500, 'summary' => 'Fly to Jomsom and reach Muktinath.', 'is_active' => true, 'is_featured' => true]);
        $this->package->destinations()->attach($this->destination);
    }

    public function test_every_piece_of_wording_has_a_field_in_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create()->assignRole(Access::SUPER_ADMIN));

        $page = Livewire::test(PageContent::class);
        foreach (array_keys(SiteTexts::defaults()) as $key) {
            $page->assertFormFieldExists($key);
        }
    }

    public function test_wording_edited_in_the_admin_shows_on_the_site(): void
    {
        $this->actingAs(User::factory()->create()->assignRole(Access::SUPER_ADMIN));

        Livewire::test(PageContent::class)
            ->fillForm([
                'menu_book_label' => 'Plan My Yatra',
                'pkg_enquiry_title' => 'Ask about this yatra',
                'dest_about_title' => 'All about :name',
                'footer_links_title' => 'Good to know',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        // Seen by a visitor (staff accounts do not get the booking button).
        auth()->logout();
        $this->get('/')->assertSee('Plan My Yatra')->assertSee('Good to know')->assertDontSee('Book a Tour');
        $this->get('/packages/muktinath-yatra-by-air')->assertSee('Ask about this yatra');
        // ":name" is filled in by the website app: "All about Muktinath Temple".
        $this->get('/destinations/muktinath-temple')->assertOk()->assertSee('All about :name');

        // A field left empty goes back to the original text.
        Settings::set(['menu_book_label' => '']);
        $this->get('/')->assertSee('Book a Tour');
    }

    public function test_country_page_intros_come_from_the_admin(): void
    {
        Settings::set(['country_intro_nepal' => 'Temples from Kathmandu to the Himalaya.']);

        $this->get('/destinations/country/nepal')->assertOk()
            ->assertSee('Temples and Sacred Places in Nepal')
            ->assertSee('Temples from Kathmandu to the Himalaya.');
    }

    public function test_enquiries_from_a_package_page_carry_the_package(): void
    {
        // The package page sends its id with the page, for the enquiry box beside it.
        $this->get('/packages/muktinath-yatra-by-air')->assertOk()
            ->assertSee('Enquire about this tour')
            ->assertSee('"id":'.$this->package->id.',"slug":"muktinath-yatra-by-air"', false);

        $this->postJson('/api/enquiry', ['name' => 'Ram Thapa', 'phone' => '9801234567', 'message' => 'Is there a departure in May?', 'package_id' => $this->package->id])
            ->assertCreated();

        $inquiry = Inquiry::sole();
        $this->assertSame($this->package->id, $inquiry->package_id);
        $this->assertSame('Muktinath Yatra by Air', $inquiry->topic());
    }

    public function test_enquiries_from_a_destination_page_carry_the_destination(): void
    {
        $this->getJson('/api/destinations/muktinath-temple')->assertOk()->assertJsonPath('destination.id', $this->destination->id);

        $this->postJson('/api/enquiry', ['name' => 'Sita', 'phone' => '9801234567', 'message' => 'Best month?', 'destination_id' => $this->destination->id])->assertCreated();

        $this->assertSame('Muktinath Temple', Inquiry::sole()->topic());
    }

    public function test_information_pages_have_a_sidebar(): void
    {
        Page::create(['title' => 'About Us', 'content' => '<p>We plan pilgrimages.</p>', 'is_active' => true]);
        Page::create(['title' => 'Travel Information', 'content' => '<p>Visas and permits.</p>', 'is_active' => true]);

        $this->get('/about-us')->assertOk()
            ->assertSee('More information')
            ->assertSee('/travel-information')
            ->assertSee('Planning a pilgrimage?')
            ->assertSee('Popular tours')
            ->assertSee('Muktinath Yatra by Air');
    }

    public function test_contact_page_shows_a_map(): void
    {
        // The app draws the map from the office address, or else the "map location".
        $this->get('/contact-us')->assertOk()->assertSee('Find us on the map');
        $this->getJson('/api/site')->assertJsonPath('site.map_location', 'Kathmandu, Nepal')->assertJsonPath('site.address', '');

        Settings::set(['address' => 'Thamel, Kathmandu']);
        $this->getJson('/api/site')->assertJsonPath('site.address', 'Thamel, Kathmandu');
    }

    public function test_packages_page_lists_the_tours_with_their_summaries(): void
    {
        $this->get('/packages')->assertOk()->assertSee('Muktinath Yatra by Air');
        $this->getJson('/api/packages')->assertOk()
            ->assertJsonPath('packages.data.0.summary', 'Fly to Jomsom and reach Muktinath.') // shown in the list view
            ->assertJsonPath('packages.meta.total', 1);
    }

    public function test_header_shows_phone_and_whatsapp_and_no_home_link(): void
    {
        Settings::set(['phone' => '+977 9851000000', 'whatsapp' => '+977 9800000000']);

        // The header (drawn by the app) links them as tel: and wa.me.
        $this->getJson('/api/site')->assertJsonPath('site.phone', '+977 9851000000')->assertJsonPath('site.whatsapp', '+977 9800000000');

        // The logo leads home; the main menu has no separate Home link.
        $header = file_get_contents(base_path('frontend/src/app/layout/header.ts'));
        preg_match('#<nav class="ml-auto hidden h-full xl:block" aria-label="Main">.*?</nav>#s', $header, $menu);
        $this->assertNotEmpty($menu);
        $this->assertStringNotContainsString("site.t('breadcrumb_home')", $menu[0]);
    }
}

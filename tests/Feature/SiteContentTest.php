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
        $this->get('/destinations/muktinath-temple')->assertSee('All about Muktinath Temple');

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
        $this->get('/packages/muktinath-yatra-by-air')->assertOk()
            ->assertSee('Enquire about this tour')
            ->assertSee('name="package_id" value="'.$this->package->id.'"', false);

        $this->from('/packages/muktinath-yatra-by-air')
            ->post('/enquiry', ['name' => 'Ram Thapa', 'phone' => '9801234567', 'message' => 'Is there a departure in May?', 'package_id' => $this->package->id])
            ->assertRedirect('/packages/muktinath-yatra-by-air#enquiry-form');

        $inquiry = Inquiry::sole();
        $this->assertSame($this->package->id, $inquiry->package_id);
        $this->assertSame('Muktinath Yatra by Air', $inquiry->topic());
    }

    public function test_enquiries_from_a_destination_page_carry_the_destination(): void
    {
        $this->get('/destinations/muktinath-temple')->assertOk()
            ->assertSee('name="destination_id" value="'.$this->destination->id.'"', false);

        $this->post('/enquiry', ['name' => 'Sita', 'phone' => '9801234567', 'message' => 'Best month?', 'destination_id' => $this->destination->id]);

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
        $this->get('/contact-us')->assertOk()
            ->assertSee('Find us on the map')
            ->assertSee('maps.google.com/maps?q=Kathmandu%2C%20Nepal', false);

        Settings::set(['address' => 'Thamel, Kathmandu']);
        $this->get('/contact-us')->assertSee('maps.google.com/maps?q=Thamel%2C%20Kathmandu', false);
    }

    public function test_packages_page_shows_a_list_by_default_with_a_grid_option(): void
    {
        $this->get('/packages')->assertOk()
            ->assertSee('data-view="list"', false)
            ->assertSee("setView('grid')", false)
            ->assertSee('Fly to Jomsom and reach Muktinath.'); // the summary shown in the list
    }

    public function test_header_shows_phone_and_whatsapp_and_no_home_link(): void
    {
        Settings::set(['phone' => '+977 9851000000', 'whatsapp' => '+977 9800000000']);

        $html = $this->get('/packages')->assertOk()->getContent();
        $this->assertStringContainsString('tel:9779851000000', $html);
        $this->assertStringContainsString('wa.me/9779800000000', $html);
        // The logo leads home; the main menu has no separate Home link.
        preg_match('#<nav[^>]*aria-label="Main".*?</nav>#s', $html, $menu);
        $this->assertNotEmpty($menu);
        $this->assertStringNotContainsString('>Home</a>', $menu[0]);

        // Transparent over the photo on the homepage only.
        $this->get('/')->assertSee('is-over-hero', false);
        $this->assertStringNotContainsString('is-over-hero', $html);
    }
}

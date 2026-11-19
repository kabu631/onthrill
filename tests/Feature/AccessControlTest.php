<?php

namespace Tests\Feature;

use App\Filament\Resources\Packages\Pages\ListPackages;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Http\Middleware\EnsureCanBook;
use App\Models\Booking;
use App\Models\Package;
use App\Models\User;
use App\Support\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/** Role-based access: who can reach what, and that nobody can escalate or lock everyone out. */
class AccessControlTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string ...$roles): User
    {
        return User::factory()->create()->assignRole(...$roles);
    }

    private function booking(?User $owner = null): Booking
    {
        return Booking::create([
            'reference' => Booking::nextReference(), 'user_id' => $owner?->id, 'package_name' => 'Pashupatinath Darshan',
            'name' => 'Gita Sharma', 'email' => 'g@example.com', 'phone' => '9876543210', 'nationality' => 'indian',
            'adults' => 2, 'currency' => 'INR', 'price_per_person' => 9000, 'total' => 18000, 'advance' => 4500,
        ]);
    }

    private function package(array $attributes = []): Package
    {
        return Package::create([
            'name' => 'Pashupatinath Darshan', 'duration_days' => 3, 'duration_nights' => 2, 'price_inr' => 9000, 'is_active' => true,
            ...$attributes,
        ]);
    }

    private function bookingForm(array $overrides = []): array
    {
        return [
            'travel_date' => now()->addMonth()->toDateString(), 'adults' => 2, 'children' => 0, 'nationality' => 'indian',
            'name' => 'Gita Sharma', 'phone' => '9876543210', 'email' => 'g@example.com', ...$overrides,
        ];
    }

    public function test_default_roles_exist_with_sensible_permissions(): void
    {
        $this->assertEqualsCanonicalizing(
            ['super-admin', 'customer', 'manager', 'booking-staff', 'content-editor'],
            Role::pluck('name')->all(),
        );

        $this->assertTrue(Role::findByName('booking-staff')->hasPermissionTo('bookings.update'));
        $this->assertFalse(Role::findByName('booking-staff')->hasPermissionTo('packages.update'));
        $this->assertFalse(Role::findByName('content-editor')->hasPermissionTo('bookings.view'));
        $this->assertFalse(Role::findByName('manager')->hasPermissionTo('roles.update'));
        $this->assertCount(0, Role::findByName('customer')->permissions);
    }

    public function test_customers_have_no_admin_access(): void
    {
        $customer = $this->userWithRole('customer');

        $this->assertFalse($customer->isStaff());
        $this->actingAs($customer)->get('/admin')->assertForbidden();
        $this->actingAs($customer)->get('/admin/bookings')->assertForbidden();
        $this->actingAs($customer)->get('/')->assertOk()->assertDontSee('Admin Panel');
    }

    public function test_booking_staff_see_bookings_but_cannot_touch_tours_users_or_settings(): void
    {
        $booking = $this->booking();
        $package = $this->package();
        $this->actingAs($this->userWithRole('booking-staff'));

        $this->get('/admin')->assertOk();
        $this->get('/admin/bookings')->assertOk();
        $this->get("/admin/bookings/{$booking->id}/edit")->assertOk();
        $this->get('/admin/inquiries')->assertOk();
        $this->get('/admin/packages')->assertOk(); // may look things up…

        $this->get('/admin/packages/create')->assertForbidden(); // …but not change them
        $this->get("/admin/packages/{$package->id}/edit")->assertForbidden();
        $this->get('/admin/destinations/create')->assertForbidden();
        $this->get('/admin/users')->assertForbidden();
        $this->get('/admin/roles')->assertForbidden();
        $this->get('/admin/site-settings')->assertForbidden();
        $this->get('/admin/pages')->assertForbidden();
    }

    public function test_content_editors_manage_tours_and_pages_but_not_bookings(): void
    {
        $booking = $this->booking();
        $this->actingAs($this->userWithRole('content-editor'));

        $this->get('/admin/packages/create')->assertOk();
        $this->get('/admin/destinations/create')->assertOk();
        $this->get('/admin/tour-categories')->assertOk();
        $this->get('/admin/pages')->assertOk();
        $this->get('/admin/page-content')->assertOk();

        $this->get('/admin/bookings')->assertForbidden();
        $this->get("/admin/bookings/{$booking->id}/edit")->assertForbidden();
        $this->get('/admin/site-settings')->assertForbidden();
    }

    public function test_view_only_staff_cannot_edit_even_through_inline_toggles(): void
    {
        Role::create(['name' => 'viewer', 'guard_name' => 'web'])->syncPermissions(['bookings.view', 'packages.view']);
        $booking = $this->booking();
        $package = $this->package();
        $this->actingAs($this->userWithRole('viewer'));

        $this->get("/admin/bookings/{$booking->id}")->assertOk();
        $this->get("/admin/bookings/{$booking->id}/edit")->assertForbidden();

        // The "Visible" switch in the list is an edit, so it must be refused.
        Livewire::test(ListPackages::class)->call('updateTableColumnState', 'is_active', (string) $package->id, false);
        $this->assertTrue($package->fresh()->is_active);

        // Control: the very same call succeeds for someone who may edit packages.
        Livewire::actingAs($this->userWithRole('content-editor'))
            ->test(ListPackages::class)->call('updateTableColumnState', 'is_active', (string) $package->id, false);
        $this->assertFalse($package->fresh()->is_active);
    }

    public function test_super_admin_can_create_a_role_and_it_takes_effect(): void
    {
        $this->actingAs($this->userWithRole(Access::SUPER_ADMIN));

        Livewire::test(CreateRole::class)
            ->fillForm(['name' => 'Gallery Helper', 'area_gallery' => ['view', 'create'], 'area_testimonials' => ['view']])
            ->call('create')
            ->assertHasNoFormErrors();

        $role = Role::findByName('gallery-helper');
        $this->assertEqualsCanonicalizing(['gallery.view', 'gallery.create', 'testimonials.view'], $role->permissions->pluck('name')->all());

        // Editing shows the ticked boxes and saves changes.
        Livewire::test(EditRole::class, ['record' => $role->id])
            ->assertFormSet(['area_gallery' => ['view', 'create']])
            ->fillForm(['area_gallery' => ['view']])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertFalse($role->fresh()->hasPermissionTo('gallery.create'));

        $helper = $this->userWithRole('gallery-helper');
        $this->actingAs($helper)->get('/admin/gallery-images')->assertOk();
        $this->actingAs($helper)->get('/admin/packages')->assertForbidden();
    }

    public function test_role_names_must_be_unique_and_builtin_roles_are_locked(): void
    {
        $this->actingAs($this->userWithRole(Access::SUPER_ADMIN));

        Livewire::test(CreateRole::class)->fillForm(['name' => 'Manager'])->call('create')->assertHasFormErrors(['name']);
        Livewire::test(CreateRole::class)->fillForm(['name' => 'Super Admin'])->call('create')->assertHasFormErrors(['name']);

        $this->get('/admin/roles/'.Role::findByName(Access::SUPER_ADMIN)->id.'/edit')->assertForbidden();
        $this->get('/admin/roles/'.Role::findByName(Access::CUSTOMER)->id.'/edit')->assertForbidden();
    }

    public function test_super_admin_can_assign_roles_to_a_user(): void
    {
        $staff = $this->userWithRole('customer');
        $this->actingAs($this->userWithRole(Access::SUPER_ADMIN));

        Livewire::test(EditUser::class, ['record' => $staff->id])
            ->fillForm(['roles' => [Role::findByName('booking-staff')->id]])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame(['booking-staff'], $staff->fresh()->getRoleNames()->all());
        $this->assertTrue($staff->fresh()->isStaff());
    }

    public function test_nobody_can_escalate_or_lock_everyone_out(): void
    {
        $superAdmin = $this->userWithRole(Access::SUPER_ADMIN);

        // A custom role that may manage users, but is not Super Admin.
        Role::create(['name' => 'hr', 'guard_name' => 'web'])->syncPermissions(['users.view', 'users.update', 'users.delete']);
        $hr = $this->userWithRole('hr');
        $customer = $this->userWithRole('customer');

        // HR cannot edit or delete a Super Admin…
        $this->actingAs($hr)->get("/admin/users/{$superAdmin->id}/edit")->assertForbidden();
        $this->assertFalse($hr->can('delete', $superAdmin));

        // …and, lacking roles.update, cannot hand out roles: the field is not on the form,
        // so even a forged submission leaves roles untouched.
        Livewire::actingAs($hr)->test(EditUser::class, ['record' => $customer->id])
            ->assertFormFieldDoesNotExist('roles')
            ->call('save');
        $this->assertSame(['customer'], $customer->fresh()->getRoleNames()->all());

        // Nobody deletes themselves, and the last Super Admin cannot be deleted.
        $this->assertFalse($superAdmin->can('delete', $superAdmin));
        $this->assertFalse($hr->can('delete', $hr));
        $second = $this->userWithRole(Access::SUPER_ADMIN);
        $this->assertTrue($superAdmin->can('delete', $second));
        $second->delete();
        $this->assertFalse($hr->can('delete', $superAdmin));
    }

    public function test_customers_only_see_their_own_bookings(): void
    {
        $alice = $this->userWithRole('customer');
        $bob = $this->userWithRole('customer');
        $booking = $this->booking($alice);

        $this->actingAs($alice)->get(route('account.booking', $booking))->assertOk()->assertSee($booking->reference);
        $this->actingAs($bob)->get(route('account.booking', $booking))->assertNotFound();
        $this->actingAs($bob)->get(route('bookings.show', $booking))->assertNotFound();

        $this->assertTrue($alice->can('view', $booking));
        $this->assertFalse($bob->can('view', $booking));
        $this->assertFalse($alice->can('update', $booking));
    }

    public function test_sign_in_sends_staff_to_admin_and_customers_to_their_account(): void
    {
        $staff = $this->userWithRole('booking-staff');
        $customer = $this->userWithRole('customer');

        $this->post('/login', ['email' => $staff->email, 'password' => 'password'])->assertRedirect('/admin');
        $this->get('/')->assertSee('Admin Panel');
        $this->post('/logout');

        $this->post('/login', ['email' => $customer->email, 'password' => 'password'])->assertRedirect('/my-account');
    }

    public function test_customers_are_never_sent_to_the_admin_panel_after_signing_up_or_in(): void
    {
        // Someone opens /admin while signed out (the browser remembers it), then
        // creates a customer account on the website.
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/register', ['name' => 'Kabin', 'email' => 'new@example.com', 'password' => 'secret-123', 'password_confirmation' => 'secret-123'])
            ->assertRedirect(route('account.index'));
        $this->post('/logout');

        // Same for signing in.
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->post('/login', ['email' => 'new@example.com', 'password' => 'secret-123'])->assertRedirect(route('account.index'));
        $this->post('/logout');

        // Other pages a customer was trying to reach are still remembered...
        $this->get('/my-account/bookings/OT-1')->assertRedirect('/login');
        $this->post('/login', ['email' => 'new@example.com', 'password' => 'secret-123'])->assertRedirect('/my-account/bookings/OT-1');
        $this->post('/logout');

        // ...and staff still land where they were heading in the admin panel.
        $staff = $this->userWithRole('booking-staff');
        $this->get('/admin/bookings')->assertRedirect('/admin/login');
        $this->post('/login', ['email' => $staff->email, 'password' => 'password'])->assertRedirect('/admin/bookings');
    }

    public function test_a_customer_at_the_admin_panel_sees_why_and_can_switch_accounts(): void
    {
        $customer = $this->userWithRole('customer');

        $this->actingAs($customer)->get('/admin')->assertForbidden()
            ->assertSee('This account cannot open the admin panel')
            ->assertSee($customer->email)
            ->assertSee('Sign out and go to admin login');

        $this->post('/logout', ['next' => 'admin-login'])->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_staff_cannot_book_but_guests_and_customers_can(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class); // many posts in one test
        $package = $this->package();
        $url = '/packages/'.$package->slug;

        foreach ([Access::SUPER_ADMIN, 'manager', 'booking-staff', 'content-editor'] as $role) {
            $this->actingAs($this->userWithRole($role));

            // The page shows a notice instead of the booking form…
            $this->get($url)->assertOk()->assertSee('Booking is turned off for staff accounts')->assertDontSee('Send booking request');

            // …and the server refuses, whether JavaScript is used or not.
            $this->postJson($url.'/book', $this->bookingForm())->assertForbidden()->assertJson(['message' => EnsureCanBook::MESSAGE]);
            $this->from($url)->post($url.'/book', $this->bookingForm())->assertRedirect($url)->assertSessionHas('error');
            $this->assertSame(0, Booking::count(), "$role made a booking");

            $this->post('/logout');
        }

        // A customer account and a guest can both book.
        $customer = $this->userWithRole('customer');
        $this->actingAs($customer)->get($url)->assertSee('Send booking request');
        $this->post($url.'/book', $this->bookingForm())->assertRedirect();
        $this->assertSame($customer->id, Booking::sole()->user_id);
        $this->post('/logout');

        $this->post($url.'/book', $this->bookingForm(['email' => 'guest@example.com']))->assertRedirect();
        $this->assertSame(2, Booking::count());
    }
}

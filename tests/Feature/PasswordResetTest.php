<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Models\User;
use App\Notifications\ResetPasswordLink;
use App\Support\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function customer(): User
    {
        return User::factory()->create(['name' => 'Sita Devi', 'email' => 'sita@example.com'])->assignRole(Access::CUSTOMER);
    }

    public function test_sign_in_page_links_to_forgot_password(): void
    {
        $this->get('/login')->assertOk()->assertSee(route('password.request'));
        $this->get('/forgot-password')->assertOk()->assertSee('Email me a link');
    }

    public function test_customer_gets_a_reset_email_with_the_website_link(): void
    {
        Notification::fake();
        $user = $this->customer();

        $this->from('/forgot-password')->post('/forgot-password', ['email' => 'sita@example.com'])
            ->assertRedirect('/forgot-password')
            ->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordLink::class, function (ResetPasswordLink $notification) use ($user) {
            $mail = $notification->toMail($user);
            $html = (string) $mail->render();

            return str_starts_with($notification->url, route('password.reset', ['token' => $notification->token]))
                && $mail->subject === 'Reset your password | '.setting('site_name')
                && str_contains($html, 'Namaste Sita')
                && str_contains($html, 'Choose a new password')
                && str_contains($html, $notification->url);
        });
    }

    public function test_unknown_email_gets_the_same_answer_and_nothing_is_sent(): void
    {
        Notification::fake();
        $this->customer();

        $this->from('/forgot-password')->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertSessionHas('status', fn ($status) => str_contains($status, 'If an account exists for nobody@example.com'))
            ->assertSessionHasNoErrors();

        Notification::assertNothingSent();
    }

    public function test_customer_chooses_a_new_password_and_is_signed_in(): void
    {
        $user = $this->customer();
        $token = Password::createToken($user);

        $this->get("/reset-password/{$token}?email=sita@example.com")->assertOk()->assertSee('Choose a new password')->assertSee('sita@example.com');

        $this->post('/reset-password', ['token' => $token, 'email' => 'sita@example.com', 'password' => 'new-secret-123', 'password_confirmation' => 'new-secret-123'])
            ->assertRedirect(route('account.index'));

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('new-secret-123', $user->fresh()->password));

        // The link works only once.
        $this->post('/logout');
        $this->post('/reset-password', ['token' => $token, 'email' => 'sita@example.com', 'password' => 'another-pass-1', 'password_confirmation' => 'another-pass-1'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('new-secret-123', $user->fresh()->password));
    }

    public function test_wrong_or_mismatched_details_are_refused(): void
    {
        $user = $this->customer();
        $token = Password::createToken($user);

        $this->post('/reset-password', ['token' => 'not-the-token', 'email' => 'sita@example.com', 'password' => 'new-secret-123', 'password_confirmation' => 'new-secret-123'])
            ->assertSessionHasErrors('email');
        $this->post('/reset-password', ['token' => $token, 'email' => 'sita@example.com', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');
        $this->post('/reset-password', ['token' => $token, 'email' => 'sita@example.com', 'password' => 'new-secret-123', 'password_confirmation' => 'different-123'])
            ->assertSessionHasErrors('password');

        $this->assertGuest();
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_staff_land_in_the_admin_panel_after_resetting(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com'])->assignRole(Access::SUPER_ADMIN);
        $token = Password::createToken($admin);

        $this->post('/reset-password', ['token' => $token, 'email' => 'admin@example.com', 'password' => 'new-secret-123', 'password_confirmation' => 'new-secret-123'])
            ->assertRedirect('/admin');
        $this->get('/admin')->assertOk();
    }

    public function test_admin_login_page_has_forgot_password_and_sends_the_same_email(): void
    {
        Notification::fake();
        $admin = User::factory()->create(['name' => 'Ram Prasad', 'email' => 'admin@example.com'])->assignRole(Access::SUPER_ADMIN);

        $this->get('/admin/login')->assertOk()->assertSee('/admin/password-reset/request');
        $this->get('/admin/password-reset/request')->assertOk();

        Livewire::test(RequestPasswordReset::class)->fillForm(['email' => 'admin@example.com'])->call('request')->assertNotified();

        Notification::assertSentTo($admin, ResetPasswordLink::class, function (ResetPasswordLink $notification) use ($admin) {
            return str_contains($notification->url, '/admin/password-reset/reset')
                && str_contains((string) $notification->toMail($admin)->render(), 'Namaste Ram');
        });

        // An unknown address looks exactly the same from outside.
        Livewire::test(RequestPasswordReset::class)->fillForm(['email' => 'nobody@example.com'])->call('request')
            ->assertNotified(__(Password::RESET_LINK_SENT));
        Notification::assertSentTimes(ResetPasswordLink::class, 1);
    }

    public function test_password_can_be_set_from_the_server_when_email_is_not_working(): void
    {
        $user = $this->customer();

        $this->artisan('user:password', ['email' => 'sita@example.com', '--password' => 'server-set-123'])
            ->expectsOutputToContain('New password for sita@example.com')
            ->assertSuccessful();
        $this->assertTrue(Hash::check('server-set-123', $user->fresh()->password));

        $this->artisan('user:password', ['email' => 'nobody@example.com'])->assertFailed();
        $this->artisan('user:password', ['email' => 'sita@example.com', '--password' => 'short'])->assertFailed();
    }
}

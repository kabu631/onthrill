<?php

namespace Tests\Feature;

use App\Filament\Pages\Auth\Login as AdminLogin;
use App\Models\User;
use App\Support\Access;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\Cookie;
use Tests\TestCase;

/** Signing in lasts until the browser is closed or the PC restarts; then the visitor signs in again. */
class SignInSessionTest extends TestCase
{
    use RefreshDatabase;

    private function cookies($response): array
    {
        return collect($response->headers->getCookies())->keyBy(fn (Cookie $cookie) => $cookie->getName())->all();
    }

    public function test_the_site_is_set_to_end_sign_ins_when_the_browser_closes(): void
    {
        $this->assertTrue(config('session.expire_on_close'));
    }

    public function test_website_sign_in_ends_with_the_browser_and_ignores_remember_me(): void
    {
        $user = User::factory()->create(['remember_token' => null])->assignRole(Access::CUSTOMER);

        $this->get('/login')->assertOk()->assertDontSee('name="remember"', false)->assertDontSee('Keep me signed in');

        // Even if an old form or a browser sends "remember", it is not used.
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password', 'remember' => '1']);
        $response->assertRedirect(route('account.index'));
        $this->assertAuthenticatedAs($user);

        $cookies = $this->cookies($response);
        $this->assertArrayHasKey(config('session.cookie'), $cookies);
        $this->assertSame(0, $cookies[config('session.cookie')]->getExpiresTime(), 'the sign-in cookie must be deleted when the browser closes');
        $this->assertEmpty(array_filter(array_keys($cookies), fn ($name) => str_starts_with($name, 'remember_')), 'no long-lasting "remember me" cookie');
        $this->assertNull($user->fresh()->remember_token, 'a "remember me" sign-in would have created a token');
    }

    public function test_admin_login_has_no_remember_me(): void
    {
        $admin = User::factory()->create(['remember_token' => null])->assignRole(Access::SUPER_ADMIN);

        $this->get('/admin/login')->assertOk()->assertDontSee('Remember me');

        Livewire::test(AdminLogin::class)
            ->fillForm(['email' => $admin->email, 'password' => 'password'])
            ->set('data.remember', true) // even if forced
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($admin);
        $this->assertNull($admin->fresh()->remember_token, 'a "remember me" login would have created a token');
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_chinese_username(): void
    {
        $response = $this->withSession(['_token' => 'test-token'])->post(route('register.store'), [
            '_token' => 'test-token',
            'username' => '张三',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'username' => '张三',
        ]);
    }

    public function test_register_username_still_rejects_spaces(): void
    {
        $response = $this->from(route('register'))->withSession(['_token' => 'test-token'])->post(route('register.store'), [
            '_token' => 'test-token',
            'username' => '张 三',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('username');
        $this->assertSame(0, User::count());
    }

    public function test_pages_render_apple_touch_icon_link(): void
    {
        foreach ([route('home'), route('login'), route('register')] as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('rel="apple-touch-icon"', false)
                ->assertSee('apple-touch-icon.png', false);
        }

        $user = User::create([
            'username' => 'icon-user',
            'password' => 'Password@123',
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('rel="apple-touch-icon"', false)
            ->assertSee('apple-touch-icon.png', false);

        $this->actingAs($user)->get(route('password.change'))
            ->assertOk()
            ->assertSee('rel="apple-touch-icon"', false)
            ->assertSee('apple-touch-icon.png', false);
    }

    public function test_registered_user_password_is_correctly_hashed(): void
    {
        $response = $this->post(route('register.store'), [
            'username' => 'hash-test-user',
            'password' => 'ValidPass@123',
            'password_confirmation' => 'ValidPass@123',
        ]);
        $response->assertRedirect(route('dashboard'));
        
        $this->post(route('logout'));
        
        $this->post(route('login.attempt'), [
            'username' => 'hash-test-user',
            'password' => 'ValidPass@123',
        ])->assertRedirect(route('dashboard'));
    }
}

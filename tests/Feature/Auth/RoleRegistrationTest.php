<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RoleRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_register_with_user_role()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'user',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'user@example.com',
            'role' => 'user',
        ]);

        $user = User::where('email', 'user@example.com')->first();
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function user_can_register_with_admin_role()
    {
        $response = $this->post('/register', [
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'admin@example.com')->first();
        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isUser());
    }

    /** @test */
    public function user_defaults_to_user_role_when_no_role_selected()
    {
        $response = $this->post('/register', [
            'name' => 'Test Default User',
            'email' => 'default@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'default@example.com',
            'role' => 'user', // Should default to user
        ]);

        $user = User::where('email', 'default@example.com')->first();
        $this->assertTrue($user->isUser());
        $this->assertFalse($user->isAdmin());
    }



    /** @test */
    public function registration_form_has_role_selection_fields()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Jenis Akun');
        $response->assertSee('Anggota');
        $response->assertSee('Admin');
        $response->assertSee('role_user');
        $response->assertSee('role_admin');
    }

    /** @test */
    public function invalid_role_is_rejected()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'invalid@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'invalid_role',
            'terms' => '1',
        ]);

        $response->assertSessionHasErrors(['role']);
        $this->assertDatabaseMissing('users', ['email' => 'invalid@example.com']);
    }
}

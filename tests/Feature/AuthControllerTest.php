<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;



class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->createOne();
    }

    /**
     * Test user authentication.
     *
     * @return void
     */
    public function testUserAuthentication()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    /**
     * Test getting authenticated user.
     *
     * @return void
     */
    public function testGetAuthenticatedUser()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/auth/user');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'email' => $this->user->email,
            ]);
    }

    /**
     * Test user logout.
     *
     * @return void
     */
    public function testUserLogout()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Successfully logged out',
            ]);
    }

    /**
     * Test token refresh.
     *
     * @return void
     */
    public function testTokenRefresh()
    {
        $token = $this->login();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/auth/refresh');

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
    }

    /**
     * Logs in a user and returns the token.
     * 
     * @return string
     */
    public function login()
    {
        Auth::login($this->user);
        return Auth::tokenById($this->user->id);
    }
}
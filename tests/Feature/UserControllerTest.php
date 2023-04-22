<?php

namespace Tests\Feature;

use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = UserFactory::new()->createOne();
    }


    /**
     * Test store method.
     *
     * @return void
     */
    public function testStore()
    {
        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->user->password,
        ];

        $response = $this->postJson('/api/user', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in'
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
    }

    /**
     * Test update method.
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->user->password,
        ];

        $token = $this->login();

        $response = $this->putJson('/api/user/' . $this->user->id, $data, [
            'Authorization' => 'Bearer ' . $token
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User updated successfully',
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $data['name'],
            'email' => $data['email'],
        ]);
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
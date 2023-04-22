<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Factories\ProductFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductControllerTest extends TestCase
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
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 0, 1000),
            'user_id' => $this->user->id,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/product', $data);

        $response->assertStatus(200)
            ->assertJson([
                'name' => $data['name'],
                'price' => $data['price'],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'price' => $data['price'],
        ]);
    }

    /**
     * Test update method.
     *
     * @return void
     */
    public function testUpdate()
    {
        $product = ProductFactory::new()->withUserId($this->user->id)->createOne();

        $data = [
            'name' => $this->faker->name(),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 0, 1000),
        ];

        $response = $this->actingAs($this->user)->putJson('/api/product/' . $product->id, $data);

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => $data['name'],
            'price' => $data['price'],
        ]);
    }

    /**
     * Test delete method.
     *
     * @return void
     */
    public function testDelete()
    {
        $product = ProductFactory::new()->withUserId($this->user->id)->createOne();
        $response = $this->actingAs($this->user)->deleteJson('/api/product/' . $product->id);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }
}
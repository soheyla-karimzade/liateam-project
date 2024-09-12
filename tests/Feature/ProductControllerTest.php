<?php

namespace Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    private $token;
    private $product;
    private $order;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->token = $this->getTokenUser($this->user)['token'];

        $this->product = Product::factory(5)->create();
        $this->order = Order::factory()->create(['user_id' => $this->user->id]);

    }


    public function test_can_create_product()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Product1',
            'price' => 100.00,
            'inventory' => 10,
        ],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'name', 'price', 'inventory', 'created_at', 'updated_at'
            ]);
    }

    public function test_can_list_products()
    {
        $response = $this->getJson('/api/products',
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['name', 'price', 'inventory', 'created_at', 'updated_at']
            ]);
    }

    public function test_can_show_product()
    {
        $response = $this->getJson('/api/products/' . $this->product->first()->id,
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'name', 'price', 'inventory', 'created_at', 'updated_at'
            ]);
    }

    public function test_can_update_product()
    {

        $response = $this->putJson('/api/products/' . $this->product->first()->id, [
            'name' => 'Updated Product 1',
            'price' => 150.00,
            'inventory' => 20,
        ],
        [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Product 1',
                'price' => 150.00,
                'inventory' => 20,
            ]);
    }

    public function test_can_delete_product()
    {
        $response = $this->deleteJson('/api/products/' . $this->product->first()->id,[],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('products', ['id' => $this->product->first()->id]);
    }
}

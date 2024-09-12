<?php

namespace Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
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

        $this->product = Product::factory(2)->create();
        $this->order = Order::factory()->create(['user_id' => $this->user->id]);

    }

    public function test_can_create_order()
    {

        $product1 = $this->product->first();
        $product2 = $this->product->last();

        $response = $this->postJson('/api/orders', [
            'products' => [
                ['id' => $product1->id, 'quantity' => 2],
                ['id' => $product2->id, 'quantity' => 1]
            ],
            'count' => 1,
            'total_price' => 100.00,
        ],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        );
        $response->assertStatus(201)
            ->assertJsonStructure([
                'products', 'count', 'total_price', 'created_at', 'updated_at'
            ]);
    }

    public function test_cannot_create_order_without_required_data()
    {
        $product1 = $this->product->first();
        $product2 = $this->product->last();

        $response = $this->postJson('/api/orders', [
            'count' => 1,
            'total_price' => 100.00,
        ],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        );

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        $response->assertStatus(422);
        $this->assertNotNull( $decodedContent['errors']);
        $this->assertSame('The products field is required.', $decodedContent['message']);
        $this->assertSame('The products field is required.',$decodedContent['errors']['products'][0]);
    }



    public function test_can_list_orders()
    {
        $response = $this->getJson('/api/orders', [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['products', 'count', 'total_price', 'created_at', 'updated_at']
            ]);
    }

    public function test_can_show_order()
    {

        $response = $this->getJson('/api/orders/' . $this->order->id, [
            'Authorization' => 'Bearer ' . $this->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products', 'count', 'total_price', 'created_at', 'updated_at'
            ]);
    }

    public function test_can_update_order()
    {
        $product1 = $this->product->first();
        $product2 = $this->product->last();

        $response = $this->putJson('/api/orders/' . $this->order->id, [
            'products' => [
                ['id' => $product1->id, 'quantity' => 3],
                ['id' => $product2->id, 'quantity' => 2]
            ],
            'count' => 2,
            'total_price' => 200.00,
        ],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'count' => 2,
                'total_price' => 200.00,
            ]);
    }

    public function test_can_delete_order()
    {
        $response = $this->deleteJson('/api/orders/' . $this->order->id,[],
            [
                'Authorization' => 'Bearer ' . $this->token,
            ]);
        $response->assertStatus(204);
        $this->assertDatabaseMissing('orders', ['id' => $this->order->id]);
    }
}

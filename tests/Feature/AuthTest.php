<?php

namespace Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $password;

    protected function setUp(): void
    {
        parent::setUp();
        $this->password='password123';
        $this->user = User::factory()->create([
            'password' => bcrypt( 'password123'),
        ]);
        $this->token = $this->getTokenUser($this->user,'password123')['token'];

    }
    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        $this->assertSame('Test User', $decodedContent['user']['name']);
        $this->assertSame('test@example.com', $decodedContent['user']['email']);
        $this->assertNotNull( $decodedContent['token']);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message', 'user', 'token'
            ]);
    }

    public function test_user_can_login()
    {

        $response = $this->postJson('/api/login', [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);

        $content = $response->getContent();
        $decodedContent = json_decode($content, true);

        $this->assertNotNull( $decodedContent['token']);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message', 'token'
            ]);
    }


    public function test_user_can_logout(){
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .  $this->token ,
        ])->postJson('/api/logout');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_access_protected_route()
    {

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' .  $this->token ,
        ])->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJson([
                'email' => $this->user->email
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_route()
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
    }
}

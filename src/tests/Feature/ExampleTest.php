<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can access home page
     */
    public function test_authenticated_user_can_access_home(): void
    {
        /** @var Authenticatable $user */
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated user is redirected to login
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}

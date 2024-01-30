<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    // use RefreshDatabase;

    public function testAjouterRole()
    {
        $role=Role::factory()->create();
        $response=$this
           ->actingAs($role)
           ->post('api/')

    }
    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    

    
}

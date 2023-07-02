<?php

namespace Tests\Feature\Web\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_action_when_the_user_is_not_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();

        $response = $this->get("/profile/{$user->id}");

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    public function test_show_action_when_the_user_is_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();

        $response = $this->actingAs($user)->get("/profile/{$user->id}");

        $response->assertOk()
            ->assertViewIs('profile.show')
            ->assertViewHas([
                'user',
                'allEvents',
                'myEvents',
                'userEvents'
            ]);
    }
}

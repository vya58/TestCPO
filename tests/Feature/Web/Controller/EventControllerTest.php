<?php

namespace Tests\Feature\Web\Controller;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Event;
use App\Models\Participation;
use App\Models\User;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_action_when_the_user_is_not_authenticated(): void
    {
        $response = $this->get('/event');

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    public function test_index_action_when_the_user_is_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();

        $response = $this->actingAs($user)->get('/event');

        $response->assertOk()
            ->assertViewIs('event.index')
            ->assertViewHas([
                'allEvents',
                'myEvents'
            ]);
    }

    public function test_self_action_when_the_user_is_not_authenticated(): void
    {
        $response = $this->get('/self');

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    public function test_self_action_when_the_user_is_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();

        $response = $this->actingAs($user)->get('/self');

        $response->assertOk()
            ->assertViewIs('event.self')
            ->assertViewHas([
                'allEvents',
                'myEvents'
            ]);
    }

    public function test_show_action_when_the_user_is_not_authenticated(): void
    {
        $event = Event::factory()->state([
            'user_id' => User::factory()->create()
        ])->create();

        $response = $this->get("/event/{$event->id}");

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    public function test_show_action_when_the_user_is_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();
        $event = Event::all()->random();

        $response = $this->actingAs($user)->get("/event/{$event->id}");

        $response->assertOk()
            ->assertViewIs('event.show')
            ->assertViewHas([
                'allEvents',
                'myEvents',
                'event',
                'members'
            ]);
    }

    public function test_create_action_when_the_user_is_not_authenticated(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $event = Event::all()->random();

        $response = $this->get("/event/create/{$event->id}");

        $this->assertGuest();

        $response->assertRedirect('/login');
    }

    public function test_create_action_when_the_user_is_authenticated_and_from_not_participating_in_event(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::factory()->create();
        $event = Event::all()->random();

        $response = $this->actingAs($user)->get("/event/create/{$event->id}");

        $response->assertRedirect("/event/{$event->id}");

        $this->assertDatabaseHas('participations', [
            'event_id' => $event->id,
            'user_id' => $user->id
        ]);
    }

    public function test_create_action_when_the_user_is_authenticated_and_participating_in_event(): void
    {
        // Запуск `DatabaseSeeder`
        $this->seed();

        $user = User::all()->random();
        $event = Event::all()->random();

        Participation::firstOrCreate(
            [
                'event_id' => $event->id,
                'user_id' => $user->id,
            ],
        );

        $this->assertDatabaseHas('participations', [
            'event_id' => $event->id,
            'user_id' => $user->id
        ]);

        $response = $this->actingAs($user)->get("/event/create/{$event->id}");

        $response->assertRedirect("/event/{$event->id}");

        $this->assertDatabaseMissing('participations', [
            'event_id' => $event->id,
            'user_id' => $user->id
        ]);
    }
}

<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Factories\Sequence;

class EventControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_action_when_the_user_is_not_authorized(): void
    {
        User::factory(5)->create();

        $eventCount = 10;

        Event::factory($eventCount)->hasUsers(mt_rand(3, 5))->state(new Sequence(
            fn ($sequence) => ['user_id' => User::all()->random()],
        ))->create();

        $response = $this->getJson('/api/events');

        $response->assertUnauthorized();
    }

    public function test_index_action_when_the_user_is_logged_in(): void
    {
        User::factory(5)->create();

        $user = Sanctum::actingAs(User::factory()->create());

        $eventCount = 10;

        Event::factory($eventCount)->hasUsers(mt_rand(3, 5))->state(new Sequence(
            fn ($sequence) => ['user_id' => User::all()->random()],
        ))->create();

        $response = $this->actingAs($user)->getJson('/api/events');

        $response
            ->assertOk()
            ->assertJsonStructure([
                'error',
                'result' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'user_id'
                    ]
                ],
            ])
            ->assertJsonFragment([
                'error' => null
            ])
            ->assertJsonCount($eventCount, 'result.*');
    }

    public function test_store_action_when_the_user_is_not_authorized(): void
    {
        User::factory()->create();

        $response = $this->postJson('/api/events', [
            'title' => fake()->sentence(),
            'description' => fake()->text()
        ]);

        $response->assertUnauthorized();
    }

    public function test_store_action_when_the_user_is_logged_in(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $reguestData = [
            'title' => fake()->sentence(),
            'description' => fake()->text()
        ];

        $response = $this->actingAs($user)->postJson('/api/events', $reguestData);

        $this->assertDatabaseHas('events', [
            'title' => $reguestData['title'],
            'description' => $reguestData['description'],
        ]);

        $event = Event::where('title', $reguestData['title'])->first();

        $response
            ->assertCreated()
            ->assertJsonStructure([
                'error',
                'result' => [
                    'id',
                    'title',
                    'description',
                    'user_id'
                ],
            ])
            ->assertJsonFragment([
                'error' => null,
                'result' => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'user_id' => $user->id
                ],
            ]);
    }

    public function test_destroy_action_when_the_user_is_not_authorized(): void
    {
        $user = User::factory()->create();

        $event = Event::factory()->state([
            'user_id' => $user->id
        ])->create();

        // Проверка, что событие существует
        $this->assertModelExists($event);

        $response = $this->deleteJson("/api/events/{$event->id}");

        $response->assertUnauthorized();

        // Проверка, что событие не удалено
        $this->assertModelExists($event);
    }

    public function test_destroy_action_when_non_event_author_is_logged_in(): void
    {
        $author = User::factory()->create();

        $user = Sanctum::actingAs(User::factory()->create());

        $event = Event::factory()->state([
            'user_id' => $author->id
        ])->create();

        // Проверка, что событие существует
        $this->assertModelExists($event);

        $response = $this->actingAs($user)->deleteJson("/api/events/{$event->id}");

        $response->assertForbidden();

        // Проверка, что событие не удалено
        $this->assertModelExists($event);
    }

    public function test_destroy_action_when_event_author_is_logged_in(): void
    {
        $author = Sanctum::actingAs(User::factory()->create());

        $event = Event::factory()->state([
            'user_id' => $author->id
        ])->create();

        // Проверка, что событие существует
        $this->assertModelExists($event);

        $response = $this->actingAs($author)->deleteJson("/api/events/{$event->id}");

        $response->assertNoContent();

        // Проверка, что событие удалено
        $this->assertModelMissing($event);
    }
}

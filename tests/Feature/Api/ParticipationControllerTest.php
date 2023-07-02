<?php

namespace Tests\Feature\Api;

use App\Models\Event;
use App\Models\Participation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Symfony\Component\HttpFoundation\Response;

class ParticipationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_action_when_the_user_is_not_authorized(): void
    {
        $event = Event::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'user_id' => User::factory()->create(),
                ],
            ))
            ->create();

        User::factory()->create();

        $response = $this->postJson("/api/participate/{$event->id}");

        $response->assertUnauthorized();
    }

    public function test_store_action_when_the_user_is_logged_in(): void
    {
        $event = Event::factory()
            ->state(new Sequence(
                fn ($sequence) => [
                    'user_id' => User::factory()->create(),
                ],
            ))
            ->create();

        $participant = Sanctum::actingAs(User::factory()->create());

        $response = $this->actingAs($participant)->postJson("/api/participate/{$event->id}");

        $this->assertDatabaseHas('participations', [
            'event_id' => $event->id,
            'user_id' => $participant->id,
        ]);

        $participation = Participation::where([
            ['event_id', '=', $event->id],
            ['user_id', '=', $participant->id],
        ])->first();

        $response->assertCreated()
            ->assertJsonStructure([
                'error',
                'result' => [
                    'id',
                    'event_id',
                    'user_id'
                ],
            ])
            ->assertJsonFragment([
                'error' => null,
                'result' => [
                    'id' => $participation->id,
                    'event_id' => $participation->event_id,
                    'user_id' => $participation->user_id
                ],
            ]);

        // Проверка "привязки" события к участнику
        $participantEventIds = [];

        foreach ($participant->events as $participatEvent) {
            $participantEventIds[] = $participatEvent->id;
        }

        $this->assertTrue(in_array($participation->event_id, $participantEventIds));
    }

    public function test_store_action_when_user_is_member(): void
    {
        $author = User::factory()->create();
        $participant = Sanctum::actingAs(User::factory()->create());

        $event = Event::factory()
            ->state(
                ['user_id' => $author]
            )
            ->create();

        Participation::factory()
            ->state(
                [
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                ],

            )
            ->create();

        $response = $this->actingAs($participant)->postJson("/api/participate/{$event->id}");

        $response->assertUnprocessable()
            ->assertJsonStructure([
                'error' => [
                    'code',
                    'message',
                ],
                'result',
            ])
            ->assertJsonFragment([
                'error' => [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => Participation::ERROR_MESSAGE['isParticipant'],
                ],
                'result' => null,
            ]);
    }

    public function test_destroy_action_when_the_user_is_not_authorized(): void
    {
        $event = Event::factory()
            ->state(
                ['user_id' => User::factory()->create()]
            )
            ->create();

        $participation = Participation::factory()
            ->state(
                [
                    'event_id' => $event->id,
                    'user_id' => User::factory()->create()
                ],

            )
            ->create();

        // Проверка, что участие существует
        $this->assertModelExists($participation);

        $response = $this->deleteJson("/api/participate/{$event->id}");

        $response->assertUnauthorized();

        // Проверка, что участие не удалено
        $this->assertModelExists($participation);
    }

    public function test_destroy_action_when_user_is_member(): void
    {
        $author = User::factory()->create();
        $participant = Sanctum::actingAs(User::factory()->create());

        $event = Event::factory()
            ->state(
                ['user_id' => $author]
            )
            ->create();

        $participation = Participation::factory()
            ->state(
                [
                    'event_id' => $event->id,
                    'user_id' => $participant->id,
                ],
            )
            ->create();

        // Проверка, что участие существует
        $this->assertModelExists($participation);

        $response = $this->actingAs($participant)->deleteJson("/api/participate/{$event->id}");

        $response->assertNoContent();

        // Проверка, что участие удалено
        $this->assertModelMissing($participation);
    }

    public function test_destroy_action_when_user_is_not_a_member(): void
    {
        $author = User::factory()->create();
        $participant = Sanctum::actingAs(User::factory()->create());

        $event = Event::factory()
            ->state(
                ['user_id' => $author]
            )
            ->create();

        $response = $this->actingAs($participant)->deleteJson("/api/participate/{$event->id}");

        $response->assertUnprocessable()->assertJsonStructure([
            'error' => [
                'code',
                'message',
            ],
            'result',
        ])
            ->assertJsonFragment([
                'error' => [
                    'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'message' => Participation::ERROR_MESSAGE['isNotParticipant'],
                ],
                'result' => null,
            ]);
    }
}

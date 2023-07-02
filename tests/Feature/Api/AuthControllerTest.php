<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_action(): void
    {
        $reguestData = [
            'login' => fake()->name(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birthday' => fake()->date(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/register', $reguestData);

        $response->assertCreated()
            ->assertJsonStructure([
                'error',
                'result'  => [],
            ]);

        // Проверка сохранения комментария в БД
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'login' => $reguestData['login'],
            'first_name' => $reguestData['first_name'],
            'last_name' => $reguestData['last_name'],
            'birthday' => $reguestData['birthday'],
        ]);

        $user = User::where('login', $reguestData['login'])->first();

        $this->assertTrue(Hash::check($reguestData['password'], $user->password));
    }

    public function test_login_action_with_valid_data(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'login' => $user->login,
            'password' => 'password'
        ]);

        $this->assertAuthenticated();

        $response->assertOk();
    }

    public function test_login_action_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'login' => $user->login,
            'password' => 'wrong-password'
        ]);

        $this->assertGuest();

        $response->assertUnauthorized();
    }
}

<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        Event::factory(30)->hasUsers(mt_rand(2, 4))->state(new Sequence(
            fn ($sequence) => ['user_id' => User::all()->random()],
        ))->create();
    }
}

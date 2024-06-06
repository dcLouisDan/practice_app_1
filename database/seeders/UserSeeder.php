<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $numberOfUsers = 10;
        $faker = Faker::create();

        User::factory()
            ->count($numberOfUsers)
            ->state(function (array $attributes) use ($faker) {
                return [
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'username' => $faker->unique()->userName(),
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                    'profile_picture' => null
                ];
            })
            ->create();
    }
}

<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'        => $this->faker->bs(),
            'description' => $this->faker->paragraph(),
            'team_id'     => Team::factory(),
            'created_by'  => User::factory(),
        ];
    }
}
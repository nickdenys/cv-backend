<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Profile::query()->firstOrCreate(
            ['id' => 1],
            [
                'title' => 'Nick Denys',
                'bio' => 'Short bio about me...',
                'links' => [
                    'github' => 'https://www.github.com/nickdenys',
                    'linkedin' => 'https://www.linkedin.com/in/nickdenys',
                ]
            ]
        );
    }
}

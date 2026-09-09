<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Alice Smith',
                'email' => 'alice@example.com',
                'role' => 'Lead Architect',
                'status' => 'active',
            ],
            [
                'name' => 'Bob Jones',
                'email' => 'bob@example.com',
                'role' => 'Backend Developer',
                'status' => 'active',
            ],
            [
                'name' => 'Charlie Brown',
                'email' => 'charlie@example.com',
                'role' => 'UI/UX Designer',
                'status' => 'pending',
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}

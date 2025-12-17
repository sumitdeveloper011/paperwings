<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'user@paperwings.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'User',
                'last_name' => 'Singh',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 1, // Active
            ]
        );

        // Assign User role
        if (!$user->hasRole('User')) {
            $user->assignRole('User');
        }
    }
}

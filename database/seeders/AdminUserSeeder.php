<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@paperwings.com'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'status' => 1, // Active
            ]
        );

        // Assign SuperAdmin role
        if (!$admin->hasRole('SuperAdmin')) {
            $admin->assignRole('SuperAdmin');
        }
    }
}

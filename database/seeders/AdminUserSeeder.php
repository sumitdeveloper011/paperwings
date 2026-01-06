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
            ['email' => 'admin@paperwings.co.nz'],
            [
                'uuid' => Str::uuid()->toString(),
                'first_name' => 'Sheik',
                'last_name' => 'Ashaid',
                'email_verified_at' => now(),
                'password' => Hash::make('#Paperwings29waterloord'),
                'status' => 1, // Active
            ]
        );

        // Assign SuperAdmin role
        if (!$admin->hasRole('SuperAdmin')) {
            $admin->assignRole('SuperAdmin');
        }
    }
}

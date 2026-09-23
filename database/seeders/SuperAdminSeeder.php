<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@nscbiairport.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('ChangeMe!2026#Strong'),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('Super Admin');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => env('SUPER_ADMIN_USERNAME')],
            [
                'fullname' => env('SUPER_ADMIN_NAME'),
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD')),
                'role' => 'admin',
            ]
        );
    }
}
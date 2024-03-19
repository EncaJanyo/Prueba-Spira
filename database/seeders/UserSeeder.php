<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'phone' => '3001234567',
            'password' => 'password',
            'remember_token' => Str::random(30),
        ]);

        $admin->assignRole('admin');
    }
}

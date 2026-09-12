<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrátor',
            'email' => 'admin@example.com',
            'password' => Hash::make('heslo1234'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Jana Nováková',
            'position' => 'Vedoucí provozu',
            'role' => 'employee',
        ]);

        User::create([
            'name' => 'Petr Svoboda',
            'position' => 'Technik',
            'role' => 'employee',
        ]);
    }
}

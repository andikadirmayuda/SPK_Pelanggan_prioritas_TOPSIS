<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    User::create([
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin'
    ]);

    User::create([
        'name' => 'Manager',
        'email' => 'manager@example.com',
        'password' => Hash::make('password'),
        'role' => 'manager'
    ]);

    User::create([
        'name' => 'Karyawan',
        'email' => 'karyawan@example.com',
        'password' => Hash::make('password'),
        'role' => 'karyawan'
    ]);

}

}

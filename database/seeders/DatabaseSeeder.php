<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific users for roles
        $password = 'password'; // consistent password for seeded users

        User::updateOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Student User',
                'password' => $password,
                'role' => 'student',
                'id_number' => 'S1001',
                'department' => 'Computer Science',
            ]
        );

        User::updateOrCreate(
            ['email' => 'instructor@gmail.com'],
            [
                'name' => 'Instructor User',
                'password' => $password,
                'role' => 'instructor',
                'id_number' => 'I2001',
                'department' => 'Mathematics',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => $password,
                'role' => 'admin',
                'id_number' => 'A3001',
                'department' => null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'registrar@gmail.com'],
            [
                'name' => 'Registrar User',
                'password' => $password,
                'role' => 'registrar',
                'id_number' => 'R4001',
                'department' => null,
            ]
        );
    }
}

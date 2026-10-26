<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Default Admin
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'adminlms@sarasavimaga.com',
            'password' => \Illuminate\Support\Facades\Hash::make('Admin@LakmalChamara@34#'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // Default Student
        \App\Models\User::create([
            'name' => 'John Student',
            'email' => 'student@lms.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'student',
            'is_active' => true,
        ]);
        
        $this->command->info('Default Admin (admin@lms.com) and Student (student@lms.com) created.');
    }
}

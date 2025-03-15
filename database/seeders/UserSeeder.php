<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Admin user
        // User::create([
        //     'name' => 'Admin',
        //     'email' => 'admin@bengkel.com',
        //     'password' => bcrypt('password'),
        //     'role' => 'admin',
        // ]);

        // // Service User
        // User::create([
        //     'name' => 'Service',
        //     'email' => 'service@example.com',
        //     'password' => bcrypt('password123'),
        //     'role' => 'service',
        // ]);

        // Estimator user
        User::create([
            'name' => 'Estimator',
            'email' => 'estimator@bengkel.com',
            'password' => bcrypt('password'),
            'role' => 'estimator',
        ]);
    }
}

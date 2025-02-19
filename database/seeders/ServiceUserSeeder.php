<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ServiceUserSeeder extends Seeder
{
    /**
     * Jalankan database seed.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Budi',
            'email' => 'service@example.com',
            'password' => Hash::make('password123'), 
            'role' => 'service', // role dari tabel user
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

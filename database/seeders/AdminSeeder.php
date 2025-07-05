<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin dengan role admin
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@wipamotor.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);
        
        // User dengan role user untuk pengujian
        Admin::create([
            'name' => 'User Test',
            'email' => 'user@wipamotor.com',
            'password' => Hash::make('user123'),
            'role' => 'user',
        ]);
    }
}

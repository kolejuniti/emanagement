<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Aitab',
                'email' => 'aitab@gmail.com',
                'type' => 'aitab',
                'password' => '12345678'
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'type' => 'admin',
                'password' => '12345678'
            ]
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],  // Use this to find the record
                [  // Default values for creating a new model instance
                    'name' => $user['name'],
                    'type' => $user['type'],
                    'password' => Hash::make($user['password']),
                    'remember_token' => Str::random(10)
                ]
            );
        }
        
    }
}

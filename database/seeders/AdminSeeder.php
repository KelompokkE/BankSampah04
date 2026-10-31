<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('cikokol04');

        if (!$password) {
            throw new \RuntimeException(
                'ADMIN_PASSWORD belum diatur di file .env'
            );
        }

        User::updateOrCreate(
            ['username' => 'pengelola'],
            [
                'name' => 'Pengelola Bank Sampah',
                'email' => 'pengelola@banksampah.local',
                'password' => Hash::make($password),
            ]
        );
    }
}
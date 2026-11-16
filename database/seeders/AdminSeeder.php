<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            throw new RuntimeException('Isi ADMIN_EMAIL dan ADMIN_PASSWORD di .env sebelum menjalankan seeder.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => 'Admin KIMJO',
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );
    }
}

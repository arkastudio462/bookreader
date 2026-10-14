<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@perpuskita.test',
                'role' => UserRole::ADMIN,
            ],
            [
                'name' => 'Penerbit Contoh',
                'email' => 'publisher@perpuskita.test',
                'role' => UserRole::PUBLISHER,
            ],
            [
                'name' => 'Penulis Contoh',
                'email' => 'author@perpuskita.test',
                'role' => UserRole::AUTHOR,
            ],
            [
                'name' => 'Pembaca Contoh',
                'email' => 'user@perpuskita.test',
                'role' => UserRole::USER,
            ],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role']->value,
                    'status' => 'active',
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}

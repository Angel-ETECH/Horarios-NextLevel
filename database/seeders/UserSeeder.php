<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin por defecto
        User::updateOrCreate(
            ['email' => 'admin@nextlevel.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@nextlevel.com',
                'password' => Hash::make('admin123'),
            ]
        );

        // Admin de prueba
        User::updateOrCreate(
            ['email' => 'jhosep@nextlevel.com'],
            [
                'name' => 'Jhosep',
                'email' => 'jhosep@nextlevel.com',
                'password' => Hash::make('jhosep123'),
            ]
        );

        $this->command->info('✅ Usuarios creados:');
        $this->command->info('   - admin@nextlevel.com / admin123');
        $this->command->info('   - jhosep@nextlevel.com / jhosep123');
    }
}

<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@sirkulib.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@sirkulib.test',
                'password' => 'password',
                'role' => Role::Admin,
                'is_active' => true,
            ]
        );

        // Create librarian user
        $librarian = User::firstOrCreate(
            ['email' => 'librarian@sirkulib.test'],
            [
                'name' => 'Pustakawan',
                'email' => 'librarian@sirkulib.test',
                'password' => 'password',
                'role' => Role::Librarian,
                'is_active' => true,
            ]
        );

        // Create principal user
        $principal = User::firstOrCreate(
            ['email' => 'principal@sirkulib.test'],
            [
                'name' => 'Kepala Sekolah',
                'email' => 'principal@sirkulib.test',
                'password' => 'password',
                'role' => Role::Principal,
                'is_active' => true,
            ]
        );

        $this->command->info('Default users created:');
        $this->command->info('- Admin: admin@sirkulib.test (password: password)');
        $this->command->info('- Librarian: librarian@sirkulib.test (password: password)');
        $this->command->info('- Principal: principal@sirkulib.test (password: password)');
    }
}
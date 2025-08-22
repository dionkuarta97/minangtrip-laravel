<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Users;
use Illuminate\Support\Facades\Hash;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create test owner user
        Users::create([
            'username' => 'owner',
            'password' => Hash::make('owner123'),
            'role' => 'owner',
            'change_password_at' => now(),
        ]);

        // Create another test owner user
        Users::create([
            'username' => 'admin_owner',
            'password' => Hash::make('admin123'),
            'role' => 'owner',
            'change_password_at' => now(),
        ]);

        $this->command->info('Owner users seeded successfully!');
        $this->command->info('Username: owner, Password: owner123');
        $this->command->info('Username: admin_owner, Password: admin123');
    }
}
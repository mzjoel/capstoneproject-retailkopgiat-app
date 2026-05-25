<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@giatexpress.com'],
            [
                'password' => bcrypt('giatexpress@123'),
                'role_id' => 1
            ]
        );

        $admin->adminProfile()->firstOrCreate(
            ['user_id' => $admin->id],
            [
                'name' => 'Super Admin',
                'employee_id' => 'EMP2026001',
                'department' => 'Retail'
            ]
        );
    }
}

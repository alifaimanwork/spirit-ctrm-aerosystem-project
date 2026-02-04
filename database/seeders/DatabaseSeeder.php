<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'SuperAdmin Test',
            'email' => 'superadmintest@example.com',
            'staff_id' => 'S000',
            'password' => Hash::make('12345678'),
            'role' => 'superadmin',
        ]);

        // Call your DataRecordSeeder
        $this->call(DataRecordSeeder::class);
    }
}

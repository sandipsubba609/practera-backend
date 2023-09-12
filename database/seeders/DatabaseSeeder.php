<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'contact_number' => '123456789',
            'country' => 'Australia',
            'state' => 'Queensland',
            'city' => 'Random',
            'email' => 'admin@admin.com',
            'email_verified_at' => Carbon::now()->timestamp,
            'password' => Hash::make('admin123'),
            'role' => 1,
        ]);
    }
}

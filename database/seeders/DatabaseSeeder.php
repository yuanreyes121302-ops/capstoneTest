<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Create a test landlord user
        \App\Models\User::create([
            'user_id' => 'LANDLORD001',
            'first_name' => 'Test',
            'last_name' => 'Landlord',
            'email' => 'landlord@test.com',
            'password' => bcrypt('password'),
            'role' => 'landlord',
            'is_approved' => true,
            'contact_number' => '1234567890',
            'gender' => 'male',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Adminrecep;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminRecepSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUser = User::firstOrCreate(
            ['email' => 'administrator@example.com'],
            [
                'name' => 'administrator',
                'phone' => 3242343,
                'role' => 'Administrator',
                'password' => Hash::make('administrator123')
            ]
        );
        Adminrecep::firstOrCreate(
            ['user_id' => $adminUser->id],
            ['document' => 2132332]
        );
        $adminUser->assignRole('Administrator');

        $recepUser = User::firstOrCreate(
            ['email' => 'receptionist@example.com'],
            [
                'name' => 'receptionist',
                'phone' => 5463567,
                'role' => 'Receptionist',
                'password' => Hash::make('receptionist123')
            ]
        );
        Adminrecep::firstOrCreate(
            ['user_id' => $recepUser->id],
            ['document' => 435345345]
        );
        $recepUser->assignRole('Receptionist');
    }
}

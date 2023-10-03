<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResidentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = 'resident';
        $user->phone = 123123;
        $user->email = 'resident@example.com';
        $user->role = 'Resident';
        $user->password = bcrypt('resident123');
        $user->save();
        $resident = new Resident();
        $resident->tower = 30;
        $resident->apt = 402;
        $resident->status = 'Habilitado';
        $resident->user_id = $user->id;
        $resident->save();
        $user->assignRole('Resident');
    }
}

<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $resident = User::where('role', 'Resident')->first();
        if ($resident) {
            $periods = [
                ['year' => '2024', 'month' => '01'],
                ['year' => '2024', 'month' => '02'],
                ['year' => '2024', 'month' => '03'],
            ];

            foreach ($periods as $period) {
                Payment::firstOrCreate([
                    'year' => $period['year'],
                    'month' => $period['month'],
                    'user_id' => $resident->id,
                ]);
            }
        }
    }
}

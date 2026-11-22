<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::firstOrCreate(
            ['name' => 'Reserva de Toscana'],
            [
                'num_tower' => 20,
                'num_int' => 120,
                'num_apt' => 1200,
                'logo' => 'logo-complex.png',
                'principal_color' => '#23A711',
                'second_color' => '#424C40',
                'third_color' => '#306AAD',
                'fourth_color' => '#AD3630',
                'glossary' => 'Portal integral de administración, gestión de reservas y control de pagos del conjunto residencial.',
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                'title' => 'Salón Social Principal',
                'description' => 'Salón para eventos y reuniones sociales con aire acondicionado, mesas y cocineta.',
                'capacity' => 60,
                'strip' => 240, // 4 hours per slot
                'start' => '10:00:00',
                'final' => '22:00:00',
                'state' => true,
                'gallery' => '/storage/services/salon-social.png;',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => true,
            ],
            [
                'title' => 'Zona BBQ y Parrilla',
                'description' => 'Estación de asados con parrilla a carbón, pérgola campestre y lavaplatos.',
                'capacity' => 20,
                'strip' => 180, // 3 hours per slot
                'start' => '11:00:00',
                'final' => '20:00:00',
                'state' => true,
                'gallery' => '/storage/services/zona-bbq.png;',
                'monday' => false,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => true,
            ],
            [
                'title' => 'Cancha Múltiple',
                'description' => 'Cancha polideportiva sintética para fútbol 5, baloncesto y voleibol.',
                'capacity' => 14,
                'strip' => 60, // 1 hour per slot
                'start' => '07:00:00',
                'final' => '21:00:00',
                'state' => true,
                'gallery' => '/storage/services/cancha-multiple.png;',
                'monday' => true,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => true,
            ],
            [
                'title' => 'Piscina y Zonas Húmedas',
                'description' => 'Piscina para adultos y niños, sauna y turco con vestieres.',
                'capacity' => 25,
                'strip' => 120, // 2 hours per slot
                'start' => '08:00:00',
                'final' => '18:00:00',
                'state' => true,
                'gallery' => '/storage/services/piscina-humeda.png;',
                'monday' => false, // maintenance day
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => true,
            ]
        ];

        foreach ($services as $serviceData) {
            Service::firstOrCreate(
                ['title' => $serviceData['title']],
                $serviceData
            );
        }
    }
}

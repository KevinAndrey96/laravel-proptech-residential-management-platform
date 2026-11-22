<?php

namespace Tests\Feature;

use App\Models\DetailSpaceOccupation;
use App\Models\Parking;
use App\Models\ParkingSpace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ParkingTest extends TestCase
{
    use RefreshDatabase;

    protected User $watchmanUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Watchman']);
        Role::firstOrCreate(['name' => 'Administrator']);

        $this->watchmanUser = User::create([
            'name' => 'Guardia Principal',
            'email' => 'guardia@example.com',
            'phone' => '3123456789',
            'role' => 'Watchman',
            'password' => bcrypt('password123')
        ]);
        $this->watchmanUser->assignRole('Watchman');
    }

    public function test_can_create_parking_zone_and_auto_generate_spaces()
    {
        $response = $this->postJson('/api/parking-store', [
            'name' => 'Zona Visitantes Norte',
            'capacity' => 5,
            'type' => 'Visitante',
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('parkings', [
            'name' => 'Zona Visitantes Norte',
            'capacity' => 5,
            'type' => 'Visitante',
        ]);

        $parking = Parking::where('name', 'Zona Visitantes Norte')->first();
        $this->assertNotNull($parking);

        $spacesCount = ParkingSpace::where('parking_id', $parking->id)->count();
        $this->assertEquals(5, $spacesCount);
    }

    public function test_can_toggle_parking_space_status()
    {
        $parking = Parking::create([
            'name' => 'Zona Residentes Torre 1',
            'capacity' => 1,
            'type' => 'Residente',
        ]);

        $space = ParkingSpace::create([
            'parking_id' => $parking->id,
            'num' => 101,
            'status' => 0,
            'enabled' => 1,
            'type' => 'Residente',
        ]);

        $response = $this->getJson("/api/changeStatus/{$space->id}/1");
        $response->assertStatus(200);

        $this->assertEquals(1, $space->fresh()->status);
    }

    public function test_can_toggle_parking_space_enabled()
    {
        $parking = Parking::create([
            'name' => 'Zona Bahía Externa',
            'capacity' => 1,
            'type' => 'Visitante',
        ]);

        $space = ParkingSpace::create([
            'parking_id' => $parking->id,
            'num' => 1,
            'status' => 0,
            'enabled' => 1,
            'type' => 'Visitante',
        ]);

        // Disable space (e.g., under maintenance)
        $response = $this->getJson("/api/changeEnabled/{$space->id}/0");
        $response->assertStatus(200);

        $this->assertEquals(0, $space->fresh()->enabled);
    }

    public function test_can_log_detail_space_occupation()
    {
        $parking = Parking::create([
            'name' => 'Zona Visitantes Sur',
            'capacity' => 2,
            'type' => 'Visitante',
        ]);

        $space = ParkingSpace::create([
            'parking_id' => $parking->id,
            'num' => 1,
            'status' => 0,
            'enabled' => 1,
            'type' => 'Visitante',
        ]);

        $occupation = DetailSpaceOccupation::create([
            'parking_space_id' => $space->id,
            'visitant_name' => 'Andres Castro',
            'plate' => 'ABC-123',
            'apto' => 'Torre 2 Apt 401',
            'arrival_observation' => 'Ingreso de visita familiar autorizada',
        ]);

        $this->assertDatabaseHas('detail_space_occupations', [
            'id' => $occupation->id,
            'plate' => 'ABC-123',
            'visitant_name' => 'Andres Castro',
        ]);
    }
}

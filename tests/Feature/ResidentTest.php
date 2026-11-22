<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ResidentTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Superadmin']);
        Role::firstOrCreate(['name' => 'Administrator']);
        Role::firstOrCreate(['name' => 'Resident']);

        Setting::firstOrCreate(
            ['name' => 'Reserva de Toscana'],
            [
                'num_tower' => 10,
                'num_int' => 50,
                'num_apt' => 500,
                'logo' => 'logo.png',
                'principal_color' => '#23A711',
                'second_color' => '#424C40',
                'third_color' => '#306AAD',
                'fourth_color' => '#AD3630',
                'glossary' => 'Portal de administración residencial'
            ]
        );

        $this->adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '3109876543',
            'role' => 'Administrator',
            'password' => bcrypt('password123')
        ]);
        $this->adminUser->assignRole('Administrator');
    }

    public function test_administrator_can_view_residents_index()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/residents');

        $response->assertStatus(200);
    }

    public function test_administrator_can_store_new_resident()
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/residents/store', [
                'name' => 'Laura Gomez',
                'phone' => '3157891234',
                'email' => 'laura.gomez@example.com',
                'role' => 'Resident',
                'tower' => 3,
                'apt' => 204,
                'status' => 'Habilitado'
            ]);

        $response->assertRedirect('/residents');
        $response->assertSessionHas('residentSuccess');

        $this->assertDatabaseHas('users', [
            'email' => 'laura.gomez@example.com',
            'role' => 'Resident'
        ]);

        $this->assertDatabaseHas('residents', [
            'tower' => 3,
            'apt' => 204,
            'status' => 'Habilitado'
        ]);
    }

    public function test_cannot_register_duplicate_resident_in_same_apartment()
    {
        // First registration
        $this->actingAs($this->adminUser)
            ->post('/residents/store', [
                'name' => 'Pedro Perez',
                'phone' => '3111111111',
                'email' => 'pedro.perez@example.com',
                'role' => 'Resident',
                'tower' => 1,
                'apt' => 101,
                'status' => 'Habilitado'
            ]);

        // Attempting duplicate registration in tower 1 apt 101
        $response = $this->actingAs($this->adminUser)
            ->post('/residents/store', [
                'name' => 'Maria Rodriguez',
                'phone' => '3222222222',
                'email' => 'maria.rodriguez@example.com',
                'role' => 'Resident',
                'tower' => 1,
                'apt' => 101,
                'status' => 'Habilitado'
            ]);

        $response->assertRedirect('/residents');
        $response->assertSessionHas('residentFail');
    }

    public function test_can_change_resident_status()
    {
        $user = User::create([
            'name' => 'Resident To Disable',
            'email' => 'disable.me@example.com',
            'phone' => '3000000000',
            'role' => 'Resident',
            'password' => bcrypt('password123')
        ]);
        $user->assignRole('Resident');

        $resident = Resident::create([
            'user_id' => $user->id,
            'tower' => 4,
            'apt' => 301,
            'status' => 'Habilitado'
        ]);

        $response = $this->actingAs($this->adminUser)
            ->post('/changeStatusResident', [
                'id' => $user->id,
                'status' => 'Inhabilitado'
            ]);

        $response->assertStatus(302);
        $this->assertEquals('Inhabilitado', $resident->fresh()->status);
    }
}

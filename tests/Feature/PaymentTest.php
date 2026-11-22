<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Resident;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $residentUser;

    protected function setUp(): void
    {
        parent::setUp();

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
            'name' => 'Admin Finanzas',
            'email' => 'finanzas@example.com',
            'phone' => '3110001122',
            'role' => 'Administrator',
            'password' => bcrypt('password123')
        ]);
        $this->adminUser->assignRole('Administrator');

        $this->residentUser = User::create([
            'name' => 'Gloria Restrepo',
            'email' => 'gloria.restrepo@example.com',
            'phone' => '3153334455',
            'role' => 'Resident',
            'password' => bcrypt('password123')
        ]);
        $this->residentUser->assignRole('Resident');

        Resident::create([
            'user_id' => $this->residentUser->id,
            'tower' => 1,
            'apt' => 302,
            'status' => 'Habilitado'
        ]);
    }

    public function test_admin_can_record_monthly_payment_for_resident()
    {
        $response = $this->actingAs($this->adminUser)
            ->post('/payments-store', [
                'date' => '2024-04-15',
                'userID' => (string) $this->residentUser->id,
            ]);

        $response->assertRedirect('/payments');
        $response->assertSessionHas('successfulPayment');

        $this->assertDatabaseHas('payments', [
            'user_id' => $this->residentUser->id,
            'year' => '2024',
            'month' => '4',
        ]);
    }

    public function test_cannot_record_duplicate_payment_for_same_month()
    {
        // First payment
        $this->actingAs($this->adminUser)
            ->post('/payments-store', [
                'date' => '2024-05-10',
                'userID' => (string) $this->residentUser->id,
            ]);

        // Second payment attempt for the same month (May 2024)
        $response = $this->actingAs($this->adminUser)
            ->post('/payments-store', [
                'date' => '2024-05-28',
                'userID' => (string) $this->residentUser->id,
            ]);

        $response->assertRedirect('/payments');
        $response->assertSessionHas('thereIsAlreadyAPayment');

        $count = Payment::where('user_id', $this->residentUser->id)
            ->where('year', '2024')
            ->where('month', '5')
            ->count();

        $this->assertEquals(1, $count);
    }

    public function test_resident_can_view_my_payments_page()
    {
        $response = $this->actingAs($this->residentUser)
            ->get('/my-payments');

        $response->assertStatus(200);
    }
}

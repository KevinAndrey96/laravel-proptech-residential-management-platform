<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Resident;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    protected User $residentUser;
    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Resident']);
        Role::firstOrCreate(['name' => 'Administrator']);

        $this->residentUser = User::create([
            'name' => 'Carlos Mendoza',
            'email' => 'carlos.mendoza@example.com',
            'phone' => '3001234567',
            'role' => 'Resident',
            'password' => bcrypt('secret123')
        ]);
        $this->residentUser->assignRole('Resident');

        Resident::create([
            'user_id' => $this->residentUser->id,
            'tower' => 2,
            'apt' => 501,
            'status' => 'Habilitado'
        ]);

        $this->service = Service::create([
            'title' => 'Salón Social Principal',
            'description' => 'Salón para eventos sociales con aire acondicionado y cocineta.',
            'capacity' => 50,
            'strip' => 180, // 3 hours
            'start' => '10:00:00',
            'final' => '22:00:00',
            'state' => true,
            'gallery' => '/storage/services/salon.png;',
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ]);
    }

    public function test_authenticated_resident_can_view_services()
    {
        $response = $this->actingAs($this->residentUser)
            ->get('/services');

        $response->assertStatus(200);
        $response->assertSee('Salón Social Principal');
    }

    public function test_resident_can_create_a_booking()
    {
        $targetDate = Carbon::now()->addDays(5)->format('Y-m-d');

        $response = $this->actingAs($this->residentUser)
            ->post('/bookings/store', [
                'quantity' => 4,
                'date' => $targetDate,
                'hour' => '14:00',
                'service_id' => $this->service->id,
            ]);

        $response->assertRedirect('/bookings/create');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->residentUser->id,
            'service_id' => $this->service->id,
            'date' => $targetDate,
            'hour' => '14:00',
            'state' => 'Reservada',
        ]);
    }

    public function test_cancel_bookings_command_cancels_unauthorized_reservations_after_24_hours()
    {
        $booking = Booking::create([
            'user_id' => $this->residentUser->id,
            'service_id' => $this->service->id,
            'quantity' => 15,
            'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
            'day' => 'Saturday',
            'hour' => '10:00',
            'state' => 'Reservada',
        ]);

        // Simulate creation 30 hours ago
        $booking->updated_at = Carbon::now()->subHours(30);
        $booking->save(['timestamps' => false]);

        $this->artisan('booking:cancel')
            ->assertExitCode(0);

        $this->assertEquals('Cancelada', $booking->fresh()->state);
    }

    public function test_cancel_bookings_command_marks_past_authorized_booking_as_perdida()
    {
        $booking = Booking::create([
            'user_id' => $this->residentUser->id,
            'service_id' => $this->service->id,
            'quantity' => 10,
            'date' => Carbon::now()->subDays(1)->format('Y-m-d'),
            'day' => 'Yesterday',
            'hour' => '10:00',
            'state' => 'Autorizada',
        ]);

        $this->artisan('booking:cancel')
            ->assertExitCode(0);

        $this->assertEquals('Perdida', $booking->fresh()->state);
    }
}

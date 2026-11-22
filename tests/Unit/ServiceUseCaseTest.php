<?php

namespace Tests\Unit;

use App\Models\Service;
use App\UseCases\Services\DeleteServiceUseCase;
use App\UseCases\Services\StoreServiceUseCase;
use App\UseCases\Services\UpdateServiceUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ServiceUseCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_service_use_case_creates_service()
    {
        $useCase = new StoreServiceUseCase();

        $request = new Request([
            'title' => 'Zona BBQ Campestre',
            'description' => 'Área de parrilla y picnic',
            'capacity' => 25,
            'strip' => 120,
            'start' => '10:00:00',
            'final' => '20:00:00',
            'state' => 'enable',
            'monday' => 'false',
            'tuesday' => 'true',
            'wednesday' => 'true',
            'thursday' => 'true',
            'friday' => 'true',
            'saturday' => 'true',
            'sunday' => 'true',
        ]);

        $service = $useCase->handle($request);

        $this->assertInstanceOf(Service::class, $service);
        $this->assertEquals('Zona BBQ Campestre', $service->title);
        $this->assertEquals(25, $service->capacity);
        $this->assertEquals(120, $service->strip);
        $this->assertTrue((bool) $service->state);
        $this->assertFalse((bool) $service->monday);
        $this->assertTrue((bool) $service->saturday);
    }

    public function test_update_service_use_case_modifies_service()
    {
        $service = Service::create([
            'title' => 'Piscina Infantil',
            'description' => 'Piscina baja para niños',
            'capacity' => 15,
            'strip' => 60,
            'start' => '09:00:00',
            'final' => '17:00:00',
            'state' => true,
            'gallery' => '',
            'monday' => false,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ]);

        $useCase = new UpdateServiceUseCase();
        $request = new Request([
            'service_id' => $service->id,
            'title' => 'Piscina Infantil Climatizada',
            'description' => 'Piscina climatizada con salvavidas',
            'capacity' => 20,
            'strip' => 90,
            'start' => '08:00:00',
            'final' => '18:00:00',
            'state' => 'enable',
            'monday' => 'true',
            'tuesday' => 'true',
            'wednesday' => 'true',
            'thursday' => 'true',
            'friday' => 'true',
            'saturday' => 'true',
            'sunday' => 'true',
        ]);

        $updated = $useCase->handle($request);

        $this->assertNotNull($updated);
        $this->assertEquals('Piscina Infantil Climatizada', $updated->title);
        $this->assertEquals(20, $updated->capacity);
        $this->assertEquals(90, $updated->strip);
        $this->assertTrue((bool) $updated->monday);
    }

    public function test_delete_service_use_case_sets_is_deleted()
    {
        $service = Service::create([
            'title' => 'Cancha de Squash',
            'description' => 'Cancha cubierta',
            'capacity' => 4,
            'strip' => 60,
            'start' => '06:00:00',
            'final' => '21:00:00',
            'state' => true,
            'gallery' => '',
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ]);

        $useCase = new DeleteServiceUseCase();
        $request = new Request(['id' => $service->id]);

        $result = $useCase->handle($request);

        $this->assertTrue($result);
        $this->assertEquals(1, $service->fresh()->is_deleted);
    }
}

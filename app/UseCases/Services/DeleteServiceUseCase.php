<?php

namespace App\UseCases\Services;

use App\Models\Service;
use App\UseCases\Contracts\Services\DeleteServiceUseCaseInterface;
use Illuminate\Http\Request;

class DeleteServiceUseCase implements DeleteServiceUseCaseInterface
{
    public function handle(Request $request): bool
    {
        $service = Service::find($request->input('id'));
        if ($service) {
            $service->is_deleted = 1;
            $service->save();
            return true;
        }

        return false;
    }
}

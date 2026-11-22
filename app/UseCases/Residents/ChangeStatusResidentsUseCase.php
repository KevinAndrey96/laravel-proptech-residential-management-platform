<?php

namespace App\UseCases\Residents;

use App\Models\Resident;
use App\Models\User;
use App\UseCases\Contracts\Residents\ChangeStatusResidentsUseCaseInterface;
use Illuminate\Http\Request;

class ChangeStatusResidentsUseCase implements ChangeStatusResidentsUseCaseInterface
{
    public function handle(Request $request): void
    {
        $user = User::find($request->input('id'));
        if ($user) {
            $resident = Resident::where('user_id', $user->id)->first();
            if ($resident) {
                $resident->status = $request->input('status', 'Habilitado');
                $resident->save();
            }
        }
    }
}

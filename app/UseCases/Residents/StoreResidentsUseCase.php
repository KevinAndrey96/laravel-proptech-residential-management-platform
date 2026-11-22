<?php

namespace App\UseCases\Residents;

use App\Models\Resident;
use App\Models\User;
use App\UseCases\Contracts\Residents\StoreResidentsUseCaseInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Class StoreResidentsUseCase
 * @package App\UseCases\Residents
 */
class StoreResidentsUseCase implements StoreResidentsUseCaseInterface
{
    public function handle(Request $request): bool
    {
        if (User::where('email', $request->input('email'))->exists()) {
            return true;
        }

        $apartmentOccupied = Resident::where('tower', $request->input('tower'))
            ->where('apt', $request->input('apt'))
            ->exists();

        if ($apartmentOccupied) {
            return true;
        }

        $user = new User();
        $user->name = $request->input('name');
        $user->phone = $request->input('phone');
        $user->email = $request->input('email');
        $user->role = $request->input('role', 'Resident');
        $user->password = Hash::make($request->input('phone', 'secret123'));
        $user->save();

        $resident = new Resident();
        $resident->tower = $request->input('tower');
        $resident->apt = $request->input('apt');
        $resident->status = $request->input('status', 'Habilitado');
        $resident->user_id = $user->id;
        $resident->save();

        $user->assignRole('Resident');

        return false;
    }
}

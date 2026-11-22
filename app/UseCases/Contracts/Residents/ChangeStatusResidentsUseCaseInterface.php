<?php

namespace App\UseCases\Contracts\Residents;

use Illuminate\Http\Request;

interface ChangeStatusResidentsUseCaseInterface
{
    public function handle(Request $request): void;
}

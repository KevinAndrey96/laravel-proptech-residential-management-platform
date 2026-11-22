<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, int $id)
 * @method static latest()
 * @property int $parking_space_id
 * @property string $visitant_name
 * @property string $plate
 * @property string $apto
 * @property string|null $arrival_observation
 * @property string|null $departure_observation
 */
class DetailSpaceOccupation extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_space_id',
        'visitant_name',
        'plate',
        'apto',
        'arrival_observation',
        'departure_observation',
    ];

    public function parkingSpace(): BelongsTo
    {
        return $this->belongsTo(ParkingSpace::class);
    }
}

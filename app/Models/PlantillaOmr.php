<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class PlantillaOmr extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'plantillas_omr';

    protected $fillable = ['nombre', 'examen_tipo', 'definicion_json', 'activo'];

    protected function casts(): array
    {
        return [
            'definicion_json' => 'array',
            'activo' => 'boolean',
        ];
    }

    public function examenes(): HasMany
    {
        return $this->hasMany(Examen::class, 'plantilla_omr_id');
    }
}

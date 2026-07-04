<?php

namespace App\Models;

use App\Models\Concerns\LogsPortalActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

class MaterialRecomendado extends Model
{
    use HasFactory, LogsActivity, LogsPortalActivity;

    protected $table = 'materiales_recomendados';

    protected $fillable = [
        'area_id', 'nivel_desempeno_id', 'titulo', 'descripcion', 'url',
        'archivo_path', 'tipo_material', 'activo',
    ];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'area_id');
    }

    public function nivelDesempeno(): BelongsTo
    {
        return $this->belongsTo(Catalogo::class, 'nivel_desempeno_id');
    }
}

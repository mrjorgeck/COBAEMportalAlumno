<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DescargaFormato extends Model
{
    public $timestamps = false;

    protected $table = 'descargas_formato';

    protected $fillable = ['proceso_ingreso_id', 'tipo', 'usuario_id', 'ip', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function proceso(): BelongsTo
    {
        return $this->belongsTo(ProcesoIngreso::class, 'proceso_ingreso_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubKriteria extends Model
{
    use HasFactory;

    protected $table = 'sub_kriteria';
    protected $fillable = ['kriteria_id', 'nama', 'nilai', 'keterangan'];

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the kriteria that owns the sub kriteria.
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }
}
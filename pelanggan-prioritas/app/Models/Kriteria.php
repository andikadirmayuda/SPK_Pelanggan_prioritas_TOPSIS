<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kriteria extends Model
{
    use HasFactory;

    protected $table = 'kriteria';
    protected $fillable = ['nama', 'bobot', 'tipe'];

    /**
     * Get the sub kriteria for the kriteria.
     */
    public function subKriteria()
    {
        return $this->hasMany(SubKriteria::class);
    }

    /**
     * Get the route key name for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }
}

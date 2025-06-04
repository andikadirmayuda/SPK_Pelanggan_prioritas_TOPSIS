<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penilaian extends Model
{
    use HasFactory;

    protected $table = 'penilaian';
      protected $fillable = [
        'pelanggan_id',
        'kriteria_id',
        'sub_kriteria_id',
        'tahun'
    ];

    /**
     * Get the pelanggan that owns the penilaian.
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(Pelanggan::class);
    }

    /**
     * Get the kriteria that owns the penilaian.
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Get the sub kriteria that owns the penilaian.
     */
    public function subKriteria(): BelongsTo
    {
        return $this->belongsTo(SubKriteria::class);
    }
}

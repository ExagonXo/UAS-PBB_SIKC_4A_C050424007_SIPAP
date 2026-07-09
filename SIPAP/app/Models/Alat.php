<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    /** @use HasFactory<\Database\Factories\AlatFactory> */
    use HasFactory;

    protected $fillable = [
        'nama_alat',
        'merk',
        'deskripsi',
        'gambar',
        'stok',
        'status',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }
}

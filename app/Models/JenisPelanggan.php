<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPelanggan extends Model
{
    protected $table = 'jenis_pelanggans';

    protected $fillable = [
        'nama_jenis',
    ];

    public function pelanggans()
    {
        return $this->hasMany(Pelanggan::class, 'jenis_pelanggan_id');
    }
}

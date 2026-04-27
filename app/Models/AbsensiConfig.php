<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiConfig extends Model
{
    protected $table = 'absensi_configs';

    protected $fillable = [
        'jam_masuk',
        'jam_keluar',
        'latitude',
        'longitude',
        'radius',
    ];

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }
}

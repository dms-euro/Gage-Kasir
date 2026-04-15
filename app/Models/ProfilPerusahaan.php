<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfilPerusahaan extends Model
{
    protected $table = 'profil_perusahaans';

    protected $fillable = [
        'nama',
        'email',
        'telepon',
        'alamat',
        'no_rekening',
        'logo',
    ];
}

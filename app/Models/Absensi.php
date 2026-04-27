<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensis';

    protected $fillable = [
        'user_id',
        'absensi_config_id',
        'date',

        'check_in_time',
        'check_in_lat',
        'check_in_lng',
        'check_in_photo',

        'check_out_time',
        'check_out_lat',
        'check_out_lng',
        'check_out_photo',

        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function config()
    {
        return $this->belongsTo(AbsensiConfig::class);
    }
}

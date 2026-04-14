<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggans';
    protected $fillable = [
        'id_pelanggan',
        'nama',
        'cv',
        'alamat',
        'no_hp',
        'status',
        'broker',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $last = self::orderBy('id', 'desc')->first();

            if (!$last) {
                $nextNumber = 1;
            } else {
                $number = (int) substr($last->id_pelanggan, 4);
                $nextNumber = $number + 1;
            }

            $model->id_pelanggan = 'PLG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}

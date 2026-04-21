<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Saldo extends Model
{
    protected $table = 'saldo';
    protected $fillable = ['jumlah'];
    protected $casts = ['jumlah' => 'decimal:2'];

    public static function getSaldo(): float
    {
        $saldo = self::first();

        // Jika tidak ada record, buat dengan nilai 0
        if (!$saldo) {
            $saldo = self::create(['jumlah' => 0]);
        }

        return $saldo->jumlah ?? 0;
    }

    public static function add(float $nominal): void
    {
        $saldo = self::first();

        // Jika tidak ada record, buat dulu
        if (!$saldo) {
            $saldo = self::create(['jumlah' => 0]);
        }

        $saldo->jumlah += $nominal;
        $saldo->save();
    }

    public static function subtract(float $nominal): void
    {
        $saldo = self::first();

        // Jika tidak ada record, buat dulu
        if (!$saldo) {
            $saldo = self::create(['jumlah' => 0]);
        }

        $saldo->jumlah -= $nominal;
        $saldo->save();
    }
}

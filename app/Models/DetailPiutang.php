<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPiutang extends Model

{
    protected $table = 'detail_piutangs';

    protected $fillable = [
        'id_produksi',
        'cicilan_ke',
        'nominal',
        'tanggal',
        'pembayaran',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeTunai($query)
    {
        return $query->where('pembayaran', 'Tunai');
    }

    public function scopeTransfer($query)
    {
        return $query->where('pembayaran', 'Bank');
    }


    public function getFormattedNominalAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}

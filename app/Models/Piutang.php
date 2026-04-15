<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piutang extends Model
{
    protected $table = 'piutangs';

    protected $fillable = [
        'id_produksi',
        'pelanggan_id',
        'total_tagihan',
        'sisa_tagihan',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function detailPiutang()
    {
        return $this->hasMany(DetailPiutang::class, 'id_produksi', 'id_produksi');
    }

    public function scopeOutstanding($query)
    {
        return $query->where('sisa_tagihan', '>', 0);
    }

    public function scopeLunas($query)
    {
        return $query->where('sisa_tagihan', '<=', 0);
    }

    public function getTotalTerbayarAttribute(): float
    {
        return $this->total_tagihan - $this->sisa_tagihan;
    }

    public function getPersentaseTerbayarAttribute(): float
    {
        if ($this->total_tagihan == 0) {
            return 0;
        }
        return round(($this->total_terbayar / $this->total_tagihan) * 100, 2);
    }

    public function formatRupiah($number): string
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

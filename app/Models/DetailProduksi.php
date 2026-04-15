<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailProduksi extends Model
{
    protected $table = 'detail_produksis';

    protected $fillable = [
        'id_produksi',
        'kategori_id',
        'deskripsi',
        'bahan',
        'panjang',
        'lebar',
        'jumlah',
        'harga',
    ];

    protected $casts = [
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'harga' => 'decimal:2',
    ];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class, 'id_produksi', 'id_produksi');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function getLuasAttribute(): float
    {
        return $this->panjang * $this->lebar;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->luas * $this->harga * $this->jumlah;
    }

    public function getUkuranAttribute(): string
    {
        return $this->panjang . ' × ' . $this->lebar . ' m';
    }

    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}

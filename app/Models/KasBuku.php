<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasBuku extends Model
{
    protected $table = 'kas_buku';

    protected $fillable = [
        'tanggal',
        'tipe',
        'kategori',
        'nominal',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeMasuk($query)
    {
        return $query->where('tipe', 'masuk');
    }
    public function scopeKeluar($query)
    {
        return $query->where('tipe', 'keluar');
    }
    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year);
    }
    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }

    // Accessor
    public function getFormattedNominalAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }
}

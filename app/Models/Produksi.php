<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksis';

    protected $fillable = [
        'id_produksi',
        'pelanggan_id',
        'pic',
        'biaya_design',
        'diskon',
        'total_tagihan',
        'bayar',
        'pembayaran',
        'keterangan',
        'tanggal',
        'user_id',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Produksi $produksi) {
            if (!$produksi->id_produksi) {
                $produksi->id_produksi = $produksi->generateIdProduksi();
            }
            if (!$produksi->tanggal) {
                $produksi->tanggal = now();
            }
        });
    }

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detailProduksi()
    {
        return $this->hasMany(DetailProduksi::class, 'id_produksi', 'id_produksi');
    }

    public function piutang()
    {
        return $this->hasOne(Piutang::class, 'id_produksi', 'id_produksi');
    }

    public function detailPiutang()
    {
        return $this->hasMany(DetailPiutang::class, 'id_produksi', 'id_produksi');
    }

    public function generateIdProduksi(): string
    {
        $prefix = now()->format('ymd');

        $lastProduksi = self::where('id_produksi', 'like', $prefix . '%')
            ->orderBy('id_produksi', 'desc')
            ->first();

        if (!$lastProduksi) {
            return $prefix . '0001';
        }

        $lastNumber = (int) substr($lastProduksi->id_produksi, -4);
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $newNumber;
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year);
    }

    public function scopeBetweenDates($query, $start, $end)
    {
        return $query->whereBetween('tanggal', [$start, $end]);
    }


    public function scopeLunas($query)
    {
        return $query->where('keterangan', 'LUNAS');
    }

    public function scopeUtang($query)
    {
        return $query->where('keterangan', 'UTANG');
    }

    public function getSubtotalItemAttribute(): float
    {
        return $this->detailProduksi->sum('subtotal');
    }

    public function getTotalAkhirAttribute(): float
    {
        return $this->subtotal_item + $this->biaya_design - $this->diskon;
    }

    public function getSisaTagihanAttribute(): float
    {
        return $this->piutang?->sisa_tagihan ?? 0;
    }

    public function getTotalDibayarAttribute(): float
    {
        return $this->total_tagihan - $this->sisa_tagihan;
    }

    public function getCanCancelAttribute(): bool
    {
        return $this->keterangan === 'UTANG';
    }

    public function getStatusBadgeAttribute(): string
    {
        if (!$this->status) {
            return '<span class="badge bg-danger">CANCELLED</span>';
        }

        return match ($this->keterangan) {
            'LUNAS' => '<span class="badge bg-success">LUNAS</span>',
            'UTANG' => '<span class="badge bg-warning">UTANG</span>',
            default => '<span class="badge bg-secondary">-</span>',
        };
    }

    public function formatRupiah($number): string
    {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}

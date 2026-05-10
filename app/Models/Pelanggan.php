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
        'jenis_pelanggan_id',
    ];

    public function scopeBroker($query, $broker)
    {
        return $query->where('broker', $broker);
    }

    public function jenisPelanggan()
    {
        return $this->belongsTo(JenisPelanggan::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {

            $last = self::whereNotNull('id_pelanggan')
                ->orderBy('id', 'desc')
                ->first();

            if (!$last) {
                $nextNumber = 1;
            } else {
                $number = (int) str_replace('PLG-', '', $last->id_pelanggan);
                $nextNumber = $number + 1;
            }

            $model->id_pelanggan = 'PLG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }

    public function produksi()
    {
        return $this->hasMany(Produksi::class);
    }

    public function piutang()
    {
        return $this->hasMany(Piutang::class);
    }

    // App/Models/Pelanggan.php

    /**
     * Get nomor WhatsApp lengkap dengan +62
     */
    public function getNoWaAttribute(): string
    {
        $no = $this->no_hp ?? $this->cp ?? '';

        // Hapus karakter non-digit
        $no = preg_replace('/[^0-9]/', '', $no);

        // Jika dimulai dengan 0, ganti dengan 62
        if (str_starts_with($no, '0')) {
            $no = '62' . substr($no, 1);
        }

        // Jika dimulai dengan 62, sudah benar
        if (str_starts_with($no, '62')) {
            return '+' . $no;
        }

        // Jika dimulai dengan 8, tambahkan 62
        return '+62' . $no;
    }

    /**
     * Set nomor HP (simpan tanpa +62)
     */
    public function setNoHpAttribute($value)
    {
        $value = preg_replace('/[^0-9]/', '', $value);

        // Jika dimulai dengan 62, buang depannya
        if (str_starts_with($value, '62')) {
            $value = '0' . substr($value, 2);
        }

        $this->attributes['no_hp'] = $value;
    }


    // public function scopeActive($query)
    // {
    //     return $query->where('status', true);
    // }

    // public function scopeSearch($query, $term)
    // {
    //     return $query->where(function ($q) use ($term) {
    //         $q->where('nama', 'like', "%{$term}%")
    //             ->orWhere('cv', 'like', "%{$term}%")
    //             ->orWhere('id_pelanggan', 'like', "%{$term}%");
    //     });
    // }

    public function getNamaLengkapAttribute(): string
    {
        if ($this->cv) {
            return $this->nama . ' (' . $this->cv . ')';
        }
        return $this->nama;
    }

    public function getTotalOrderAttribute(): int
    {
        return $this->produksi()->count();
    }

    public function getTotalPiutangAttribute(): float
    {
        return $this->piutang()
            ->where('sisa_tagihan', '>', 0)
            ->sum('sisa_tagihan');
    }
}

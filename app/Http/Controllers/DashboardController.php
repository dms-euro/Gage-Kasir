<?php

namespace App\Http\Controllers;

use App\Models\JenisPelanggan;
use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats dasar
        $orderHariIni = Produksi::whereDate('tanggal', today())->where('status', true)->count();
        $omsetHariIni = Produksi::whereDate('tanggal', today())->where('status', true)->sum('total_tagihan');
        $totalPiutang = Piutang::where('sisa_tagihan', '>', 0)->sum('sisa_tagihan');
        $totalPelanggan = Pelanggan::where('status', true)->count();
        $omsetBulanIni = Produksi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', true)
            ->sum('total_tagihan');

        // Order terbaru
        $orderTerbaru = Produksi::with('pelanggan.jenisPelanggan')
            ->where('status', true)
            ->latest()
            ->take(3)
            ->get();

        // Pelanggan dengan piutang
        $pelangganPiutang = Pelanggan::with('jenisPelanggan')
            ->where('status', true)
            ->whereHas('piutang', fn($q) => $q->where('sisa_tagihan', '>', 0))
            ->withSum(['piutang as total_piutang' => fn($q) => $q->where('sisa_tagihan', '>', 0)], 'sisa_tagihan')
            ->orderByDesc('total_piutang')
            ->take(5)
            ->get();

        // Chart data (7 hari terakhir)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = Produksi::whereDate('tanggal', $date)->where('status', true)->sum('total_tagihan');
        }

        // Ambil semua jenis pelanggan yang aktif
        $jenisPelanggans = JenisPelanggan::get();

        // Siapkan data untuk chart per jenis pelanggan (HARIAN)
        $chartPerJenisHarian = [];
        $chartLabelsHarian = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabelsHarian[] = $date->translatedFormat('d M');

            foreach ($jenisPelanggans as $jenis) {
                $total = Produksi::whereDate('produksis.tanggal', $date)
                    ->where('produksis.status', true)
                    ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
                    ->where('pelanggans.jenis_pelanggan_id', $jenis->id)
                    ->sum('produksis.total_tagihan');

                $chartPerJenisHarian[$jenis->nama_jenis][$date->format('Y-m-d')] = $total;
            }
        }

        // Order per jenis pelanggan HARIAN (jumlah order)
        $orderPerJenisHarian = [];
        foreach ($jenisPelanggans as $jenis) {
            $orderPerJenisHarian[$jenis->nama_jenis] = Produksi::whereDate('produksis.tanggal', today())
                ->where('produksis.status', true)
                ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
                ->where('pelanggans.jenis_pelanggan_id', $jenis->id)
                ->count();
        }

        // Order per jenis pelanggan BULANAN (jumlah order)
        $orderPerJenisBulanan = [];
        foreach ($jenisPelanggans as $jenis) {
            $orderPerJenisBulanan[$jenis->nama_jenis] = Produksi::whereMonth('produksis.tanggal', now()->month)
                ->whereYear('produksis.tanggal', now()->year)
                ->where('produksis.status', true)
                ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
                ->where('pelanggans.jenis_pelanggan_id', $jenis->id)
                ->count();
        }

        // Omset per jenis pelanggan BULANAN
        $omsetPerJenisBulanan = [];
        foreach ($jenisPelanggans as $jenis) {
            $omsetPerJenisBulanan[$jenis->nama_jenis] = Produksi::whereMonth('produksis.tanggal', now()->month)
                ->whereYear('produksis.tanggal', now()->year)
                ->where('produksis.status', true)
                ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
                ->where('pelanggans.jenis_pelanggan_id', $jenis->id)
                ->sum('produksis.total_tagihan');
        }

        // Total pelanggan per jenis
        $totalPelangganPerJenis = [];
        foreach ($jenisPelanggans as $jenis) {
            $totalPelangganPerJenis[$jenis->nama_jenis] = Pelanggan::where('status', true)
                ->where('jenis_pelanggan_id', $jenis->id)
                ->count();
        }

        // Total piutang per jenis pelanggan
        $totalPiutangPerJenis = [];
        foreach ($jenisPelanggans as $jenis) {
            $totalPiutangPerJenis[$jenis->nama_jenis] = Piutang::where('sisa_tagihan', '>', 0)
                ->join('pelanggans', 'piutangs.pelanggan_id', '=', 'pelanggans.id')
                ->where('pelanggans.jenis_pelanggan_id', $jenis->id)
                ->sum('piutangs.sisa_tagihan');
        }

        return view('pages.dashboard', compact(
            // Stats dasar
            'orderHariIni',
            'omsetHariIni',
            'totalPiutang',
            'totalPelanggan',
            'omsetBulanIni',

            // Data terbaru
            'orderTerbaru',
            'pelangganPiutang',

            // Chart data
            'chartLabels',
            'chartData',
            'chartLabelsHarian',
            'chartPerJenisHarian',

            // Data per jenis pelanggan
            'jenisPelanggans',
            'orderPerJenisHarian',
            'orderPerJenisBulanan',
            'omsetPerJenisBulanan',
            'totalPelangganPerJenis',
            'totalPiutangPerJenis'
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

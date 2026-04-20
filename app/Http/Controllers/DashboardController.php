<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
        $orderTerbaru = Produksi::with('pelanggan')
            ->where('status', true)
            ->latest()
            ->take(3)
            ->get();

        // Pelanggan dengan piutang
        $pelangganPiutang = Pelanggan::where('status', true)
            ->whereHas('piutang', fn($q) => $q->where('sisa_tagihan', '>', 0))
            ->withSum(['piutang as total_piutang' => fn($q) => $q->where('sisa_tagihan', '>', 0)], 'sisa_tagihan')
            ->orderByDesc('total_piutang')
            ->take(5)
            ->get();

        // Chart data
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');
            $chartData[] = Produksi::whereDate('tanggal', $date)->where('status', true)->sum('total_tagihan');
        }

        $brokerList = ['Broker', 'Non Broker', 'Kena Pajak', 'CSR'];

        // Order per broker HARIAN - PERBAIKAN: spesifikkan tabel untuk status
        $orderPerBrokerHarian = Produksi::whereDate('produksis.tanggal', today())
            ->where('produksis.status', true)
            ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
            ->selectRaw('pelanggans.broker, COUNT(*) as total')
            ->groupBy('pelanggans.broker')
            ->pluck('total', 'broker')
            ->toArray();

        // Order per broker BULANAN - PERBAIKAN: spesifikkan tabel untuk status
        $orderPerBrokerBulanan = Produksi::whereMonth('produksis.tanggal', now()->month)
            ->whereYear('produksis.tanggal', now()->year)
            ->where('produksis.status', true)
            ->join('pelanggans', 'produksis.pelanggan_id', '=', 'pelanggans.id')
            ->selectRaw('pelanggans.broker, COUNT(*) as total')
            ->groupBy('pelanggans.broker')
            ->pluck('total', 'broker')
            ->toArray();

        return view('pages.dashboard', compact(
            'orderHariIni',
            'omsetHariIni',
            'totalPiutang',
            'totalPelanggan',
            'omsetBulanIni',
            'orderTerbaru',
            'pelangganPiutang',
            'chartLabels',
            'chartData',
            'brokerList',
            'orderPerBrokerHarian',
            'orderPerBrokerBulanan'
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

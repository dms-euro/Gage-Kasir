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
        // Stats Cards
        $orderHariIni = Produksi::whereDate('tanggal', today())
            ->where('status', true)
            ->count();

        $omsetHariIni = Produksi::whereDate('tanggal', today())
            ->where('status', true)
            ->sum('total_tagihan');

        $totalPiutang = Piutang::where('sisa_tagihan', '>', 0)->sum('sisa_tagihan');

        $totalPelanggan = Pelanggan::where('status', true)->count();

        // Omset Bulan Ini
        $omsetBulanIni = Produksi::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->where('status', true)
            ->sum('total_tagihan');

        // Order Terbaru (5)
        $orderTerbaru = Produksi::with('pelanggan')
            ->where('status', true)
            ->latest()
            ->take(5)
            ->get();

        // Pelanggan dengan Piutang
        $pelangganPiutang = Pelanggan::where('status', true)
            ->whereHas('piutang', function ($q) {
                $q->where('sisa_tagihan', '>', 0);
            })
            ->withSum(['piutang as total_piutang' => function ($q) {
                $q->where('sisa_tagihan', '>', 0);
            }], 'sisa_tagihan')
            ->orderByDesc('total_piutang')
            ->take(5)
            ->get();

        // Chart Data (7 hari terakhir)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->translatedFormat('d M');

            $omset = Produksi::whereDate('tanggal', $date)
                ->where('status', true)
                ->sum('total_tagihan');
            $chartData[] = $omset;
        }

        return view('pages.dashboard', compact(
            'orderHariIni',
            'omsetHariIni',
            'totalPiutang',
            'totalPelanggan',
            'omsetBulanIni',
            'orderTerbaru',
            'pelangganPiutang',
            'chartLabels',
            'chartData'
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

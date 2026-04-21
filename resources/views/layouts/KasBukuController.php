<?php

namespace App\Http\Controllers;

use App\Models\KasBuku;
use App\Models\ProfilPerusahaan;
use App\Models\Saldo;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasBukuController extends Controller
{
    public function index(Request $request)
    {
        $query = KasBuku::with('user')->latest('tanggal')->latest('id');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $kas = $query->paginate(15);
        $saldo = Saldo::getSaldo();
        $totalMasuk = KasBuku::masuk()->sum('nominal');
        $totalKeluar = KasBuku::keluar()->sum('nominal');
        $todayMasuk = KasBuku::today()->masuk()->sum('nominal');
        $todayKeluar = KasBuku::today()->keluar()->sum('nominal');

        return view('pages.kas-buku', compact(
            'kas',
            'saldo',
            'totalMasuk',
            'totalKeluar',
            'todayMasuk',
            'todayKeluar'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'tipe' => 'required|in:masuk,keluar',
            'kategori' => 'required|string|max:50',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $saldoSekarang = Saldo::getSaldo();

        if ($request->tipe === 'keluar' && $request->nominal > $saldoSekarang) {
            return back()->with('error', 'Saldo tidak mencukupi!')->withInput();
        }

        DB::transaction(function () use ($request) {
            KasBuku::create([
                'tanggal' => $request->tanggal,
                'tipe' => $request->tipe,
                'kategori' => $request->kategori,
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            if ($request->tipe === 'masuk') {
                Saldo::add($request->nominal);
            } else {
                Saldo::subtract($request->nominal);
            }
        });

        return back()->with('success', 'Transaksi berhasil disimpan.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $kas = KasBuku::findOrFail($id);

            if ($kas->tipe === 'masuk') {
                Saldo::subtract($kas->nominal);
            } else {
                Saldo::add($kas->nominal);
            }

            $kas->delete();
        });

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $query = KasBuku::with('user')->latest('tanggal');

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', 'like', '%' . $request->kategori . '%');
        }
        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $kas = $query->get();
        $profil = ProfilPerusahaan::first();
        $saldo = Saldo::getSaldo();

        $totalMasuk = $kas->where('tipe', 'masuk')->sum('nominal');
        $totalKeluar = $kas->where('tipe', 'keluar')->sum('nominal');

        $title = 'Laporan Kas Buku';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $title .= ' Periode ' . $request->start_date . ' s/d ' . $request->end_date;
        }

        $pdf = Pdf::loadView('pages.kas-pdf', compact('kas', 'profil', 'title', 'saldo', 'totalMasuk', 'totalKeluar'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('laporan-kas-' . date('Ymd') . '.pdf');
    }
}

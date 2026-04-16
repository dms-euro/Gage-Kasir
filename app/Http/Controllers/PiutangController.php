<?php

namespace App\Http\Controllers;

use App\Models\DetailPiutang;
use App\Models\Piutang;
use App\Models\Produksi;
use App\Models\ProfilPerusahaan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $query = Piutang::with(['produksi', 'pelanggan'])->where('sisa_tagihan', '>', 0);

        if ($request->status == 'outstanding') {
            $query->where('sisa_tagihan', '>', 0);
        } elseif ($request->status == 'lunas') {
            $query->where('sisa_tagihan', 0);
        }

        if ($request->filled('search')) {
            $query->where('id_produksi', 'like', '%' . $request->search . '%');
        }

        $piutangs = $query->latest()->paginate(10);

        $totalOutstanding = Piutang::where('sisa_tagihan', '>', 0)->sum('sisa_tagihan');
        $totalLunas = Piutang::where('sisa_tagihan', 0)->sum('total_tagihan');
        $totalCount = Piutang::where('sisa_tagihan', '>', 0)->count();

        return view('pages.piutang', compact('piutangs', 'totalOutstanding', 'totalLunas', 'totalCount'));
    }

    public function show($id_produksi)
    {
        $piutang = Piutang::where('id_produksi', $id_produksi)
            ->with([
                'produksi.detailProduksi.kategori',
                'pelanggan',
                'detailPiutang' => fn($q) => $q->latest()
            ])
            ->firstOrFail();

        return view('pages.piutang_detail', compact('piutang'));
    }

    public function bayar(Request $request, $id_produksi)
    {
        $request->validate([
            'nominal' => 'required|numeric|min:1',
            'pembayaran' => 'required|in:Tunai,Bank',
        ]);

        DB::transaction(function () use ($request, $id_produksi) {
            $piutang = Piutang::where('id_produksi', $id_produksi)->lockForUpdate()->firstOrFail();

            $nominal = min($request->nominal, $piutang->sisa_tagihan);
            $sisa = $piutang->sisa_tagihan - $nominal;

            $piutang->update(['sisa_tagihan' => $sisa]);

            $cicilanKe = $piutang->detailPiutang()->count();

            DetailPiutang::create([
                'id_produksi' => $id_produksi,
                'cicilan_ke' => $cicilanKe + 1,
                'nominal' => $nominal,
                'tanggal' => now(),
                'pembayaran' => $request->pembayaran,
            ]);

            if ($sisa == 0) {
                Produksi::where('id_produksi', $id_produksi)->update(['keterangan' => 'LUNAS']);
            }
        });

        return back()->with('success', 'Pembayaran berhasil.');
    }

    public function exportPdf(Request $request)
    {
        $query = Piutang::with(['produksi', 'pelanggan', 'detailPiutang']);

        // Filter tanggal - PERBAIKAN: gunakan whereHas untuk akses tabel produksi
        if ($request->filled('start_date')) {
            $query->whereHas('produksi', function ($q) use ($request) {
                $q->whereDate('tanggal', '>=', $request->start_date);
            });
        }
        if ($request->filled('end_date')) {
            $query->whereHas('produksi', function ($q) use ($request) {
                $q->whereDate('tanggal', '<=', $request->end_date);
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where('id_produksi', 'like', '%' . $request->search . '%');
        }

        $piutangs = $query->latest()->get();
        $profil = ProfilPerusahaan::first();

        // Summary
        $totalTagihan = $piutangs->sum('total_tagihan');
        $totalDibayar = $piutangs->sum('total_terbayar');
        $totalSisa = $piutangs->sum('sisa_tagihan');
        $totalOutstanding = $piutangs->where('sisa_tagihan', '>', 0)->count();
        $totalLunas = $piutangs->where('sisa_tagihan', 0)->count();

        // Status Text
        $statusText = 'Semua Status';

        // Title
        $title = 'Laporan Piutang';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $title .= ' Periode ' . Carbon::parse($request->start_date)->format('d M Y') . ' - ' . Carbon::parse($request->end_date)->format('d M Y');
        } elseif ($request->filled('start_date')) {
            $title .= ' dari ' . Carbon::parse($request->start_date)->format('d M Y');
        } elseif ($request->filled('end_date')) {
            $title .= ' sampai ' . Carbon::parse($request->end_date)->format('d M Y');
        }

        // PERBAIKAN: kirim $statusText ke view
        $pdf = Pdf::loadView('pages.piutang_pdf', compact(
            'piutangs',
            'profil',
            'title',
            'totalTagihan',
            'totalDibayar',
            'totalSisa',
            'totalOutstanding',
            'totalLunas',
            'statusText'  // <- Tambahkan ini
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'laporan-piutang';
        if ($request->filled('start_date')) {
            $filename .= '-' . $request->start_date;
        }
        if ($request->filled('end_date')) {
            $filename .= '-sampai-' . $request->end_date;
        }
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
}

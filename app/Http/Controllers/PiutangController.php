<?php

namespace App\Http\Controllers;

use App\Models\DetailPiutang;
use App\Models\Piutang;
use App\Models\Produksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PiutangController extends Controller
{
    public function index(Request $request)
    {
        $query = Piutang::with(['produksi', 'pelanggan']);

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
}

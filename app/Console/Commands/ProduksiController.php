<?php

namespace App\Http\Controllers;

use App\Models\DetailPiutang;
use App\Models\DetailProduksi;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use App\Models\ProfilPerusahaan;
use App\Models\JenisPelanggan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    public function kategoriIndex()
    {
        $kategoris = Kategori::latest()->paginate(25);
        return view('pages.kategori', compact('kategoris'));
    }

    public function kategoriStore(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|unique:kategoris,nama_kategori',]);
        Kategori::create($request->all());
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function kategoriUpdate(Request $request, string $id)
    {
        $request->validate(['nama_kategori' => 'required']);
        $kategori = Kategori::findOrFail($id);
        $kategori->update($request->all());
        return back()->with('success', 'Kategori berhasil diupdate');
    }

    public function kategoriDestroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function index(Request $request)
    {
        $query = Produksi::with(['pelanggan.jenisPelanggan', 'detailProduksi'])
            ->where('produksis.status', true);

        // Filter tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('produksis.tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('produksis.tanggal', '<=', $request->end_date);
        }

        // Filter jenis pelanggan (menggunakan jenis_pelanggan_id)
        if ($request->filled('jenis_pelanggan_id')) {
            $query->whereHas('pelanggan', function ($q) use ($request) {
                $q->where('jenis_pelanggan_id', $request->jenis_pelanggan_id);
            });
        }

        // Filter status (LUNAS/UTANG)
        if ($request->filled('status_filter')) {
            $query->where('produksis.keterangan', $request->status_filter);
        }

        // Search by nota
        if ($request->filled('search')) {
            $query->where('produksis.id_produksi', 'like', '%' . $request->search . '%');
        }

        $produksis = $query->latest('produksis.tanggal')
            ->latest('produksis.id_produksi')
            ->paginate(15);

        $pelanggans = Pelanggan::where('status', true)
            ->with('jenisPelanggan')
            ->orderBy('nama')
            ->get();

        // Untuk filter dropdown jenis pelanggan
        $jenisPelanggans = JenisPelanggan::orderBy('nama_jenis')->get();

        return view('pages.produksi', compact('produksis', 'pelanggans', 'jenisPelanggans'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id'
        ]);

        $pelanggan = Pelanggan::with('jenisPelanggan')->findOrFail($request->pelanggan_id);

        $produksi = new Produksi();
        $id_produksi = $produksi->generateIdProduksi();

        $detailItems = DetailProduksi::where('id_produksi', $id_produksi)
            ->with('kategori')
            ->get();

        $subtotal = $detailItems->sum('subtotal');
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.order', compact(
            'pelanggan',
            'id_produksi',
            'detailItems',
            'kategoris',
            'subtotal'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_produksi' => 'required|string|max:50',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'required|string|max:255',
            'bahan' => 'nullable|string|max:100',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
        ]);

        try {
            DetailProduksi::create([
                'id_produksi' => $request->id_produksi,
                'kategori_id' => $request->kategori_id,
                'deskripsi' => $request->deskripsi,
                'bahan' => $request->bahan,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'jumlah' => $request->jumlah,
                'harga' => $request->harga,
            ]);

            $pelangganId = $request->pelanggan_id;

            return redirect()
                ->route('produksi.create', ['pelanggan_id' => $pelangganId])
                ->with('success', 'Item berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $produksi = Produksi::where('id_produksi', $id)->firstOrFail();

        if (!$produksi->can_cancel) {
            return back()->with('error', 'Produksi tidak bisa dibatalkan karena sudah LUNAS atau memiliki cicilan.');
        }

        DB::transaction(function () use ($produksi) {
            $produksi->update([
                'status' => 0
            ]);

            if ($produksi->piutang) {
                $produksi->piutang->delete();
            }

            if ($produksi->detailPiutang->count() > 0) {
                $produksi->detailPiutang()->delete();
            }
        });

        return back()->with('success', 'Produksi berhasil dibatalkan.');
    }

    public function destroyDetail($id)
    {
        try {
            $detail = DetailProduksi::findOrFail($id);
            $pelanggan_id = request('pelanggan_id');

            $detail->delete();

            return redirect()
                ->route('produksi.create', ['pelanggan_id' => $pelanggan_id])
                ->with('success', 'Item berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus item: ' . $e->getMessage());
        }
    }

    public function editDetail($id)
    {
        $detail = DetailProduksi::with('kategori')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $detail->id,
                'kategori_id' => $detail->kategori_id,
                'deskripsi' => $detail->deskripsi,
                'bahan' => $detail->bahan,
                'panjang' => $detail->panjang,
                'lebar' => $detail->lebar,
                'jumlah' => $detail->jumlah,
                'harga' => $detail->harga,
            ]
        ]);
    }

    public function updateDetail(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategoris,id',
            'deskripsi' => 'required|string|max:255',
            'bahan' => 'nullable|string|max:100',
            'panjang' => 'required|numeric|min:0',
            'lebar' => 'required|numeric|min:0',
            'jumlah' => 'required|integer|min:1',
            'harga' => 'required|numeric|min:0',
            'pelanggan_id' => 'required|exists:pelanggans,id',
        ]);

        try {
            $detail = DetailProduksi::findOrFail($id);

            $detail->update([
                'kategori_id' => $request->kategori_id,
                'deskripsi' => $request->deskripsi,
                'bahan' => $request->bahan,
                'panjang' => $request->panjang,
                'lebar' => $request->lebar,
                'jumlah' => $request->jumlah,
                'harga' => $request->harga,
            ]);

            return redirect()
                ->route('produksi.create', ['pelanggan_id' => $request->pelanggan_id])
                ->with('success', 'Item berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui item: ' . $e->getMessage());
        }
    }

    public function finalisasi(Request $request)
    {
        $request->validate([
            'id_produksi' => 'required|string|max:50',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'pic' => 'nullable|string|max:100',
            'biaya_design' => 'nullable|numeric|min:0',
            'diskon' => 'nullable|numeric|min:0',
            'bayar' => 'required|numeric|min:0',
            'pembayaran' => 'required|in:Tunai,Bank',
        ]);

        $itemCount = DetailProduksi::where('id_produksi', $request->id_produksi)->count();
        if ($itemCount == 0) {
            return back()->with('error', 'Minimal harus ada 1 item dalam order.');
        }

        DB::beginTransaction();
        try {
            $detailItems = DetailProduksi::where('id_produksi', $request->id_produksi)->get();
            $subtotal = $detailItems->sum('subtotal');

            $biayaDesign = $request->biaya_design ?? 0;
            $diskon = $request->diskon ?? 0;
            $totalTagihan = $subtotal + $biayaDesign - $diskon;
            $bayar = $request->bayar;

            $keterangan = ($bayar >= $totalTagihan) ? 'LUNAS' : 'UTANG';
            $sisaTagihan = max(0, $totalTagihan - $bayar);

            $produksi = Produksi::create([
                'id_produksi' => $request->id_produksi,
                'pelanggan_id' => $request->pelanggan_id,
                'pic' => $request->pic,
                'biaya_design' => $biayaDesign,
                'diskon' => $diskon,
                'total_tagihan' => $totalTagihan,
                'bayar' => $bayar,
                'pembayaran' => $request->pembayaran,
                'keterangan' => $keterangan,
                'tanggal' => now(),
                'user_id' => auth()->id(),
                'status' => true,
            ]);

            DetailProduksi::where('id_produksi', $request->id_produksi)
                ->update(['id_produksi' => $produksi->id_produksi]);

            if ($keterangan === 'UTANG') {
                Piutang::create([
                    'id_produksi' => $produksi->id_produksi,
                    'pelanggan_id' => $request->pelanggan_id,
                    'total_tagihan' => $totalTagihan,
                    'sisa_tagihan' => $sisaTagihan,
                ]);
            }

            $nominalBayar = $bayar > 0 ? $bayar : $totalTagihan;
            DetailPiutang::create([
                'id_produksi' => $produksi->id_produksi,
                'cicilan_ke' => 'DP',
                'nominal' => $nominalBayar,
                'tanggal' => now(),
                'pembayaran' => $request->pembayaran,
            ]);

            DB::commit();

            session()->forget(['order_pelanggan_id', 'order_id_produksi']);

            return redirect()
                ->route('produksi.invoice', $produksi->id_produksi)
                ->with('success', 'Order berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan order: ' . $e->getMessage());
        }
    }

    public function invoice($id_produksi)
    {
        $Profilperusahaan = ProfilPerusahaan::first();

        $produksi = Produksi::where('id_produksi', $id_produksi)
            ->with([
                'pelanggan.jenisPelanggan',
                'detailProduksi.kategori',
                'piutang',
                'detailPiutang' => function ($query) {
                    $query->orderBy('tanggal', 'asc');
                },
                'user'
            ])
            ->firstOrFail();

        return view('pages.invoice', compact('Profilperusahaan', 'produksi'));
    }

    public function cetakNota($id_produksi)
    {
        $Profilperusahaan = ProfilPerusahaan::first();

        $produksi = Produksi::where('id_produksi', $id_produksi)
            ->with([
                'pelanggan.jenisPelanggan',
                'detailProduksi.kategori',
                'piutang',
                'detailPiutang' => function ($query) {
                    $query->orderBy('tanggal', 'asc');
                },
                'user'
            ])
            ->firstOrFail();

        return view('pages.cetak-nota', compact('Profilperusahaan', 'produksi'));
    }

    public function exportPdf(Request $request)
    {
        $mode = $request->get('mode', 'custom');

        // Gunakan parameter export jika ada, fallback ke parameter filter biasa
        $startDate = $request->get('export_start_date') ?? $request->get('start_date');
        $endDate = $request->get('export_end_date') ?? $request->get('end_date');
        $jenisId = $request->get('export_jenis_pelanggan_id') ?? $request->get('jenis_pelanggan_id');
        $statusFilter = $request->get('export_status_filter') ?? $request->get('status_filter');
        $search = $request->get('search');

        $query = Produksi::with(['pelanggan.jenisPelanggan', 'detailProduksi'])
            ->where('status', true)
            ->latest('tanggal')
            ->latest('id_produksi');

        // Filter tanggal
        if ($startDate && $endDate) {
            $query->whereDate('tanggal', '>=', $startDate)
                ->whereDate('tanggal', '<=', $endDate);
            $title = 'Laporan Produksi Periode ' .
                Carbon::parse($startDate)->format('d M Y') . ' - ' .
                Carbon::parse($endDate)->format('d M Y');
        } elseif ($startDate) {
            $query->whereDate('tanggal', '>=', $startDate);
            $title = 'Laporan Produksi dari ' . Carbon::parse($startDate)->format('d M Y');
        } elseif ($endDate) {
            $query->whereDate('tanggal', '<=', $endDate);
            $title = 'Laporan Produksi sampai ' . Carbon::parse($endDate)->format('d M Y');
        } else {
            $title = 'Laporan Semua Produksi';
        }

        // Filter jenis pelanggan
        if ($jenisId) {
            $query->whereHas('pelanggan', function ($q) use ($jenisId) {
                $q->where('jenis_pelanggan_id', $jenisId);
            });
            $jenisNama = JenisPelanggan::find($jenisId)?->nama_jenis;
            if ($jenisNama) $title .= ' - ' . $jenisNama;
        }

        // Filter status
        if ($statusFilter) {
            $query->where('keterangan', $statusFilter);
            $title .= ' - ' . $statusFilter;
        }

        // Search
        if ($search) {
            $query->where('id_produksi', 'like', '%' . $search . '%');
        }

        $produksis = $query->get();
        $profil = ProfilPerusahaan::first();

        $totalOmset = $produksis->sum('total_tagihan');
        $totalOrder = $produksis->count();
        $totalLunas = $produksis->where('keterangan', 'LUNAS')->count();
        $totalUtang = $produksis->where('keterangan', 'UTANG')->count();

        $pdf = Pdf::loadView('pages.produksi_pdf', compact(
            'produksis',
            'profil',
            'title',
            'totalOmset',
            'totalOrder',
            'totalLunas',
            'totalUtang'
        ));

        $pdf->setPaper('A4', 'landscape');

        $filename = 'laporan-produksi';
        if ($startDate) $filename .= '-' . $startDate;
        if ($endDate) $filename .= '-sampai-' . $endDate;
        $filename .= '.pdf';

        return $pdf->download($filename);
    }
    public function cetakNotaPdf($id_produksi)
    {
        $Profilperusahaan = ProfilPerusahaan::first();

        $produksi = Produksi::where('id_produksi', $id_produksi)
            ->with([
                'pelanggan.jenisPelanggan',
                'detailProduksi.kategori',
                'piutang',
                'detailPiutang',
                'user'
            ])
            ->firstOrFail();

        $pdf = Pdf::loadView('pages.cetak-nota-pdf', compact('Profilperusahaan', 'produksi'));
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('Nota-' . $produksi->id_produksi . '.pdf');
    }
}

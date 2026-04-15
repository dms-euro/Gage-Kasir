<?php

namespace App\Http\Controllers;

use App\Models\DetailPiutang;
use App\Models\DetailProduksi;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use App\Models\ProfilPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function kategoriIndex()
    {
        $kategoris = Kategori::latest()->paginate(25);
        return view('pages.kategori', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function kategoriStore(Request $request)
    {
        $request->validate(['nama_kategori' => 'required|unique:kategoris,nama_kategori',]);
        Kategori::create($request->all());
        return redirect()->route('kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function kategoriUpdate(Request $request, string $id)
    {
        $request->validate(['nama_kategori' => 'required']);
        $kategori = Kategori::findOrFail($id);
        $kategori->update($request->all());
        return back()->with('success', 'Kategori berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function kategoriDestroy(string $id)
    {
        $kategori = Kategori::findOrFail($id);
        $kategori->delete();
        return back()->with('success', 'Kategori berhasil dihapus');
    }

    public function index(Request $request)
    {
        $mode = $request->get('mode', 'today');

        $query = Produksi::with(['pelanggan', 'detailProduksi'])
            ->where('status', true)
            ->latest('tanggal')
            ->latest('id_produksi');

        if ($request->filled('search')) {
            $query->where('id_produksi', 'like', '%' . $request->search . '%');
        }

        $produksis = $query->paginate(15);

        $pelanggans = Pelanggan::where('status', true)
            ->orderBy('nama')
            ->get();

        return view('pages.produksi', compact('produksis', 'pelanggans', 'mode',));
    }

    public function create(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,id'
        ]);

        $pelanggan = Pelanggan::findOrFail($request->pelanggan_id);

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
            'pelanggan_id' => 'required|exists:pelanggans,id', // TAMBAHKAN VALIDASI
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

            // AMBIL DARI REQUEST, BUKAN SESSION
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
            return back()->with('error', 'Produksi tidak bisa dibatalkan');
        }

        $produksi->update([
            'status' => 0
        ]);

        return back()->with('success', 'Produksi berhasil dibatalkan');
    }

    /**
     * Hapus detail item
     */
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

    /**
     * API untuk edit item
     */
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

    /**
     * Update detail item
     */
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

    /**
     * Finalisasi order
     */
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
                'pelanggan',
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use App\Models\JenisPelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pelanggans = Pelanggan::with('jenisPelanggan')
            ->when($request->jenis_pelanggan_id, function ($q) use ($request) {
                $q->where('jenis_pelanggan_id', $request->jenis_pelanggan_id);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama', 'like', "%{$request->search}%")
                        ->orWhere('cv', 'like', "%{$request->search}%")
                        ->orWhere('no_hp', 'like', "%{$request->search}%")
                        ->orWhere('id_pelanggan', 'like', "%{$request->search}%");
                });
            })
            ->when($request->status !== null, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $jenisPelanggans = JenisPelanggan::orderBy('nama_jenis')->get();

        // Generate preview ID untuk form tambah
        $lastId = Pelanggan::max('id');
        $nextNumber = $lastId ? $lastId + 1 : 1;
        $previewId = 'PEL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('pages.pelanggan', compact('pelanggans', 'previewId', 'jenisPelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $jenisPelanggans = JenisPelanggan::get();

        $lastId = Pelanggan::max('id');
        $nextNumber = $lastId ? $lastId + 1 : 1;
        $previewId = 'PEL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        return view('pages.pelanggan-form', compact('jenisPelanggans', 'previewId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis_pelanggan_id' => 'required|exists:jenis_pelanggans,id',
            'nama' => 'required|string|max:50',
            'cv' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            // Generate ID Pelanggan otomatis
            $lastId = Pelanggan::max('id');
            $nextNumber = $lastId ? $lastId + 1 : 1;
            $idPelanggan = 'PEL-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            Pelanggan::create([
                'id_pelanggan' => $idPelanggan,
                'jenis_pelanggan_id' => $request->jenis_pelanggan_id,
                'nama' => $request->nama,
                'cv' => $request->cv ?? '',
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
                'status' => 1
            ]);

            DB::commit();

            return redirect()
                ->route('pelanggan.index')
                ->with('success', 'Pelanggan berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource (Detail Pelanggan).
     */
    public function show($id)
    {
        $pelanggan = Pelanggan::with('jenisPelanggan')->findOrFail($id);

        // Data produksi terakhir
        $lastProduksi = Produksi::where('pelanggan_id', $id)
            ->where('status', true)
            ->latest('tanggal')
            ->first();

        // Summary data
        $totalOrder = Produksi::where('pelanggan_id', $id)->where('status', true)->count();
        $totalTransaksi = Produksi::where('pelanggan_id', $id)->where('status', true)->sum('total_tagihan');
        $totalPiutang = Piutang::where('pelanggan_id', $id)->where('sisa_tagihan', '>', 0)->sum('sisa_tagihan');

        return view('pages.pelanggan_detail', compact(
            'pelanggan',
            'lastProduksi',
            'totalOrder',
            'totalTransaksi',
            'totalPiutang'
        ));
    }

    /**
     * Display produksi history for specific pelanggan.
     */
    public function produksi(Request $request, $id)
    {
        $pelanggan = Pelanggan::with('jenisPelanggan')->findOrFail($id);

        $query = Produksi::with(['detailProduksi', 'piutang'])
            ->where('pelanggan_id', $id)
            ->latest('tanggal');

        // Filter status
        if ($request->status == 'LUNAS') {
            $query->where('keterangan', 'LUNAS')->where('status', true);
        } elseif ($request->status == 'UTANG') {
            $query->where('keterangan', 'UTANG')->where('status', true);
        } elseif ($request->status == 'CANCELLED') {
            $query->where('status', false);
        } else {
            $query->where('status', true);
        }

        $produksis = $query->paginate(15);

        // Summary
        $totalOrder = Produksi::where('pelanggan_id', $id)->where('status', true)->count();
        $totalTransaksi = Produksi::where('pelanggan_id', $id)->where('status', true)->sum('total_tagihan');
        $totalPiutang = Piutang::where('pelanggan_id', $id)->where('sisa_tagihan', '>', 0)->sum('sisa_tagihan');
        $totalLunas = Produksi::where('pelanggan_id', $id)->where('status', true)->where('keterangan', 'LUNAS')->count();
        $totalUtang = Produksi::where('pelanggan_id', $id)->where('status', true)->where('keterangan', 'UTANG')->count();

        return view('pages.pelanggan_produksi', compact(
            'pelanggan',
            'produksis',
            'totalOrder',
            'totalTransaksi',
            'totalPiutang',
            'totalLunas',
            'totalUtang'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $pelanggan = Pelanggan::with('jenisPelanggan')->findOrFail($id);
        $jenisPelanggans = JenisPelanggan::get();

        return view('pages.pelanggan-form', compact('pelanggan', 'jenisPelanggans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_pelanggan_id' => 'required|exists:jenis_pelanggans,id',
            'nama' => 'required|string|max:50',
            'cv' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'no_hp' => 'required|string|max:20',
        ]);

        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $pelanggan->update([
                'jenis_pelanggan_id' => $request->jenis_pelanggan_id,
                'nama' => $request->nama,
                'cv' => $request->cv ?? '',
                'alamat' => $request->alamat,
                'no_hp' => $request->no_hp,
            ]);

            return redirect()
                ->route('pelanggan.index')
                ->with('success', 'Data pelanggan berhasil diupdate');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage (soft delete - set status 0).
     */
    public function destroy($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            // Cek apakah pelanggan memiliki transaksi aktif
            $hasActiveOrders = Produksi::where('pelanggan_id', $id)
                ->where('status', true)
                ->exists();

            if ($hasActiveOrders) {
                return back()->with('error', 'Pelanggan tidak bisa dinonaktifkan karena masih memiliki order aktif');
            }

            $pelanggan->update([
                'status' => 0
            ]);

            return back()->with('success', 'Pelanggan berhasil dinonaktifkan');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menonaktifkan pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Activate a deactivated customer.
     */
    public function activate($id)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $pelanggan->update([
                'status' => 1
            ]);

            return back()->with('success', 'Pelanggan berhasil diaktifkan kembali');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengaktifkan pelanggan: ' . $e->getMessage());
        }
    }

    /**
     * Get customers by type (for AJAX).
     */
    public function getByType(Request $request)
    {
        $request->validate([
            'jenis' => 'required|string'
        ]);

        $jenisPelanggan = JenisPelanggan::where('nama_jenis', $request->jenis)->first();

        if (!$jenisPelanggan) {
            return response()->json(['data' => []]);
        }

        $pelanggans = Pelanggan::where('jenis_pelanggan_id', $jenisPelanggan->id)
            ->where('status', 1)
            ->get(['id', 'id_pelanggan', 'nama', 'cv']);

        return response()->json(['data' => $pelanggans]);
    }
}

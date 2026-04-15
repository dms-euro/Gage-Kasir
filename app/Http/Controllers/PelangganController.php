<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Piutang;
use App\Models\Produksi;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pelanggans = Pelanggan::query()
            ->when($request->broker, function ($q) use ($request) {
                $q->broker($request->broker);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('nama', 'like', "%{$request->search}%")
                        ->orWhere('cv', 'like', "%{$request->search}%")
                        ->orWhere('no_hp', 'like', "%{$request->search}%")
                        ->orWhere('id_pelanggan', 'like', "%{$request->search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $lastId = Pelanggan::max('id');
        $nextNumber = $lastId ? $lastId + 1 : 1;

        $previewId = 'PLG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('pages.pelanggan', compact('pelanggans', 'previewId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'cv' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'broker' => 'required',
        ]);

        Pelanggan::create([

            'nama' => $request->nama,
            'cv' => $request->cv,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'broker' => $request->broker,
            'status' => 1
        ]);

        return redirect()->route('pelanggan.index', ['broker' => $request->broker])->with('success', 'Pelanggan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function produksi(Request $request, $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

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

        return view('pages.pelanggan_produksi', compact(
            'pelanggan',
            'produksis',
            'totalOrder',
            'totalTransaksi',
            'totalPiutang'
        ));
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
        $request->validate([
            'nama' => 'required',
            'cv' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'broker' => 'required',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'nama' => $request->nama,
            'cv' => $request->cv,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'broker' => $request->broker,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $pelanggan = Pelanggan::findOrFail($id);

        $pelanggan->update([
            'status' => 0
        ]);

        return back()->with('success', 'Data dihapus (nonaktif)');
    }
}

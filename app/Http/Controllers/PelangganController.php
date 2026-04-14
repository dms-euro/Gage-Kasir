<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pelanggans = Pelanggan::latest()->paginate(10);
        $last = Pelanggan::orderBy('id', 'desc')->first();

        if (!$last) {
            $nextNumber = 1;
        } else {
            $number = (int) str_replace('PLG-', '', $last->id_pelanggan);
            $nextNumber = $number + 1;
        }

        $previewId = 'PLG-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('pages.pelanggan', compact('pelanggans', 'previewId'));
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

        return back()->with('success', 'Pelanggan berhasil ditambahkan');
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

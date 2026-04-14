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
        return view('', compact('pelanggans'));
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
            'cp' => 'required',
            'broker' => 'required',
        ]);

        Pelanggan::create([
            'nama' => $request->nama,
            'cv' => $request->cv,
            'alamat' => $request->alamat,
            'cp' => $request->cp,
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
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $request->validate([
            'nama' => 'required',
            'cv' => 'required',
            'alamat' => 'required',
            'cp' => 'required',
            'broker' => 'required',
        ]);

        $pelanggan->update([
            'nama' => $request->nama,
            'cv' => $request->cv,
            'alamat' => $request->alamat,
            'cp' => $request->cp,
            'broker' => $request->broker,
        ]);

        return back()->with('success', 'Data berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        $pelanggan->update([
            'status' => 0
        ]);

        return back()->with('success', 'Data dihapus (nonaktif)');
    }
}

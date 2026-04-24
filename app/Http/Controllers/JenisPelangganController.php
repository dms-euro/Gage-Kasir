<?php

namespace App\Http\Controllers;

use App\Models\JenisPelanggan;
use Illuminate\Http\Request;

class JenisPelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jenisPelanggan = JenisPelanggan::latest()->paginate(25);
        return view('pages.pelanggan_jenis', compact('jenisPelanggan'));
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
            'nama_jenis' => 'required',
        ]);

        JenisPelanggan::create($request->all());
        return redirect()->back()->with('success', 'Kategori Pelanggan berhasil ditambahkan.');
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
            'nama_jenis' => 'required',
        ]);

        $jenisPelanggan = JenisPelanggan::findOrFail($id);
        $jenisPelanggan->update($request->all());
        return redirect()->back()->with('success', 'Kategori Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jenis = JenisPelanggan::findOrFail($id);
        $jenis->delete();
        return redirect()->back()->with('success', 'Kategori Pelanggan berhasil dihapus.');
    }
}

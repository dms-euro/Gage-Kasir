<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\ProfilPerusahaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilPerusahaanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profil = ProfilPerusahaan::first();
        return view('pages.profile-perusahaan', compact('profil'));
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $profil = ProfilPerusahaan::first();

        $validated = $request->validate([
            'nama' => 'nullable',
            'email' => 'nullable',
            'telepon' => 'nullable',
            'alamat' => 'nullable',
            'no_rekening' => 'nullable',
            'logo' => 'nullable'
        ]);

        if ($request->hasFile('logo')) {

            if ($profil->logo && Storage::exists($profil->logo)) {
                Storage::delete($profil->logo);
            }
            $path = $request->file('logo')->store('logo', 'public');
            $validated['logo'] = $path;
        }

        $profil->update($validated);

        return back()->with('success', 'Profil berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

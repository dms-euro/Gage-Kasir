<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiConfig;
use Illuminate\Http\Request;

class AbsensiConfigController extends Controller
{
    public function index()
    {
        $config = AbsensiConfig::first();

        // kalau belum ada → auto create
        if (!$config) {
            $config = AbsensiConfig::create([
                'jam_masuk' => '08:00:00',
                'jam_keluar' => '17:00:00',
                'latitude' => 0,
                'longitude' => 0,
                'radius' => 100,
            ]);
        }

        return view('pages.absensi-config', compact('config'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'jam_masuk' => 'required',
            'jam_keluar' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'radius' => 'required|integer'
        ]);

        $config = AbsensiConfig::first();

        $config->update([
            'jam_masuk' => $request->jam_masuk,
            'jam_keluar' => $request->jam_keluar,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'radius' => $request->radius,
        ]);

        return redirect()->back()->with('success', 'Config berhasil diupdate');
    }
}

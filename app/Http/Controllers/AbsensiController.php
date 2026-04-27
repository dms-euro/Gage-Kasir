<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\AbsensiConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    /* ===============================
        HALAMAN ABSEN HARI INI
    =============================== */
    public function index()
    {
        $today = now()->toDateString();

        $absensi = Absensi::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        $config = AbsensiConfig::first();

        if (!$config) {
            abort(500, 'Config absensi belum diatur');
        }

        return view('pages.absensi', compact('absensi', 'config'));
    }

    /* ===============================
        PROSES ABSEN (MASUK / PULANG)
    =============================== */
    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'photo_base64' => 'required'
        ]);

        $user = Auth::user();
        $today = now()->toDateString();
        $config = AbsensiConfig::first();

        if (!$config) {
            return back()->with('error', 'Config belum ada');
        }

        /* ===============================
            VALIDASI RADIUS
        =============================== */
        $distance = $this->distance(
            $config->latitude,
            $config->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > $config->radius) {
            return back()->with('error', 'Diluar radius!');
        }

        /* ===============================
            CEK ABSEN HARI INI
        =============================== */
        $absensi = Absensi::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        $photo = $this->saveImage($request->photo_base64);

        /* ===============================
            1. CHECK IN
        =============================== */
        if (!$absensi) {

            $status = now()->format('H:i:s') > $config->jam_masuk
                ? 'terlambat'
                : 'hadir';

            Absensi::create([
                'user_id' => $user->id,
                'absensi_config_id' => $config->id,
                'date' => $today,

                'check_in_time' => now(),
                'check_in_lat' => $request->latitude,
                'check_in_lng' => $request->longitude,
                'check_in_photo' => $photo,

                'status' => $status
            ]);

            return back()->with('success', 'Absen masuk berhasil');
        }

        /* ===============================
            2. CHECK OUT
        =============================== */
        if (!$absensi->check_out_time) {

            // opsional: validasi jam pulang
            if (now()->format('H:i:s') < $config->jam_pulang) {
                return back()->with('error', 'Belum waktunya pulang');
            }

            $absensi->update([
                'check_out_time' => now(),
                'check_out_lat' => $request->latitude,
                'check_out_lng' => $request->longitude,
                'check_out_photo' => $photo,
            ]);

            return back()->with('success', 'Absen pulang berhasil');
        }

        /* ===============================
            3. SUDAH SELESAI
        =============================== */
        return back()->with('error', 'Kamu sudah absen hari ini');
    }

    /* ===============================
        RIWAYAT USER
    =============================== */
    public function riwayat()
    {
        $data = Absensi::where('user_id', Auth::id())
            ->latest()
            ->paginate(30);

        return view('pages.absensi-riwayat', compact('data'));
    }

    /* ===============================
        SIMPAN FOTO
    =============================== */
    private function saveImage($base64)
    {
        $img = str_replace('data:image/jpeg;base64,', '', $base64);
        $img = base64_decode($img);

        // buat image dari string
        $image = imagecreatefromstring($img);

        // nama file
        $path = 'absensi/' . uniqid() . '.jpg';
        $fullPath = storage_path('app/public/' . $path);

        $quality = 70;

        do {
            ob_start();
            imagejpeg($image, null, $quality);
            $compressed = ob_get_clean();

            $sizeKB = strlen($compressed) / 1024;

            $quality -= 5;
        } while ($sizeKB > 100 && $quality > 10);

        // simpan
        file_put_contents($fullPath, $compressed);

        imagedestroy($image);

        return $path;
    }

    /* ===============================
        HITUNG JARAK (HAVERSINE)
    =============================== */
    private function distance($lat1, $lon1, $lat2, $lon2)
    {
        $earth = 6371000;

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        return $earth * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}

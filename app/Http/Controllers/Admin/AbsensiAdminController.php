<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class AbsensiAdminController extends Controller
{
    /* =========================
        REKAP (HARI & BULAN)
    ========================= */
    public function rekap(Request $request)
    {
        $mode = $request->get('mode', 'hari');
        $search = $request->get('search');

        // FILTER USER
        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })
            ->whereIn('level', ['karyawan', 'front office'])
            ->get();

        /* =========================
            MODE HARIAN
        ========================= */
        if ($mode === 'hari') {

            $date = $request->get('date', now()->toDateString());

            $data = Absensi::with('user')
                ->where('date', $date)
                ->whereIn('user_id', $users->pluck('id'))
                ->get()
                ->keyBy('user_id');

            return view('pages.absensi-rekap', [
                'mode' => 'hari',
                'date' => $date,
                'users' => $users,
                'data' => $data,
                'search' => $search
            ]);
        }

        /* =========================
            MODE BULANAN
        ========================= */
        if ($mode === 'bulan') {
            $month = $request->get('month', now()->format('Y-m'));
            $year = substr($month, 0, 4);
            $monthNum = substr($month, 5, 2);

            $absensi = Absensi::whereYear('date', $year)
                ->whereMonth('date', $monthNum)
                ->whereIn('user_id', $users->pluck('id'))
                ->get();

            // mapping: user_id -> tanggal
            $absensiMap = [];
            foreach ($absensi as $item) {
                $day = date('d', strtotime($item->date));
                $absensiMap[$item->user_id][$day] = $item;
            }

            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

            return view('pages.absensi-rekap', [
                'mode' => 'bulan',
                'month' => $month,
                'year' => $year,           // ← TAMBAHKAN INI
                'monthNum' => $monthNum,   // ← TAMBAHKAN INI
                'users' => $users,
                'absensiMap' => $absensiMap,
                'daysInMonth' => $daysInMonth,
                'search' => $search
            ]);
        }

        abort(404);
    }

    public function exportPdf(Request $request)
    {
        $mode   = $request->get('mode', 'hari');
        $search = $request->get('search');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%");
            })
            ->whereIn('level', ['karyawan', 'front office'])
            ->get();

        /* =========================
        MODE HARIAN
    ========================= */
        if ($mode === 'hari') {

            $date = $request->get('date', now()->toDateString());

            $data = Absensi::with('user')
                ->where('date', $date)
                ->whereIn('user_id', $users->pluck('id'))
                ->get()
                ->keyBy('user_id');

            $filename = 'rekap-harian-' . $date . '.pdf';

            $pdf = Pdf::loadView('pages.absensi-pdf', [
                'mode'  => 'hari',
                'date'  => $date,
                'users' => $users,
                'data'  => $data
            ])->setPaper('A4', 'landscape');

            return $pdf->download($filename);
        }

        /* =========================
        MODE BULANAN
    ========================= */
        if ($mode === 'bulan') {

            $month = $request->get('month', now()->format('Y-m'));

            $year     = substr($month, 0, 4);
            $monthNum = substr($month, 5, 2);

            $absensi = Absensi::whereYear('date', $year)
                ->whereMonth('date', $monthNum)
                ->whereIn('user_id', $users->pluck('id'))
                ->get();

            $absensiMap = [];

            foreach ($absensi as $item) {
                $day = date('d', strtotime($item->date));
                $absensiMap[$item->user_id][$day] = $item;
            }

            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $monthNum, $year);

            $filename = 'rekap-bulanan-' . $month . '.pdf';

            $pdf = Pdf::loadView('pages.absensi-pdf', [
                'mode'         => 'bulan',
                'month'        => $month,
                'users'        => $users,
                'absensiMap'   => $absensiMap,
                'daysInMonth'  => $daysInMonth
            ])->setPaper('A4', 'landscape');

            return $pdf->download($filename);
        }

        abort(404);
    }
}

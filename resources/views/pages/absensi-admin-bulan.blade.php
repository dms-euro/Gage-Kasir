{{-- resources/views/pages/absensi-admin-bulan.blade.php --}}
@extends('layouts.app')

@section('title', 'Rekap Absensi - Bulanan')

@section('content')
<div class="container mx-auto px-4 py-4 overflow-x-auto">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
        <h2 class="text-xl font-bold text-primary mb-2 sm:mb-0">Rekap Absensi Bulanan</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.absensi.rekap', ['mode' => 'hari']) }}" class="btn btn-outline-secondary text-sm">
                <i data-lucide="calendar" class="w-4 h-4 mr-1"></i> Harian
            </a>
            <a href="{{ route('admin.absensi.export-pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}" class="btn btn-danger text-sm">
                <i data-lucide="file-pdf" class="w-4 h-4 mr-1"></i> Export PDF
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-2">
        <select name="bulan" class="form-select w-32">
            @for($i = 1; $i <= 12; $i++)
                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        <select name="tahun" class="form-select w-28">
            @for($i = 2023; $i <= date('Y'); $i++)
                <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
            @endfor
        </select>
        <input type="hidden" name="mode" value="bulan">
        <button type="submit" class="btn btn-primary">Tampilkan</button>
    </form>

    {{-- Statistik --}}
    <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="bg-success/10 p-3 rounded-lg text-center">
            <div class="text-success font-bold text-xl">{{ $statistik['H'] }}</div>
            <div class="text-xs">Hadir (H)</div>
        </div>
        <div class="bg-warning/10 p-3 rounded-lg text-center">
            <div class="text-warning font-bold text-xl">{{ $statistik['T'] }}</div>
            <div class="text-xs">Terlambat (T)</div>
        </div>
        <div class="bg-danger/10 p-3 rounded-lg text-center">
            <div class="text-danger font-bold text-xl">{{ $statistik['A'] }}</div>
            <div class="text-xs">Absen (A)</div>
        </div>
    </div>

    {{-- Calendar Table --}}
    <div class="overflow-x-auto">
        <table class="table table-bordered table-sm text-center" style="min-width: 800px;">
            <thead>
                <tr>
                    <th class="sticky left-0 bg-white">Karyawan</th>
                    @foreach($dates as $date)
                        <th class="whitespace-nowrap">
                            {{ $date->format('d') }}
                            <div class="text-xs text-slate-400">{{ $date->translatedFormat('D') }}</div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($matrix as $id => $karyawan)
                <tr>
                    <td class="sticky left-0 bg-white text-left font-medium whitespace-nowrap">
                        {{ $karyawan['nama'] }}
                        <div class="text-xs text-slate-400">{{ $karyawan['level'] }}</div>
                    </td>
                    @foreach($dates as $date)
                        @php
                            $status = $karyawan['data'][$date->day] ?? 'A';
                            $bgColor = $status == 'H' ? 'bg-success/20' : ($status == 'T' ? 'bg-warning/20' : 'bg-danger/20');
                            $textColor = $status == 'H' ? 'text-success' : ($status == 'T' ? 'text-warning' : 'text-danger');
                        @endphp
                        <td class="{{ $bgColor }} font-bold {{ $textColor }}">
                            {{ $status }}
                        </td>
                    @endforeach
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.sticky {
    position: sticky;
    left: 0;
    z-index: 10;
}
@media (max-width: 768px) {
    .container {
        padding: 0;
    }
    .table td, .table th {
        padding: 6px 4px;
        font-size: 11px;
    }
}
</style>
@endsection

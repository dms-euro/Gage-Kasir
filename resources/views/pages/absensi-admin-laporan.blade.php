{{-- resources/views/pages/absensi-admin-laporan.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container mx-auto px-4 py-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
        <h2 class="text-xl font-bold text-primary mb-2 sm:mb-0">Laporan Absensi</h2>
        <a href="{{ route('admin.absensi.rekap') }}" class="btn btn-outline-secondary text-sm">
            <i data-lucide="bar-chart" class="w-4 h-4 mr-1"></i> Kembali ke Rekap
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" class="mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-3">
            <input type="date" name="start_date" class="form-control" placeholder="Dari" value="{{ request('start_date') }}">
            <input type="date" name="end_date" class="form-control" placeholder="Sampai" value="{{ request('end_date') }}">
            <select name="user_id" class="form-select">
                <option value="">Semua Karyawan</option>
                @foreach($karyawans as $k)
                    <option value="{{ $k->id }}" {{ request('user_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                @endforeach
            </select>
            <select name="status" class="form-select">
                <option value="">Semua Status</option>
                <option value="H" {{ request('status') == 'H' ? 'selected' : '' }}>Hadir (H)</option>
                <option value="T" {{ request('status') == 'T' ? 'selected' : '' }}>Terlambat (T)</option>
                <option value="A" {{ request('status') == 'A' ? 'selected' : '' }}>Absen (A)</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i data-lucide="filter" class="w-4 h-4 mr-1"></i> Filter
            </button>
            <a href="{{ route('admin.absensi.laporan') }}" class="btn btn-outline-secondary">
                <i data-lucide="refresh" class="w-4 h-4 mr-1"></i> Reset
            </a>
        </div>
    </form>

    {{-- Tabel --}}
    <div class="overflow-x-auto">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Karyawan</th>
                    <th>Level</th>
                    <th>Status</th>
                    <th>Jam Masuk</th>
                    <th>Jam Keluar</th>
                    <th>Lokasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($absensis as $a)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d/m/Y') }}</td>
                    <td>{{ $a->user->nama ?? '-' }}</td>
                    <td>{{ $a->user->level ?? '-' }}</td>
                    <td>
                        @if($a->status == 'H')
                            <span class="badge badge-success">H (Hadir)</span>
                        @elseif($a->status == 'T')
                            <span class="badge badge-warning">T (Terlambat)</span>
                        @else
                            <span class="badge badge-danger">A (Absen)</span>
                        @endif
                    </td>
                    <td>{{ $a->jam_masuk ? \Carbon\Carbon::parse($a->jam_masuk)->format('H:i:s') : '-' }}</td>
                    <td>{{ $a->jam_keluar ? \Carbon\Carbon::parse($a->jam_keluar)->format('H:i:s') : '-' }}</td>
                    <td class="max-w-xs truncate">{{ $a->lokasi_masuk ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5 text-slate-500">Tidak ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $absensis->links() }}
    </div>
</div>
@endsection

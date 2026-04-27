@extends('layouts.app')

@section('title', 'Config Absensi')

@section('content')
    <div class="grid grid-cols-12 gap-6">

        {{-- HEADER --}}
        <div class="col-span-12 mt-8">
            <div class="intro-y flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">
                    Pengaturan Absensi
                </h2>
            </div>
        </div>

        {{-- FORM --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="intro-y box p-5 mt-5">

                <form method="POST" action="{{ route('config-absensi.update') }}" id="configForm">
                    @csrf
                    @method('PUT')

                    {{-- JAM MASUK --}}
                    <div class="mb-4">
                        <label class="form-label font-medium">
                            <i data-lucide="log-in" class="w-4 h-4 inline mr-1"></i>
                            Jam Masuk
                        </label>
                        <input type="text" name="jam_masuk" id="jam_masuk" class="form-control time-input"
                            placeholder="08:00"
                            value="{{ old('jam_masuk', \Carbon\Carbon::parse($config->jam_masuk)->format('H:i')) }}"
                            required>
                        <div class="text-xs text-slate-400 mt-1">
                            Format 24 jam (contoh: 08:00, 13:30, 17:00)
                        </div>
                    </div>

                    {{-- JAM KELUAR --}}
                    <div class="mb-4">
                        <label class="form-label font-medium">
                            <i data-lucide="log-out" class="w-4 h-4 inline mr-1"></i>
                            Jam Keluar
                        </label>
                        <input type="text" name="jam_keluar" id="jam_keluar" class="form-control time-input"
                            placeholder="17:00"
                            value="{{ old('jam_keluar', \Carbon\Carbon::parse($config->jam_keluar)->format('H:i')) }}"
                            required>
                        <div class="text-xs text-slate-400 mt-1">
                            Format 24 jam (contoh: 08:00, 13:30, 17:00)
                        </div>
                    </div>

                    {{-- LATITUDE --}}
                    <div class="mb-4">
                        <label class="form-label font-medium">
                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                            Latitude
                        </label>
                        <input type="text" name="latitude" id="latitude" class="form-control"
                            value="{{ old('latitude', $config->latitude) }}" required>
                    </div>

                    {{-- LONGITUDE --}}
                    <div class="mb-4">
                        <label class="form-label font-medium">
                            <i data-lucide="map-pin" class="w-4 h-4 inline mr-1"></i>
                            Longitude
                        </label>
                        <input type="text" name="longitude" id="longitude" class="form-control"
                            value="{{ old('longitude', $config->longitude) }}" required>
                    </div>

                    {{-- RADIUS --}}
                    <div class="mb-4">
                        <label class="form-label font-medium">
                            <i data-lucide="radio" class="w-4 h-4 inline mr-1"></i>
                            Radius (meter)
                        </label>
                        <input type="number" name="radius" id="radius" class="form-control"
                            value="{{ old('radius', $config->radius) }}" required>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                            Simpan Pengaturan
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- MAP --}}
        <div class="col-span-12 lg:col-span-6">
            <div class="intro-y box p-5 mt-5">
                <h3 class="font-medium mb-3">Preview Lokasi</h3>

                <div id="map" style="height: 350px; border-radius: 10px;"></div>

                <div class="text-slate-500 text-xs mt-2">
                    <i data-lucide="mouse-pointer" class="w-3 h-3 inline mr-1"></i>
                    Klik atau geser marker untuk menentukan lokasi
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

        <script>
            // MAP FUNCTION (tetap sama seperti lama)
            let lat = {{ $config->latitude ?? -7.8 }};
            let lng = {{ $config->longitude ?? 110.36 }};

            const map = L.map('map').setView([lat, lng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            let marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            // drag marker
            marker.on('dragend', function() {
                let pos = marker.getLatLng();
                document.querySelector('[name=latitude]').value = pos.lat;
                document.querySelector('[name=longitude]').value = pos.lng;
            });

            // klik map
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                document.querySelector('[name=latitude]').value = e.latlng.lat;
                document.querySelector('[name=longitude]').value = e.latlng.lng;
            });

            // ======================
            // FORMAT TIME 24 JAM
            // ======================
            function formatTimeInput(input) {
                let value = input.value.replace(/[^0-9]/g, '');
                if (value.length >= 2) {
                    let hours = value.substring(0, 2);
                    let minutes = value.substring(2, 4);

                    // Validasi jam 00-23
                    if (parseInt(hours) > 23) hours = '23';
                    if (parseInt(hours) < 0) hours = '00';

                    // Validasi menit 00-59
                    if (minutes) {
                        if (parseInt(minutes) > 59) minutes = '59';
                        if (parseInt(minutes) < 0) minutes = '00';
                        input.value = hours + ':' + minutes;
                    } else {
                        input.value = hours;
                    }
                }
            }

            function validateTime(input) {
                let value = input.value;
                let timeRegex = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/;

                if (!timeRegex.test(value)) {
                    // Jika format salah, coba perbaiki
                    let parts = value.split(':');
                    if (parts.length === 2) {
                        let hours = parts[0].padStart(2, '0').substring(0, 2);
                        let minutes = parts[1].padStart(2, '0').substring(0, 2);

                        if (parseInt(hours) > 23) hours = '23';
                        if (parseInt(hours) < 0) hours = '00';
                        if (parseInt(minutes) > 59) minutes = '59';
                        if (parseInt(minutes) < 0) minutes = '00';

                        input.value = hours + ':' + minutes;
                    } else {
                        // Jika benar-benar salah, set ke default
                        if (input.id === 'jam_masuk') {
                            input.value = '08:00';
                        } else if (input.id === 'jam_keluar') {
                            input.value = '17:00';
                        }
                    }
                }
            }

            // Event listeners untuk input waktu
            const jamMasuk = document.getElementById('jam_masuk');
            const jamKeluar = document.getElementById('jam_keluar');

            if (jamMasuk) {
                jamMasuk.addEventListener('input', function() {
                    formatTimeInput(this);
                });
                jamMasuk.addEventListener('blur', function() {
                    validateTime(this);
                });
            }

            if (jamKeluar) {
                jamKeluar.addEventListener('input', function() {
                    formatTimeInput(this);
                });
                jamKeluar.addEventListener('blur', function() {
                    validateTime(this);
                });
            }

            // Validasi sebelum submit
            const configForm = document.getElementById('configForm');
            if (configForm) {
                configForm.addEventListener('submit', function(e) {
                    let jamMasukVal = jamMasuk.value;
                    let jamKeluarVal = jamKeluar.value;

                    let timeRegex = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/;

                    if (!timeRegex.test(jamMasukVal)) {
                        e.preventDefault();
                        alert('Format Jam Masuk salah! Gunakan format HH:MM (contoh: 08:00)');
                        jamMasuk.focus();
                        return false;
                    }

                    if (!timeRegex.test(jamKeluarVal)) {
                        e.preventDefault();
                        alert('Format Jam Keluar salah! Gunakan format HH:MM (contoh: 17:00)');
                        jamKeluar.focus();
                        return false;
                    }

                    // Validasi jam masuk < jam keluar
                    if (jamMasukVal >= jamKeluarVal) {
                        e.preventDefault();
                        alert('Jam Masuk harus lebih awal dari Jam Keluar');
                        return false;
                    }
                });
            }

            // Refresh Lucide icons
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            });
        </script>

        <style>
            .time-input {
                font-family: monospace;
                letter-spacing: 1px;
                font-size: 14px;
            }
        </style>
    @endpush
@endsection

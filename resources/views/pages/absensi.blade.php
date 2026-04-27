@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <div class="grid grid-cols-12 gap-4 md:gap-6 md:pb-0">
        {{-- HEADER JAM & STATUS --}}
        <div class="col-span-12 mt-4 md:mt-8">
            <div class="grid grid-cols-2 gap-3">
                {{-- Jam Card --}}
                <div
                    class="intro-y {{ $absensi && $absensi->check_in_time ? 'bg-primary/10' : 'bg-primary/5' }} rounded-xl p-3 border {{ $absensi && $absensi->check_in_time ? 'border-primary/30' : 'border-primary/20' }}">
                    <div class="text-center">
                        <div id="clock" class="text-2xl md:text-3xl font-bold text-primary"></div>
                        <div class="text-slate-500 text-xs mt-1">{{ \Carbon\Carbon::now()->translatedFormat('d M Y') }}</div>
                    </div>
                </div>

                {{-- Status Card --}}
                <div
                    class="intro-y rounded-xl p-3 text-center
                    {{ $absensi && $absensi->check_out_time ? 'bg-success/10 border border-success/30' : ($absensi && $absensi->check_in_time ? 'bg-warning/10 border border-warning/30' : 'bg-warning/10 border border-warning/30') }}">
                    <div class="flex items-center justify-center gap-2">
                        <i data-lucide="{{ $absensi && $absensi->check_out_time ? 'check-circle' : ($absensi && $absensi->check_in_time ? 'log-out' : 'clock') }}"
                            class="w-5 h-5
                            {{ $absensi && $absensi->check_out_time ? 'text-success' : ($absensi && $absensi->check_in_time ? 'text-warning' : 'text-warning') }}">
                        </i>
                        <span
                            class="font-semibold text-sm
                            {{ $absensi && $absensi->check_out_time ? 'text-success' : ($absensi && $absensi->check_in_time ? 'text-warning' : 'text-warning') }}">
                            {{ $absensi && $absensi->check_out_time ? 'SUDAH PULANG' : ($absensi && $absensi->check_in_time ? 'MENUNGGU PULANG' : 'BELUM ABSEN') }}
                        </span>
                    </div>
                    <div id="radiusInfo" class="text-xs font-medium mt-1"></div>
                    @if ($absensi && $absensi->check_in_time && !$absensi->check_out_time)
                        <div id="pulangInfo" class="text-xs font-medium mt-1 text-info">
                            <i data-lucide="clock" class="w-3 h-3 inline mr-1"></i>
                            Jam pulang: {{ \Carbon\Carbon::parse($config->jam_keluar)->format('H:i') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT - CAMERA SECTION --}}
        <div class="col-span-12 mt-2">
            <div class="intro-y box p-4 overflow-hidden">
                {{-- Camera Container --}}
                <div class="relative w-full bg-gradient-to-br from-slate-800 to-slate-900 rounded-xl overflow-hidden">
                    <div class="relative w-full" style="padding-top: 56.25%;">
                        <video id="camera" autoplay playsinline
                            class="absolute top-0 left-0 w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        <img id="preview" class="hidden absolute top-0 left-0 w-full h-full object-cover" />

                        {{-- Camera Frame Overlay --}}
                        <div class="absolute inset-0 pointer-events-none">
                            <div
                                class="absolute top-4 left-4 w-10 h-10 border-t-2 border-l-2 border-white/40 rounded-tl-lg">
                            </div>
                            <div
                                class="absolute top-4 right-4 w-10 h-10 border-t-2 border-r-2 border-white/40 rounded-tr-lg">
                            </div>
                            <div
                                class="absolute bottom-4 left-4 w-10 h-10 border-b-2 border-l-2 border-white/40 rounded-bl-lg">
                            </div>
                            <div
                                class="absolute bottom-4 right-4 w-10 h-10 border-b-2 border-r-2 border-white/40 rounded-br-lg">
                            </div>
                        </div>

                        {{-- Camera Guide Text --}}
                        <div class="absolute bottom-4 left-0 right-0 text-center">
                            <p
                                class="text-white/60 text-xs bg-black/50 inline-block px-3 py-1 rounded-full backdrop-blur-sm">
                                <i data-lucide="camera" class="w-3 h-3 inline mr-1"></i>
                                {{ $absensi && $absensi->check_in_time && !$absensi->check_out_time ? 'Foto untuk absen pulang' : 'Pastikan wajah terlihat jelas' }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-3 mt-5">
                    @if (!$absensi || ($absensi && !$absensi->check_in_time))
                        {{-- Belum absen masuk --}}
                        <button id="btnCapture" class="btn btn-primary flex-1 py-3 font-semibold shadow-lg">
                            <i data-lucide="camera" class="w-5 h-5 mr-2"></i> Ambil Foto Masuk
                        </button>
                        <button id="btnCancel" class="hidden btn btn-secondary flex-1 py-3 font-semibold">
                            <i data-lucide="refresh-cw" class="w-5 h-5 mr-2"></i> Ulangi
                        </button>
                        <button id="btnSubmit" class="hidden btn btn-success flex-1 py-3 font-semibold shadow-lg">
                            <i data-lucide="send" class="w-5 h-5 mr-2"></i> Kirim Absensi Masuk
                        </button>
                    @elseif($absensi && $absensi->check_in_time && !$absensi->check_out_time)
                        {{-- Sudah absen masuk, menunggu absen pulang --}}
                        <button id="btnCapturePulang" class="btn btn-warning flex-1 py-3 font-semibold shadow-lg">
                            <i data-lucide="log-out" class="w-5 h-5 mr-2"></i> Ambil Foto Pulang
                        </button>
                        <button id="btnCancelPulang" class="hidden btn btn-secondary flex-1 py-3 font-semibold">
                            <i data-lucide="refresh-cw" class="w-5 h-5 mr-2"></i> Ulangi
                        </button>
                        <button id="btnSubmitPulang" class="hidden btn btn-success flex-1 py-3 font-semibold shadow-lg">
                            <i data-lucide="send" class="w-5 h-5 mr-2"></i> Kirim Absensi Pulang
                        </button>
                    @else
                        {{-- Sudah absen lengkap --}}
                        <button disabled class="btn btn-success flex-1 py-3 font-semibold shadow-lg cursor-not-allowed">
                            <i data-lucide="check-circle" class="w-5 h-5 mr-2"></i> Absensi Lengkap Hari Ini
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- FORM HIDDEN --}}
        <form method="POST" action="{{ route('absensi.store') }}" id="formAbsen">
            @csrf
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            <input type="hidden" name="photo_base64" id="photo">
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        // ELEMENT
        let video = document.getElementById('camera');
        let canvas = document.getElementById('canvas');
        let preview = document.getElementById('preview');

        let btnCapture = document.getElementById('btnCapture');
        let btnCancel = document.getElementById('btnCancel');
        let btnSubmit = document.getElementById('btnSubmit');

        let btnCapturePulang = document.getElementById('btnCapturePulang');
        let btnCancelPulang = document.getElementById('btnCancelPulang');
        let btnSubmitPulang = document.getElementById('btnSubmitPulang');

        let latInput = document.getElementById('latitude');
        let lngInput = document.getElementById('longitude');
        let photoInput = document.getElementById('photo');

        let info = document.getElementById('radiusInfo');
        let pulangInfo = document.getElementById('pulangInfo');

        let sudahAbsenMasuk = {{ $absensi && $absensi->check_in_time ? 'true' : 'false' }};
        let sudahAbsenPulang = {{ $absensi && $absensi->check_out_time ? 'true' : 'false' }};

        /* ======================
           JAM REALTIME
        ====================== */
        function updateClock() {
            let now = new Date();
            let timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            let clockDiv = document.getElementById('clock');
            if (clockDiv) clockDiv.innerText = timeString;
        }
        updateClock();
        setInterval(updateClock, 1000);

        /* ======================
           CAMERA
        ====================== */
        if (!sudahAbsenPulang && navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "user",
                        width: {
                            ideal: 1920
                        },
                        height: {
                            ideal: 1080
                        }
                    }
                })
                .then(stream => {
                    video.srcObject = stream;
                })
                .catch(err => {
                    console.error("Camera error:", err);
                    if (info) info.innerHTML = '⚠️ Gagal akses kamera';
                });
        } else if (sudahAbsenPulang && video) {
            video.style.opacity = '0.5';
        }

        /* ======================
           GPS + VALIDASI
        ====================== */
        let officeLat = {{ $config->latitude }};
        let officeLng = {{ $config->longitude }};
        let radius = {{ $config->radius }};

        if (navigator.geolocation && !sudahAbsenPulang) {
            navigator.geolocation.watchPosition(pos => {
                let lat = pos.coords.latitude;
                let lng = pos.coords.longitude;

                latInput.value = lat;
                lngInput.value = lng;

                let distance = getDistance(lat, lng, officeLat, officeLng);
                let distanceText = Math.round(distance) + " meter";

                if (distance <= radius) {
                    if (info) {
                        info.innerHTML =
                            '<i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i> Dalam area absensi';
                        info.className = "text-success font-medium";
                    }
                    if (btnCapture) {
                        btnCapture.disabled = false;
                        btnCapture.style.opacity = "1";
                        btnCapture.style.cursor = "pointer";
                    }
                    if (btnCapturePulang) {
                        btnCapturePulang.disabled = false;
                        btnCapturePulang.style.opacity = "1";
                        btnCapturePulang.style.cursor = "pointer";
                    }
                } else {
                    if (info) {
                        info.innerHTML =
                            '<i data-lucide="x-circle" class="w-3 h-3 inline mr-1"></i> Luar area absensi';
                        info.className = "text-danger font-medium";
                    }
                    if (btnCapture) {
                        btnCapture.disabled = true;
                        btnCapture.style.opacity = "0.5";
                        btnCapture.style.cursor = "not-allowed";
                    }
                    if (btnCapturePulang) {
                        btnCapturePulang.disabled = true;
                        btnCapturePulang.style.opacity = "0.5";
                        btnCapturePulang.style.cursor = "not-allowed";
                    }
                }

                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, err => {
                console.error("GPS error:", err);
                if (info) {
                    info.innerHTML = '⚠️ Gagal mendapatkan lokasi';
                    info.className = "text-danger font-medium";
                }
            });
        } else if (sudahAbsenPulang && info) {
            info.innerHTML = '<i data-lucide="check-circle" class="w-3 h-3 inline mr-1"></i> Absensi selesai hari ini';
            info.className = "text-success font-medium";
        }

        /* ======================
           CAPTURE & SUBMIT CHECK IN
        ====================== */
        if (btnCapture && !sudahAbsenMasuk) {
            btnCapture.onclick = () => {
                const width = video.videoWidth;
                const height = video.videoHeight;

                canvas.width = width;
                canvas.height = height;

                let ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, width, height);

                let image = canvas.toDataURL('image/jpeg', 0.9);
                photoInput.value = image;

                preview.src = image;
                preview.classList.remove('hidden');
                video.classList.add('hidden');

                btnCapture.classList.add('hidden');
                btnCancel.classList.remove('hidden');
                btnSubmit.classList.remove('hidden');
            };
        }

        if (btnCancel) {
            btnCancel.onclick = () => {
                preview.classList.add('hidden');
                video.classList.remove('hidden');

                btnCapture.classList.remove('hidden');
                btnCancel.classList.add('hidden');
                btnSubmit.classList.add('hidden');

                photoInput.value = '';
            };
        }

        if (btnSubmit) {
            btnSubmit.onclick = (e) => {
                e.preventDefault();
                if (photoInput.value && photoInput.value !== '') {
                    btnSubmit.innerHTML = '<i data-lucide="loader" class="w-5 h-5 mr-2 animate-spin"></i> Mengirim...';
                    btnSubmit.disabled = true;
                    document.getElementById('formAbsen').submit();
                } else {
                    alert('Harap ambil foto terlebih dahulu');
                }
            };
        }

        /* ======================
           CAPTURE & SUBMIT CHECK OUT
        ====================== */
        if (btnCapturePulang && sudahAbsenMasuk && !sudahAbsenPulang) {
            btnCapturePulang.onclick = () => {
                const width = video.videoWidth;
                const height = video.videoHeight;

                canvas.width = width;
                canvas.height = height;

                let ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, width, height);

                let image = canvas.toDataURL('image/jpeg', 0.9);
                photoInput.value = image;

                preview.src = image;
                preview.classList.remove('hidden');
                video.classList.add('hidden');

                btnCapturePulang.classList.add('hidden');
                btnCancelPulang.classList.remove('hidden');
                btnSubmitPulang.classList.remove('hidden');
            };
        }

        if (btnCancelPulang) {
            btnCancelPulang.onclick = () => {
                preview.classList.add('hidden');
                video.classList.remove('hidden');

                btnCapturePulang.classList.remove('hidden');
                btnCancelPulang.classList.add('hidden');
                btnSubmitPulang.classList.add('hidden');

                photoInput.value = '';
            };
        }

        if (btnSubmitPulang) {
            btnSubmitPulang.onclick = (e) => {
                e.preventDefault();

                if (photoInput.value && photoInput.value !== '') {
                    btnSubmitPulang.innerHTML =
                        '<i data-lucide="loader" class="w-5 h-5 mr-2 animate-spin"></i> Mengirim...';
                    btnSubmitPulang.disabled = true;
                    document.getElementById('formAbsen').submit();
                } else {
                    alert('Harap ambil foto terlebih dahulu');
                }
            };
        }

        /* ======================
           DISTANCE FUNCTION
        ====================== */
        function getDistance(lat1, lon1, lat2, lon2) {
            let R = 6371000;
            let dLat = (lat2 - lat1) * Math.PI / 180;
            let dLon = (lon2 - lon1) * Math.PI / 180;
            let a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        }

        // Refresh Lucide icons
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });

        const observer = new MutationObserver(() => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    </script>
@endpush

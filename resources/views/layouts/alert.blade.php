{{-- ALERT MODAL SUCCESS --}}
<div id="success-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="check-circle" class="w-16 h-16 text-success mx-auto mt-3"></i>
                    <div class="text-2xl mt-5 font-medium" id="success-title">Sukses!</div>
                    <div class="text-slate-500 mt-2" id="success-message">
                        Operasi berhasil dilakukan
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-success w-24">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ALERT MODAL ERROR --}}
<div id="error-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                    <div class="text-2xl mt-5 font-medium" id="error-title">Gagal!</div>
                    <div class="text-slate-500 mt-2" id="error-message">
                        Terjadi kesalahan
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-danger w-24">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ALERT MODAL INFO --}}
<div id="info-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="info" class="w-16 h-16 text-info mx-auto mt-3"></i>
                    <div class="text-2xl mt-5 font-medium" id="info-title">Info</div>
                    <div class="text-slate-500 mt-2" id="info-message">
                        Informasi
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-info w-24">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ALERT MODAL WARNING --}}
<div id="warning-modal" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-0">
                <div class="p-5 text-center">
                    <i data-lucide="alert-triangle" class="w-16 h-16 text-warning mx-auto mt-3"></i>
                    <div class="text-2xl mt-5 font-medium" id="warning-title">Peringatan!</div>
                    <div class="text-slate-500 mt-2" id="warning-message">
                        Perhatian
                    </div>
                </div>
                <div class="px-5 pb-8 text-center">
                    <button type="button" data-tw-dismiss="modal" class="btn btn-warning w-24">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT UNTUK MENAMPILKAN MODAL DARI SESSION FLASH --}}
@if (session('success') || session('error') || session('info') || session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('success'))
                const successModal = tailwind.Modal.getOrCreateInstance(document.getElementById('success-modal'));
                document.getElementById('success-message').innerText = `{{ session('success') }}`;
                successModal.show();
            @endif

            @if (session('error'))
                const errorModal = tailwind.Modal.getOrCreateInstance(document.getElementById('error-modal'));
                document.getElementById('error-message').innerText = `{{ session('error') }}`;
                errorModal.show();
            @endif

            @if (session('info'))
                const infoModal = tailwind.Modal.getOrCreateInstance(document.getElementById('info-modal'));
                document.getElementById('info-message').innerText = `{{ session('info') }}`;
                infoModal.show();
            @endif

            @if (session('warning'))
                const warningModal = tailwind.Modal.getOrCreateInstance(document.getElementById('warning-modal'));
                document.getElementById('warning-message').innerText = `{{ session('warning') }}`;
                warningModal.show();
            @endif

            @if ($errors->any())
                const errorModal = tailwind.Modal.getOrCreateInstance(document.getElementById('error-modal'));
                let errorMsg = '';
                @foreach ($errors->all() as $error)
                    errorMsg += '{{ $error }}\n';
                @endforeach
                document.getElementById('error-message').innerText = errorMsg;
                errorModal.show();
            @endif
        });
    </script>
@endif

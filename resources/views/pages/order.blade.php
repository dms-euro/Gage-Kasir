@extends('layouts.app')

@section('title', 'Produksi Order')

@section('content')
    <div class="intro-y flex flex-col sm:flex-row items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">
            Produksi Order
        </h2>
        <div class="w-full sm:w-auto flex mt-4 sm:mt-0">
            <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary shadow-md mr-2">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <div class="intro-y grid grid-cols-12 gap-5 mt-5">
        <div class="intro-y col-span-12 lg:col-span-8">

            {{-- INFO PELANGGAN --}}
            <div class="box p-5 mb-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-5">
                    <h3 class="font-medium text-base mr-auto">
                        <i data-lucide="user" class="w-4 h-4 mr-2 inline"></i>
                        Informasi Pelanggan
                    </h3>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <div class="text-slate-500 text-xs mb-1">ID Pelanggan</div>
                        <div class="font-semibold text-sm">{{ $pelanggan->id_pelanggan }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-xs mb-1">Tipe</div>
                        <div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium
                                {{ $pelanggan->broker == 'Broker' ? 'bg-primary/10 text-primary' : '' }}
                                {{ $pelanggan->broker == 'Non Broker' ? 'bg-slate-100 text-slate-600' : '' }}
                                {{ $pelanggan->broker == 'Pajak' ? 'bg-warning/10 text-warning' : '' }}">
                                {{ $pelanggan->broker }}
                            </span>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-slate-500 text-xs mb-1">Nama / CV</div>
                        <div class="font-semibold">{{ $pelanggan->nama_lengkap }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-xs mb-1">Contact Person</div>
                        <div class="font-medium">{{ $pelanggan->no_hp ?? ($pelanggan->cp ?? '-') }}</div>
                    </div>
                    <div>
                        <div class="text-slate-500 text-xs mb-1">Alamat</div>
                        <div class="font-medium leading-relaxed">{{ $pelanggan->alamat ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- HEADER ITEM --}}
            <div class="flex items-center mb-4">
                <div class="mr-auto">
                    <span class="text-slate-500 text-xs">No. Produksi</span>
                    <h3 class="text-xl font-bold text-primary">{{ $id_produksi }}</h3>
                </div>
                <button data-tw-toggle="modal" data-tw-target="#add-item-modal" class="btn btn-primary shadow-md">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i> Tambah Item
                </button>
            </div>

            {{-- TABEL ITEM --}}
            <div class="box overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table--sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-darkmode-800">
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap">#</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap">Deskripsi</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap">Kategori</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap">Bahan</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap text-right">Ukuran (m)</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap text-center">Jml</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap text-right">Harga/m²</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap text-right">Subtotal</th>
                                <th class="border-b dark:border-darkmode-400 whitespace-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($detailItems as $item)
                                <tr class="hover:bg-slate-50 dark:hover:bg-darkmode-700">
                                    <td class="border-b dark:border-darkmode-400">{{ $loop->iteration }}</td>
                                    <td class="border-b dark:border-darkmode-400 font-medium">{{ $item->deskripsi }}</td>
                                    <td class="border-b dark:border-darkmode-400">
                                        {{ $item->kategori->nama_kategori ?? '-' }}</td>
                                    <td class="border-b dark:border-darkmode-400">{{ $item->bahan ?? '-' }}</td>
                                    <td class="border-b dark:border-darkmode-400 text-right">
                                        {{ number_format($item->panjang, 2, ',', '.') }} x
                                        {{ number_format($item->lebar, 2, ',', '.') }}
                                    </td>
                                    <td class="border-b dark:border-darkmode-400 text-center">{{ $item->jumlah }}</td>
                                    <td class="border-b dark:border-darkmode-400 text-right">
                                        {{ number_format($item->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="border-b dark:border-darkmode-400 text-right font-medium">
                                        {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                    <td class="border-b dark:border-darkmode-400 text-center">
                                        <div class="flex justify-center items-center gap-1">
                                            <button onclick="editItem({{ $item->id }})"
                                                class="btn btn-sm btn-outline-secondary p-1" title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('produksi.detail.destroy', $item->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger p-1"
                                                    onclick="return confirm('Hapus item ini?')" title="Hapus">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-slate-500">
                                        <i data-lucide="shopping-cart" class="w-10 h-10 mx-auto mb-2 text-slate-400"></i>
                                        Belum ada item. Klik "Tambah Item" untuk memulai.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 dark:bg-darkmode-800">
                                <td colspan="7" class="border-t dark:border-darkmode-400 text-right font-medium">
                                    SUBTOTAL
                                </td>
                                <td class="border-t dark:border-darkmode-400 text-right font-bold text-primary">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </td>
                                <td class="border-t dark:border-darkmode-400"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: FINALISASI ORDER --}}
        <div class="col-span-12 lg:col-span-4">
            <div class="box p-5">
                <div class="flex items-center border-b border-slate-200/60 dark:border-darkmode-400 pb-3 mb-4">
                    <h3 class="font-medium text-base">
                        <i data-lucide="credit-card" class="w-4 h-4 mr-2 inline"></i>
                        Finalisasi Order
                    </h3>
                </div>

                <form action="{{ route('produksi.finalisasi') }}" method="POST" id="finalisasi-form">
                    @csrf
                    <input type="hidden" name="id_produksi" value="{{ $id_produksi }}">
                    <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">

                    {{-- PIC --}}
                    <div class="mb-4">
                        <label for="pic" class="form-label">PIC (Contact Person)</label>
                        <input type="text" id="pic" name="pic" class="form-control"
                            placeholder="Nama PIC dari pelanggan"
                            value="{{ auth()->user()->nama ?? auth()->user()->username }}" readonly>
                    </div>

                    {{-- Biaya Design --}}
                    <div class="mb-4">
                        <label for="biaya_design" class="form-label">Biaya Design</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-100">Rp</span>
                            <input type="number" id="biaya_design" name="biaya_design" class="form-control"
                                placeholder="0" value="{{ old('biaya_design', 0) }}" min="0" step="1000">
                        </div>
                    </div>

                    {{-- Diskon --}}
                    <div class="mb-4">
                        <label for="diskon" class="form-label">Diskon</label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-100">Rp</span>
                            <input type="number" id="diskon" name="diskon" class="form-control" placeholder="0"
                                value="{{ old('diskon', 0) }}" min="0" step="1000">
                        </div>
                    </div>

                    {{-- Ringkasan Biaya --}}
                    <div class="bg-slate-50 dark:bg-darkmode-700 p-4 rounded-md mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-slate-500">Subtotal Item</span>
                            <span class="font-medium" id="display-subtotal">Rp
                                {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-slate-500">Biaya Design</span>
                            <span class="font-medium" id="display-biaya">Rp 0</span>
                        </div>
                        <div class="flex justify-between mb-2 text-danger">
                            <span class="text-slate-500">Diskon</span>
                            <span class="font-medium" id="display-diskon">- Rp 0</span>
                        </div>
                        <div class="border-t border-slate-200 dark:border-darkmode-400 my-2"></div>
                        <div class="flex justify-between">
                            <span class="font-medium text-base">TOTAL TAGIHAN</span>
                            <span class="font-bold text-primary text-lg" id="display-total">
                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Pembayaran --}}
                    <div class="mb-4">
                        <label for="bayar" class="form-label">
                            Bayar <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-slate-100">Rp</span>
                            <input type="number" id="bayar" name="bayar" class="form-control"
                                placeholder="Jumlah bayar" value="{{ old('bayar', 0) }}" min="0" step="1000"
                                required>
                        </div>
                    </div>

                    {{-- Metode Pembayaran --}}
                    <div class="mb-4">
                        <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <div class="flex gap-4 mt-2">
                            <div class="flex items-center">
                                <input type="radio" id="tunai" name="pembayaran" value="Tunai"
                                    class="form-radio" checked>
                                <label for="tunai" class="ml-2 cursor-pointer">Tunai</label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" id="bank" name="pembayaran" value="Bank"
                                    class="form-radio">
                                <label for="bank" class="ml-2 cursor-pointer">Transfer Bank</label>
                            </div>
                        </div>
                    </div>

                    {{-- Sisa Tagihan --}}
                    <div class="bg-warning/10 dark:bg-warning/20 p-4 rounded-md mb-5">
                        <div class="flex justify-between items-center">
                            <span class="text-warning font-medium">SISA TAGIHAN</span>
                            <span class="font-bold text-warning text-lg" id="display-sisa">Rp 0</span>
                        </div>
                        <small class="text-slate-500 block mt-1">
                            <i data-lucide="info" class="w-3 h-3 inline mr-1"></i>
                            Sisa > 0 akan masuk ke piutang dan bisa dicicil nanti
                        </small>
                    </div>

                    {{-- Tombol Simpan --}}
                    <button type="submit" class="btn btn-primary w-full py-3 text-base shadow-md" id="btn-simpan">
                        <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                        Simpan Order & Cetak Invoice
                    </button>
                </form>
            </div>

            {{-- NOTE --}}
            <div class="box p-5 mt-5 bg-slate-50 dark:bg-darkmode-700">
                <div class="flex items-start">
                    <i data-lucide="info" class="w-5 h-5 text-primary mr-2 mt-0.5"></i>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            <strong>Catatan:</strong><br>
                            • Minimal 1 item dalam order<br>
                            • Sisa tagihan akan otomatis tercatat sebagai piutang<br>
                            • Invoice akan langsung bisa dicetak setelah simpan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TAMBAH ITEM --}}
    <div id="add-item-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('produksi.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id_produksi" value="{{ $id_produksi }}">
                    <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">

                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">
                            <i data-lucide="plus-circle" class="w-5 h-5 mr-2 inline text-primary"></i>
                            Tambah Item Order
                        </h2>
                        <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="modal-body grid grid-cols-12 gap-4">
                        {{-- Deskripsi --}}
                        <div class="col-span-12">
                            <label for="deskripsi" class="form-label">Deskripsi <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="deskripsi" name="deskripsi" class="form-control"
                                placeholder="Contoh: Banner Depan, Stiker Logo" required>
                        </div>

                        {{-- Kategori --}}
                        <div class="col-span-12 md:col-span-6">
                            <label for="kategori_id" class="form-label">Kategori Produksi <span
                                    class="text-danger">*</span></label>
                            <select id="kategori_id" name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bahan --}}
                        <div class="col-span-12 md:col-span-6">
                            <label for="bahan" class="form-label">Bahan</label>
                            <input type="text" id="bahan" name="bahan" class="form-control"
                                placeholder="Contoh: Albatros, Vinyl, Art Paper">
                        </div>

                        {{-- Panjang --}}
                        <div class="col-span-4">
                            <label for="panjang" class="form-label">Panjang (m) <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="panjang" name="panjang" class="form-control" placeholder="0"
                                step="0.01" min="0" required>
                        </div>

                        {{-- Lebar --}}
                        <div class="col-span-4">
                            <label for="lebar" class="form-label">Lebar (m) <span class="text-danger">*</span></label>
                            <input type="number" id="lebar" name="lebar" class="form-control" placeholder="0"
                                step="0.01" min="0" required>
                        </div>

                        {{-- Jumlah --}}
                        <div class="col-span-4">
                            <label for="jumlah" class="form-label">Jumlah <span class="text-danger">*</span></label>
                            <input type="number" id="jumlah" name="jumlah" class="form-control" placeholder="1"
                                value="1" min="1" required>
                        </div>

                        {{-- Harga --}}
                        <div class="col-span-12">
                            <label for="harga" class="form-label">Harga per m² <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100">Rp</span>
                                <input type="number" id="harga" name="harga" class="form-control"
                                    placeholder="0" min="0" required>
                            </div>
                        </div>

                        {{-- Preview Subtotal --}}
                        <div class="col-span-12">
                            <div class="bg-slate-100 dark:bg-darkmode-600 p-4 rounded-md">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 dark:text-slate-300">Subtotal Item</span>
                                    <span class="text-xl font-bold text-primary" id="modal-subtotal">Rp 0</span>
                                </div>
                                <small class="text-slate-500 block mt-1">
                                    Rumus: Panjang × Lebar × Harga × Jumlah
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24">Batal</button>
                        <button type="submit" class="btn btn-primary w-32">Simpan Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT ITEM --}}
    <div id="edit-item-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="POST" id="edit-item-form">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">

                    <div class="modal-header">
                        <h2 class="font-medium text-base mr-auto">
                            <i data-lucide="edit" class="w-5 h-5 mr-2 inline text-primary"></i>
                            Edit Item Order
                        </h2>
                        <button type="button" data-tw-dismiss="modal" class="text-slate-400 hover:text-slate-600">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <div class="modal-body grid grid-cols-12 gap-4">
                        {{-- Kategori --}}
                        <div class="col-span-12 md:col-span-6">
                            <label for="edit_kategori_id" class="form-label">Kategori Produksi</label>
                            <select id="edit_kategori_id" name="kategori_id" class="form-select" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kategori)
                                    <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Bahan --}}
                        <div class="col-span-12 md:col-span-6">
                            <label for="edit_bahan" class="form-label">Bahan</label>
                            <input type="text" id="edit_bahan" name="bahan" class="form-control"
                                placeholder="Contoh: Albatros, Vinyl">
                        </div>

                        {{-- Deskripsi --}}
                        <div class="col-span-12">
                            <label for="edit_deskripsi" class="form-label">Deskripsi</label>
                            <input type="text" id="edit_deskripsi" name="deskripsi" class="form-control" required>
                        </div>

                        {{-- Panjang --}}
                        <div class="col-span-4">
                            <label for="edit_panjang" class="form-label">Panjang (m)</label>
                            <input type="number" id="edit_panjang" name="panjang" class="form-control" step="0.01"
                                min="0" required>
                        </div>

                        {{-- Lebar --}}
                        <div class="col-span-4">
                            <label for="edit_lebar" class="form-label">Lebar (m)</label>
                            <input type="number" id="edit_lebar" name="lebar" class="form-control" step="0.01"
                                min="0" required>
                        </div>

                        {{-- Jumlah --}}
                        <div class="col-span-4">
                            <label for="edit_jumlah" class="form-label">Jumlah</label>
                            <input type="number" id="edit_jumlah" name="jumlah" class="form-control" min="1"
                                required>
                        </div>

                        {{-- Harga --}}
                        <div class="col-span-12">
                            <label for="edit_harga" class="form-label">Harga per m²</label>
                            <div class="input-group">
                                <span class="input-group-text bg-slate-100">Rp</span>
                                <input type="number" id="edit_harga" name="harga" class="form-control"
                                    min="0" required>
                            </div>
                        </div>

                        {{-- Preview Subtotal --}}
                        <div class="col-span-12">
                            <div class="bg-slate-100 dark:bg-darkmode-600 p-4 rounded-md">
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-600 dark:text-slate-300">Subtotal Item</span>
                                    <span class="text-xl font-bold text-primary" id="edit-modal-subtotal">Rp 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" data-tw-dismiss="modal"
                            class="btn btn-outline-secondary w-24">Batal</button>
                        <button type="submit" class="btn btn-primary w-32">Update Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Fungsi edit item
        function editItem(id) {
            fetch(`/produksi/detail/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const form = document.getElementById('edit-item-form');
                        form.action = `/produksi/detail/${id}`;

                        document.getElementById('edit_kategori_id').value = data.data.kategori_id;
                        document.getElementById('edit_bahan').value = data.data.bahan || '';
                        document.getElementById('edit_deskripsi').value = data.data.deskripsi;
                        document.getElementById('edit_panjang').value = data.data.panjang;
                        document.getElementById('edit_lebar').value = data.data.lebar;
                        document.getElementById('edit_jumlah').value = data.data.jumlah;
                        document.getElementById('edit_harga').value = data.data.harga;

                        // Trigger kalkulasi
                        document.getElementById('edit_panjang').dispatchEvent(new Event('input'));

                        // Buka modal (Tailwind/MT)
                        const modal = document.getElementById('edit-item-modal');
                        const modalInstance = tailwind.Modal.getOrCreateInstance(modal);
                        modalInstance.show();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil data item');
                });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Format Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            // Kalkulasi modal tambah
            const modalPanjang = document.getElementById('panjang');
            const modalLebar = document.getElementById('lebar');
            const modalJumlah = document.getElementById('jumlah');
            const modalHarga = document.getElementById('harga');
            const modalSubtotal = document.getElementById('modal-subtotal');

            function kalkulasiModalSubtotal() {
                const p = parseFloat(modalPanjang?.value) || 0;
                const l = parseFloat(modalLebar?.value) || 0;
                const j = parseFloat(modalJumlah?.value) || 0;
                const h = parseFloat(modalHarga?.value) || 0;
                const subtotal = p * l * j * h;
                if (modalSubtotal) modalSubtotal.textContent = formatRupiah(subtotal);
            }

            if (modalPanjang) {
                modalPanjang.addEventListener('input', kalkulasiModalSubtotal);
                modalLebar.addEventListener('input', kalkulasiModalSubtotal);
                modalJumlah.addEventListener('input', kalkulasiModalSubtotal);
                modalHarga.addEventListener('input', kalkulasiModalSubtotal);
            }

            // Kalkulasi modal edit
            const editPanjang = document.getElementById('edit_panjang');
            const editLebar = document.getElementById('edit_lebar');
            const editJumlah = document.getElementById('edit_jumlah');
            const editHarga = document.getElementById('edit_harga');
            const editModalSubtotal = document.getElementById('edit-modal-subtotal');

            function kalkulasiEditModalSubtotal() {
                const p = parseFloat(editPanjang?.value) || 0;
                const l = parseFloat(editLebar?.value) || 0;
                const j = parseFloat(editJumlah?.value) || 0;
                const h = parseFloat(editHarga?.value) || 0;
                const subtotal = p * l * j * h;
                if (editModalSubtotal) editModalSubtotal.textContent = formatRupiah(subtotal);
            }

            if (editPanjang) {
                editPanjang.addEventListener('input', kalkulasiEditModalSubtotal);
                editLebar.addEventListener('input', kalkulasiEditModalSubtotal);
                editJumlah.addEventListener('input', kalkulasiEditModalSubtotal);
                editHarga.addEventListener('input', kalkulasiEditModalSubtotal);
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Subtotal awal dari PHP
            const subtotalAwal = {{ $subtotal }};
            const itemCount = {{ $detailItems->count() }};

            // Element references
            const biayaInput = document.getElementById('biaya_design');
            const diskonInput = document.getElementById('diskon');
            const bayarInput = document.getElementById('bayar');
            const displaySubtotal = document.getElementById('display-subtotal');
            const displayBiaya = document.getElementById('display-biaya');
            const displayDiskon = document.getElementById('display-diskon');
            const displayTotal = document.getElementById('display-total');
            const displaySisa = document.getElementById('display-sisa');

            // Format Rupiah
            function formatRupiah(angka) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
            }

            // Fungsi kalkulasi total
            function kalkulasiTotal() {
                const biaya = parseFloat(biayaInput?.value) || 0;
                const diskon = parseFloat(diskonInput?.value) || 0;
                const bayar = parseFloat(bayarInput?.value) || 0;

                const total = subtotalAwal + biaya - diskon;
                const sisa = Math.max(0, total - bayar);

                if (displayBiaya) displayBiaya.textContent = formatRupiah(biaya);
                if (displayDiskon) displayDiskon.textContent = '- ' + formatRupiah(diskon);
                if (displayTotal) displayTotal.textContent = formatRupiah(total);
                if (displaySisa) displaySisa.textContent = formatRupiah(sisa);
            }

            // Event listeners
            if (biayaInput) biayaInput.addEventListener('input', kalkulasiTotal);
            if (diskonInput) diskonInput.addEventListener('input', kalkulasiTotal);
            if (bayarInput) bayarInput.addEventListener('input', kalkulasiTotal);

            // Panggil sekali untuk inisialisasi
            kalkulasiTotal();

            // Validasi sebelum submit
            document.getElementById('finalisasi-form')?.addEventListener('submit', function(e) {
                // Cek minimal item
                if (itemCount === 0) {
                    e.preventDefault();
                    alert('Minimal harus ada 1 item dalam order!');
                    return false;
                }

                // Cek pembayaran
                const bayar = parseFloat(bayarInput?.value) || 0;
                if (bayar < 0) {
                    e.preventDefault();
                    alert('Jumlah bayar tidak boleh negatif!');
                    return false;
                }

                // Konfirmasi jika bayar 0
                if (bayar === 0) {
                    const konfirmasi = confirm(
                        'Anda yakin ingin menyimpan dengan pembayaran Rp 0? Sisa akan masuk ke piutang.'
                    );
                    if (!konfirmasi) {
                        e.preventDefault();
                        return false;
                    }
                }

                return true;
            });
        });
    </script>
@endpush

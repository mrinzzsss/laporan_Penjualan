@extends('layouts.app')

@section('title', 'Tambah Transaksi')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Tambah Transaksi</h1>
        <p class="text-sm text-slate-500">Catat transaksi penjualan baru.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200 max-w-2xl">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-4xl mb-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Pilih dari Menu</h2>

        @foreach($kategoriList as $kategori)
            @if($kategori->barang->isNotEmpty())
            <div class="mb-5">
                <h3 class="text-sm font-medium text-slate-600 mb-2">{{ $kategori->nama }}</h3>
                <x-product-grid
                    :barang-list="$kategori->barang"
                    :add-onclick-for='fn ($barang) => sprintf("bukaModalMenu(%d, %s, %d)", $barang->id, json_encode($barang->nama), $barang->harga)'
                />
            </div>
            @endif
        @endforeach
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('transaksi.store') }}" id="transaksiForm" class="space-y-4">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode Transaksi</label>
                    <input type="text" name="kode_transaksi" value="{{ old('kode_transaksi') }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                </div>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-700">Item Barang</label>
                    <button type="button" onclick="addItemRow()" class="text-sm text-sky-500 hover:text-sky-600 font-medium">
                        + Tambah Item
                    </button>
                </div>

                <div id="itemRows" class="space-y-2">
                    {{-- Baris item akan ditambahkan di sini oleh JavaScript --}}
                </div>
            </div>

            <div class="text-right text-sm text-slate-600 pt-2 border-t border-slate-100">
                Estimasi Total: <span id="estimatedTotal" class="font-semibold text-slate-800">Rp 0</span>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Simpan Transaksi
                </button>
                <a href="{{ route('transaksi.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Modal input jumlah dari card menu -->
    <div id="modalMenu" class="fixed inset-0 bg-black/50 items-center justify-center z-50" style="display:none;">
        <div class="bg-white rounded-xl p-5 w-full max-w-xs mx-4">
            <div class="flex justify-between items-center mb-3">
                <h3 id="modalMenuNama" class="text-sm font-semibold text-slate-800">Tambah Item</h3>
                <button type="button" onclick="tutupModalMenu()" class="text-slate-400 hover:text-slate-600 text-lg leading-none">&times;</button>
            </div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Jumlah</label>
            <input type="number" id="modalMenuJumlah" min="1" value="1"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
            <button type="button" onclick="tambahDariModal()"
                    class="w-full bg-sky-400 hover:bg-sky-500 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                Tambah ke Nota
            </button>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    // Daftar barang aktif beserta harga, dikirim dari controller (plain JS, tanpa TypeScript).
    const barangList = @json($barangList->map(fn ($b) => ['id' => $b->id, 'nama' => $b->nama, 'harga' => $b->harga]));

    let rowCount = 0;

    function addItemRow(barangId = '', jumlah = 1) {
        const container = document.getElementById('itemRows');
        const rowIndex = rowCount++;

        const row = document.createElement('div');
        row.className = 'flex gap-2 items-start';
        row.dataset.rowIndex = rowIndex;

        const barangOptions = barangList.map(b =>
            `<option value="${b.id}" data-harga="${b.harga}" ${String(b.id) === String(barangId) ? 'selected' : ''}>${b.nama} (Rp ${b.harga.toLocaleString('id-ID')})</option>`
        ).join('');

        row.innerHTML = `
            <select name="items[${rowIndex}][barang_id]" required
                    class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400 barang-select">
                <option value="">Pilih barang</option>
                ${barangOptions}
            </select>
            <input type="number" name="items[${rowIndex}][jumlah]" value="${jumlah}" min="1" required
                   class="w-24 rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400 jumlah-input">
            <button type="button" onclick="removeItemRow(this)" class="px-3 py-2 text-red-500 hover:text-red-600 text-sm">
                Hapus
            </button>
        `;

        container.appendChild(row);

        row.querySelector('.barang-select').addEventListener('change', updateEstimatedTotal);
        row.querySelector('.jumlah-input').addEventListener('input', updateEstimatedTotal);

        updateEstimatedTotal();
    }

    function removeItemRow(button) {
        button.closest('div[data-row-index]').remove();
        updateEstimatedTotal();
    }

    function updateEstimatedTotal() {
        let total = 0;

        document.querySelectorAll('#itemRows > div').forEach(row => {
            const select = row.querySelector('.barang-select');
            const jumlahInput = row.querySelector('.jumlah-input');

            const selectedOption = select.options[select.selectedIndex];
            const harga = selectedOption ? parseInt(selectedOption.dataset.harga || 0) : 0;
            const jumlah = parseInt(jumlahInput.value || 0);

            total += harga * jumlah;
        });

        document.getElementById('estimatedTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    }

    // Mulai dengan 1 baris item kosong
    addItemRow();

    // ===== Modal "Pilih dari Menu" =====
    let modalBarangId = null;

    function bukaModalMenu(barangId, nama, harga) {
        modalBarangId = barangId;
        document.getElementById('modalMenuNama').textContent = 'Tambah: ' + nama;
        document.getElementById('modalMenuJumlah').value = 1;
        const modal = document.getElementById('modalMenu');
        modal.style.display = 'flex';
    }

    function tutupModalMenu() {
        document.getElementById('modalMenu').style.display = 'none';
    }

    function tambahDariModal() {
        const jumlah = parseInt(document.getElementById('modalMenuJumlah').value || 1);
        addItemRow(modalBarangId, jumlah);
        tutupModalMenu();
    }

    document.getElementById('modalMenu').addEventListener('click', function (e) {
        if (e.target === this) tutupModalMenu();
    });

    // Kalau datang dari tombol "+" di dashboard (?barang=ID), langsung buka modal jumlah.
    (function bukaModalDariQuery() {
        const params = new URLSearchParams(window.location.search);
        const barangId = params.get('barang');
        if (!barangId) return;

        const barang = barangList.find(b => String(b.id) === String(barangId));
        if (barang) bukaModalMenu(barang.id, barang.nama, barang.harga);
    })();
</script>
@endpush

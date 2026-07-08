@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Edit Transaksi</h1>
        <p class="text-sm text-slate-500">Perbarui transaksi {{ $kodeTransaksi }}.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200 max-w-2xl">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl border border-slate-200 p-6 max-w-2xl">
        <form method="POST" action="{{ route('transaksi.update', $items->first()->id) }}" id="transaksiForm" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Kode Transaksi</label>
                    <input type="text" name="kode_transaksi" value="{{ old('kode_transaksi', $kodeTransaksi) }}" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($tanggal)->format('Y-m-d')) }}" required
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
                    Simpan Perubahan
                </button>
                <a href="{{ route('transaksi.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2.5 rounded-lg transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection

@push('scripts')
<script>
    const barangList = @json($barangList->map(fn ($b) => ['id' => $b->id, 'nama' => $b->nama, 'harga' => $b->harga]));
    const existingItems = @json($items->map(fn ($item) => ['barang_id' => $item->barang_id, 'jumlah' => $item->jumlah]));

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

    // Isi baris item dari data transaksi yang sudah ada
    if (existingItems.length > 0) {
        existingItems.forEach(item => addItemRow(item.barang_id, item.jumlah));
    } else {
        addItemRow();
    }
</script>
@endpush

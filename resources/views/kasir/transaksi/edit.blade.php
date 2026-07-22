@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')

    <div class="mb-6">
        <h1 class="text-xl font-bold text-slate-800">Edit Transaksi</h1>
        <p class="text-sm text-slate-500">Perbarui transaksi {{ $kodeTransaksi }}.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- KIRI: Katalog Menu (col-span-7 / 8) --}}
        <div class="lg:col-span-7 xl:col-span-8 bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="text-base font-bold text-slate-800 mb-4">Pilih dari Menu</h2>

            @foreach($kategoriList as $kategori)
                @if($kategori->barang->isNotEmpty())
                <div class="mb-6 last:mb-0">
                    <h3 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-100">{{ $kategori->nama }}</h3>
                    <x-product-grid
                        :barang-list="$kategori->barang"
                        :add-onclick-for='fn ($barang) => sprintf("tambahKeKeranjang(%d)", $barang->id)'
                    />
                </div>
                @endif
            @endforeach
        </div>

        {{-- KANAN: Panel Detail Transaksi / Keranjang (col-span-5 / 4) --}}
        <div class="lg:col-span-5 xl:col-span-4 sticky top-20">
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-800">Detail Transaksi</h2>
                    <span id="cartCountBadge" class="text-xs bg-sky-100 text-sky-600 px-2.5 py-1 rounded-full font-medium">0 item</span>
                </div>

                <form method="POST" action="{{ route('transaksi.update', $items->first()->id) }}" id="transaksiForm" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Kode Transaksi</label>
                            <input type="text" name="kode_transaksi" value="{{ old('kode_transaksi', $kodeTransaksi) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', \Carbon\Carbon::parse($tanggal)->format('Y-m-d')) }}" required
                                   class="w-full rounded-lg border border-slate-300 px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400">
                        </div>
                    </div>

                    <div class="pt-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-2">Item Pesanan</label>

                        <div id="cartContainer" class="space-y-2 max-h-[380px] overflow-y-auto pr-1">
                            <div id="emptyCartState" class="py-8 text-center border border-dashed border-slate-200 rounded-lg text-slate-400 text-xs">
                                Belum ada item dipilih.<br>Klik tombol <strong>+</strong> pada menu di sebelah kiri.
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600 font-medium">Total Bayar:</span>
                            <span id="estimatedTotal" class="text-base font-bold text-sky-600">Rp 0</span>
                        </div>

                        <div class="flex gap-2 pt-1">
                            <button type="submit" id="submitBtn" disabled
                                    class="flex-1 bg-sky-400 hover:bg-sky-500 disabled:bg-slate-200 disabled:text-slate-400 disabled:cursor-not-allowed text-white text-sm font-medium py-2.5 rounded-lg transition">
                                Simpan Perubahan
                            </button>
                            <a href="{{ route('transaksi.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-4 py-2.5 rounded-lg transition">
                                Batal
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
    const barangList = @json($barangList->map(fn ($b) => ['id' => $b->id, 'nama' => $b->nama, 'harga' => $b->harga]));
    const existingItems = @json($items->map(fn ($item) => ['barang_id' => $item->barang_id, 'jumlah' => $item->jumlah]));

    const cart = new Map();

    // Load existing transaction items into cart state
    if (existingItems.length > 0) {
        existingItems.forEach(item => {
            cart.set(parseInt(item.barang_id), parseInt(item.jumlah));
        });
    }

    function tambahKeKeranjang(barangId) {
        const id = parseInt(barangId);
        const currentQty = cart.get(id) || 0;
        cart.set(id, currentQty + 1);
        renderCart();
    }

    function kurangiDariKeranjang(barangId) {
        const id = parseInt(barangId);
        const currentQty = cart.get(id) || 0;
        if (currentQty > 1) {
            cart.set(id, currentQty - 1);
        } else {
            cart.delete(id);
        }
        renderCart();
    }

    function hapusDariKeranjang(barangId) {
        const id = parseInt(barangId);
        cart.delete(id);
        renderCart();
    }

    function updateQtyDirect(barangId, qty) {
        const id = parseInt(barangId);
        const val = parseInt(qty);
        if (isNaN(val) || val <= 0) {
            cart.delete(id);
        } else {
            cart.set(id, val);
        }
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartContainer');
        const submitBtn = document.getElementById('submitBtn');
        const badge = document.getElementById('cartCountBadge');
        
        container.innerHTML = '';

        if (cart.size === 0) {
            container.innerHTML = `
                <div id="emptyCartState" class="py-8 text-center border border-dashed border-slate-200 rounded-lg text-slate-400 text-xs">
                    Belum ada item dipilih.<br>Klik tombol <strong>+</strong> pada menu di sebelah kiri.
                </div>
            `;
            document.getElementById('estimatedTotal').textContent = 'Rp 0';
            submitBtn.disabled = true;
            badge.textContent = '0 item';
            return;
        }

        let total = 0;
        let totalItemCount = 0;
        let index = 0;

        cart.forEach((qty, barangId) => {
            const barang = barangList.find(b => b.id === barangId);
            if (!barang) return;

            const subtotal = barang.harga * qty;
            total += subtotal;
            totalItemCount += qty;

            const row = document.createElement('div');
            row.className = 'flex items-center justify-between gap-2 p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm';
            
            row.innerHTML = `
                <input type="hidden" name="items[${index}][barang_id]" value="${barang.id}">
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-slate-800 text-xs truncate">${barang.nama}</div>
                    <div class="text-[11px] text-slate-500">Rp ${barang.harga.toLocaleString('id-ID')} &times; ${qty} = <span class="font-medium text-slate-700">Rp ${subtotal.toLocaleString('id-ID')}</span></div>
                </div>

                <div class="flex items-center gap-1">
                    <button type="button" onclick="kurangiDariKeranjang(${barang.id})"
                            class="w-6 h-6 rounded bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold flex items-center justify-center text-xs transition">
                        -
                    </button>
                    <input type="number" name="items[${index}][jumlah]" value="${qty}" min="1"
                           onchange="updateQtyDirect(${barang.id}, this.value)"
                           class="w-10 text-center text-xs border border-slate-300 rounded py-0.5 focus:outline-none focus:ring-1 focus:ring-sky-400 font-medium">
                    <button type="button" onclick="tambahKeKeranjang(${barang.id})"
                            class="w-6 h-6 rounded bg-sky-100 hover:bg-sky-200 text-sky-700 font-bold flex items-center justify-center text-xs transition">
                        +
                    </button>
                </div>

                <button type="button" onclick="hapusDariKeranjang(${barang.id})"
                        class="w-6 h-6 rounded hover:bg-red-50 text-slate-400 hover:text-red-500 flex items-center justify-center text-sm font-bold transition" title="Hapus item">
                    &times;
                </button>
            `;

            container.appendChild(row);
            index++;
        });

        document.getElementById('estimatedTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
        submitBtn.disabled = false;
        badge.textContent = totalItemCount + ' item';
    }

    renderCart();
</script>
@endpush

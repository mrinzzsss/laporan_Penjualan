{{--
    Kartu produk (barang) sederhana, gaya Shopee: gambar + tombol "+" di pojok.
    Dipakai di dashboard kasir & bisa dipakai ulang di halaman lain (mis. create transaksi).

    Props:
    - barang        : model Barang (wajib) — pakai nama, deskripsi, harga, gambar_url
    - addUrl        : string|null — kalau diisi, tombol "+" jadi link ke url ini (contoh: route transaksi.create dengan query barang)
    - addOnclick    : string|null — kalau diisi, tombol "+" pakai onclick JS custom (contoh: bukaModalMenu(...))
--}}
@props([
    'barang',
    'addUrl' => null,
    'addOnclick' => null,
])

<div class="border border-slate-200 rounded-lg overflow-hidden relative bg-white hover:shadow-sm transition">

    {{-- Tombol tambah, pojok kanan atas --}}
    @if ($addOnclick)
        <button type="button"
                onclick="{{ $addOnclick }}"
                class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-sky-400 hover:bg-sky-500 text-white text-sm leading-none flex items-center justify-center z-10">
            +
        </button>
    @elseif ($addUrl)
        <a href="{{ $addUrl }}"
           class="absolute top-1.5 right-1.5 w-6 h-6 rounded-full bg-sky-400 hover:bg-sky-500 text-white text-sm leading-none flex items-center justify-center z-10">
            +
        </a>
    @endif

    {{-- Gambar --}}
    @if ($barang->gambar_url)
        <img src="{{ $barang->gambar_url }}" alt="{{ $barang->nama }}" class="w-full h-24 object-cover bg-slate-100">
    @else
        <div class="w-full h-24 flex items-center justify-center bg-slate-100 text-slate-400 text-xs">
            Tidak ada gambar
        </div>
    @endif

    {{-- Info --}}
    <div class="p-2">
        <div class="text-xs font-semibold text-slate-800 truncate">{{ $barang->nama }}</div>
        <div class="text-xs text-slate-500 mb-1 line-clamp-2" style="min-height: 28px;">
            {{ $barang->deskripsi ?: '-' }}
        </div>
        <div class="text-xs font-medium text-sky-600">Rp {{ number_format($barang->harga, 0, ',', '.') }}</div>
    </div>
</div>

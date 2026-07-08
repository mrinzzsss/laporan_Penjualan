{{--
    Grid produk: maksimal 5 kartu per baris, sisanya otomatis turun ke baris berikutnya.
    Di layar kecil turun jadi 2-3 kolom biar tetap enak dilihat di HP.

    Props:
    - barangList : koleksi model Barang (wajib)
    - addUrlFor  : Closure|null  — dipanggil per barang, hasilnya jadi href tombol "+"
    - addOnclickFor : Closure|null — dipanggil per barang, hasilnya jadi onclick tombol "+"
--}}
@props([
    'barangList',
    'addUrlFor' => null,
    'addOnclickFor' => null,
])

@if ($barangList->isEmpty())
    <div class="text-sm text-slate-400 py-6 text-center">Belum ada produk aktif.</div>
@else
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach ($barangList as $barang)
            <x-product-card
                :barang="$barang"
                :add-url="$addUrlFor ? $addUrlFor($barang) : null"
                :add-onclick="$addOnclickFor ? $addOnclickFor($barang) : null"
            />
        @endforeach
    </div>
@endif

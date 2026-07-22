<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Kasir Kantin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    @stack('styles')
</head>
<body class="bg-slate-100 min-h-screen">

    {{-- Navbar --}}
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex justify-between h-16 items-center">

                <div class="flex items-center gap-8">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-slate-800 text-lg">
                        <span class="w-8 h-8 rounded-lg bg-sky-400 flex items-center justify-center text-white text-sm">KK</span>
                        Kasir Kantin
                    </a>

                    <div class="hidden md:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 hover:bg-slate-50' }}">
                            Dashboard
                        </a>

                        @if (auth()->user()?->isAdmin())
                            <a href="{{ route('kategori.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('kategori.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                Kategori
                            </a>
                            <a href="{{ route('barang.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('barang.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                Barang
                            </a>
                            <a href="{{ route('transaksi.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('transaksi.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                Transaksi
                            </a>
                        @endif

                        @if (auth()->user()?->isKasir())
                            <a href="{{ route('transaksi.index') }}"
                               class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('transaksi.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 hover:bg-slate-50' }}">
                                Transaksi
                            </a>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <span class="hidden sm:block text-sm text-slate-500">
                        {{ auth()->user()->name ?? '' }}
                        @if (auth()->user())
                            <span class="text-xs text-slate-400">({{ ucfirst(auth()->user()->role) }})</span>
                        @endif
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm font-medium text-slate-500 hover:text-red-500 transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>

            {{-- Mobile nav --}}
            <div class="md:hidden flex gap-1 pb-3 -mt-1 overflow-x-auto">
                <a href="{{ route('dashboard') }}" class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap {{ request()->routeIs('dashboard') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 bg-slate-50' }}">Dashboard</a>
                @if (auth()->user()?->isAdmin())
                    <a href="{{ route('kategori.index') }}" class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap {{ request()->routeIs('kategori.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 bg-slate-50' }}">Kategori</a>
                    <a href="{{ route('barang.index') }}" class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap {{ request()->routeIs('barang.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 bg-slate-50' }}">Barang</a>
                    <a href="{{ route('transaksi.index') }}" class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap {{ request()->routeIs('transaksi.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 bg-slate-50' }}">Transaksi</a>
                @endif
                @if (auth()->user()?->isKasir())
                    <a href="{{ route('transaksi.index') }}" class="px-3 py-1.5 rounded-md text-xs font-medium whitespace-nowrap {{ request()->routeIs('transaksi.*') ? 'bg-sky-50 text-sky-600' : 'text-slate-600 bg-slate-50' }}">Transaksi</a>
                @endif
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                {{ session('error') }}
            </div>
        @endif
    </div>

    {{-- Konten halaman --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

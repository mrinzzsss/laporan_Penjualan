<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laporan Penjualan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        <div class="text-center mb-6">
            <span class="inline-flex w-12 h-12 rounded-xl bg-sky-400 items-center justify-center text-white font-bold text-lg mb-3">LP</span>
            <h1 class="text-xl font-bold text-slate-800">Laporan Penjualan</h1>
            <p class="text-sm text-slate-500">Masuk untuk mengakses dashboard</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">

            @if (session('error'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 text-red-700 text-sm border border-red-200">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                           placeholder="admin@kantin.test">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-400 focus:border-sky-400"
                           placeholder="••••••••">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="remember" class="rounded border-slate-300 text-sky-400 focus:ring-sky-400">
                    <label for="remember" class="ml-2 text-sm text-slate-600">Ingat saya</label>
                </div>

                <button type="submit"
                        class="w-full bg-sky-400 hover:bg-sky-500 text-white font-medium rounded-lg py-2.5 text-sm transition">
                    Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-4">
            Default: admin@kantin.test / password
        </p>
    </div>

</body>
</html>

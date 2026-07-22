<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman form login.
     * view('auth.login') merender file template HTML auth/login.blade.php.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Memproses login pengguna.
     * Menggunakan $request->validate untuk memeriksa form input,
     * Auth::attempt untuk melakukan autentikasi email & password ke database,
     * dan $request->session()->regenerate() untuk mencegah serangan Session Fixation.
     */
    public function login(Request $request)
    {
        // Memvalidasi data input dari form login (email wajib format email, password wajib diisi)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Auth::attempt mengecek kecocokan email & password ter-hash di database.
        if (! Auth::attempt($credentials, $remember)) {
            // Jika gagal/salah, melempar exception validasi berisi pesan error
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        // Jika berhasil, buat ulang session ID untuk keamanan (Session Fixation Defense)
        $request->session()->regenerate();

        // Redirect pengguna ke route dashboard (atau halaman tujuan sebelum dipaksa login) membawa flash message
        return redirect()->intended(route('dashboard'))
            ->with('success', 'Berhasil login.');
    }

    /**
     * Memproses logout pengguna.
     * Menghapus autentikasi via Auth::logout(), membersihkan data session,
     * dan membuat token CSRF baru demi keamanan.
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Hapus status autentikasi user saat ini

        $request->session()->invalidate(); // Hapus seluruh data session terdaftar
        $request->session()->regenerateToken(); // Regenerasi token CSRF baru

        return redirect()->route('login')
            ->with('success', 'Berhasil logout.');
    }
}

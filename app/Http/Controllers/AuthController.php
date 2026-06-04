<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * AuthController - Menangani login, register, dan logout.
 * Setelah login, user diarahkan sesuai role-nya.
 */
class AuthController extends Controller
{
    // ─── Tampilkan Form Login ─────────────────────────────────────
    public function showLogin()
    {
        return view('auth.login');
    }

    // ─── Proses Login ─────────────────────────────────────────────
    public function login(Request $request)
    {
        // Validasi input form login
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:8',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 8 karakter.',
        ]);

        // Coba autentikasi dengan kredensial yang diberikan
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            // Redirect berdasarkan role
            if (Auth::user()->isAdmin()) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Selamat datang, Admin!');
            }

            // Cek apakah ada parameter booking yang dikirim dari form login
            if ($request->filled('lapangan_id') && $request->filled('tanggal') && $request->filled('jam_mulai')) {
                return redirect()->route('booking.index', [
                    'lapangan_id' => $request->lapangan_id,
                    'tanggal'     => $request->tanggal,
                    'jam_mulai'   => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                ])->with('success', 'Silakan lanjutkan proses booking Anda.');
            }

            // intended() = arahkan ke halaman yang semula ingin dibuka (misal: /booking)
            return redirect()->intended(route('booking.index'))
                ->with('success', 'Selamat datang, ' . Auth::user()->name . '!');
        }

        // Jika gagal login
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
    }

    // ─── Tampilkan Form Register ──────────────────────────────────
    public function showRegister()
    {
        return view('auth.register');
    }

    // ─── Proses Registrasi ────────────────────────────────────────
    public function register(Request $request)
    {
        // Validasi input form registrasi
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            // MEDIUM-2: Password minimal 8 karakter, huruf besar+kecil, dan angka
            'password'  => [
                'required',
                'min:8',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'nomor_hp'  => 'nullable|string|max:15',
            'alamat'    => 'nullable|string|max:500',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
        ]);

        // Buat user baru dengan role pelanggan
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'role'      => 'pelanggan', // Default role: pelanggan
            'nomor_hp'  => $request->nomor_hp,
            'alamat'    => $request->alamat,
        ]);

        // Login otomatis setelah registrasi
        Auth::login($user);

        // Cek apakah ada parameter booking yang dikirim dari form registrasi
        if ($request->filled('lapangan_id') && $request->filled('tanggal') && $request->filled('jam_mulai')) {
            return redirect()->route('booking.index', [
                'lapangan_id' => $request->lapangan_id,
                'tanggal'     => $request->tanggal,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
            ])->with('success', 'Registrasi berhasil! Silakan lanjutkan proses booking Anda.');
        }

        return redirect()->route('booking.index')
            ->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }

    // ─── Logout ───────────────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda berhasil logout.');
    }
}

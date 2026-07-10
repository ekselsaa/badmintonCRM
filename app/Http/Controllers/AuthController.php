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
    use \App\Traits\NormalizePhoneNumber;
    // Tampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses Login
    public function login(Request $request)
    {
        // Validasi input form login
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username atau No. WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = $request->input('username');
        $password = $request->input('password');

        // Tentukan tipe login (nomor_hp atau username)
        $fieldType = is_numeric($loginInput) ? 'nomor_hp' : 'username';

        // Normalisasi nomor HP jika input bertipe nomor
        if ($fieldType === 'nomor_hp') {
            $loginInput = $this->normalizePhoneNumber($loginInput);
        }

        $credentials = [
            $fieldType => $loginInput,
            'password' => $password,
        ];

        // Coba autentikasi dengan kredensial yang diberikan
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
            'username' => 'Kredensial yang Anda masukkan salah.',
        ])->withInput($request->only('username'));
    }

    // Tampilkan Form Register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses Registrasi
    public function register(Request $request)
    {
        // Normalisasi nomor HP sebelum validasi unik agar pencarian database akurat
        if ($request->filled('nomor_hp')) {
            $request->merge(['nomor_hp' => $this->normalizePhoneNumber($request->nomor_hp)]);
        }

        // Validasi input form registrasi
        $request->validate([
            'name'      => 'required|string|max:255',
            'username'  => 'required|string|max:255|unique:users,username|alpha_dash',
            // Password minimal 8 karakter, huruf besar+kecil, dan angka
            'password'  => [
                'required',
                'min:8',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'nomor_hp'  => [
                'required',
                'string',
                'min:9',
                'max:15',
                'regex:/^628[0-9]+$/',
                'unique:users,nomor_hp',
            ],
            'alamat'    => 'nullable|string|max:500',
        ], [
            'name.required'      => 'Nama wajib diisi.',
            'username.required'  => 'Username wajib diisi.',
            'username.unique'    => 'Username sudah terdaftar.',
            'username.alpha_dash'=> 'Username hanya boleh berisi huruf, angka, strip, dan garis bawah.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
            'nomor_hp.required'  => 'Nomor WhatsApp wajib diisi.',
            'nomor_hp.unique'    => 'Nomor WhatsApp ini sudah terdaftar.',
            'nomor_hp.regex'     => 'Format nomor WhatsApp tidak valid. Gunakan format nomor HP Indonesia yang benar (08xxx / 628xxx).',
            'nomor_hp.min'       => 'Nomor WhatsApp minimal 9 digit.',
            'nomor_hp.max'       => 'Nomor WhatsApp maksimal 15 digit.',
        ]);

        // Buat user baru dengan role pelanggan
        $user = User::create([
            'name'      => $request->name,
            'username'  => $request->username,
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

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda berhasil logout.');
    }
}

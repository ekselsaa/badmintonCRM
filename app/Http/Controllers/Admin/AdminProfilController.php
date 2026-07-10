<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * AdminProfilController - Menangani halaman profil dan pengelolaan akun administrator.
 * Fitur: lihat profil, update data diri admin, ganti foto profil, ganti password/email.
 */
class AdminProfilController extends Controller
{
    /**
     * Tampilkan halaman profil/kelola akun admin.
     */
    public function show()
    {
        $user = Auth::user();
        return view('admin.profil.show', compact('user'));
    }

    /**
     * Update data profil/akun admin.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Normalisasi nomor HP sebelum validasi unik agar pencarian database akurat
        if ($request->filled('nomor_hp')) {
            $cleaned = preg_replace('/[^0-9]/', '', $request->nomor_hp);
            if (str_starts_with($cleaned, '0')) {
                $cleaned = '62' . substr($cleaned, 1);
            } elseif (str_starts_with($cleaned, '8')) {
                $cleaned = '62' . $cleaned;
            }
            $request->merge(['nomor_hp' => $cleaned]);
        }

        $request->validate([
            'name'        => 'required|string|max:100',
            'nomor_hp'    => [
                'nullable',
                'string',
                'min:9',
                'max:15',
                'regex:/^628[0-9]+$/',
                'unique:users,nomor_hp,' . $user->id,
            ],
            'alamat'      => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'    => 'nullable|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'foto_profil.image'  => 'File foto harus berupa gambar.',
            'foto_profil.mimes'  => 'Format gambar harus jpg, jpeg, atau png.',
            'foto_profil.max'    => 'Ukuran foto maksimal 2MB.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'nomor_hp.unique'    => 'Nomor WhatsApp ini sudah terdaftar oleh pengguna lain.',
            'nomor_hp.regex'     => 'Format nomor WhatsApp tidak valid. Gunakan format nomor HP Indonesia yang benar (08xxx / 628xxx).',
            'nomor_hp.min'       => 'Nomor WhatsApp minimal 9 digit.',
            'nomor_hp.max'       => 'Nomor WhatsApp maksimal 15 digit.',
        ]);

        // Upload foto profil jika ada
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->foto_profil = $path;
        }

        // Simpan data dasar
        $user->name     = $request->name;
        $user->nomor_hp = $request->nomor_hp;
        $user->alamat   = $request->alamat;

        // Ubah password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Akun admin berhasil diperbarui!');
    }
}

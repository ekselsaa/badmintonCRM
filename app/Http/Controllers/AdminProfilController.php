<?php

namespace App\Http\Controllers;

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

        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nomor_hp'    => 'nullable|string|max:20',
            'alamat'      => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'    => 'nullable|min:6|confirmed',
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah digunakan oleh akun lain.',
            'foto_profil.image'  => 'File foto harus berupa gambar.',
            'foto_profil.mimes'  => 'Format gambar harus jpg, jpeg, atau png.',
            'foto_profil.max'    => 'Ukuran foto maksimal 2MB.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
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
        $user->email    = $request->email;
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

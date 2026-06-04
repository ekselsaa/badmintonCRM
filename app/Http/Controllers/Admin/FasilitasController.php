<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = Fasilitas::orderBy('id')->get();
        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'nullable'
        ]);

        Fasilitas::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'icon' => $request->icon ?? 'bi-box',
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return back()->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $fasilitas = Fasilitas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:50',
        ]);

        $fasilitas->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'icon' => $request->icon ?? 'bi-box',
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return back()->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $fasilitas = Fasilitas::findOrFail($id);
        $fasilitas->delete();

        return back()->with('success', 'Fasilitas berhasil dihapus.');
    }
}

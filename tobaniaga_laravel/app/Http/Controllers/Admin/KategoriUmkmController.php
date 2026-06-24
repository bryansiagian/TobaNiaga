<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriUmkm;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriUmkmController extends Controller
{
    public function index()
    {
        $kategori = KategoriUmkm::withCount(['umkm' => fn($q) => $q->whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'verified'))])->orderBy('nama')->get();

        return view('admin.umkm.kategori', compact('kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kategori_umkm,nama'],
        ], [
            'nama.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        KategoriUmkm::create($validated);

        return redirect()->route('admin.kategori-umkm.index')
            ->with('success', 'Kategori UMKM berhasil ditambahkan.');
    }

    public function update(Request $request, KategoriUmkm $kategori_umkm)
    {
        $validated = $request->validate([
            'nama' => [
                'required', 'string', 'max:255',
                Rule::unique('kategori_umkm', 'nama')->ignore($kategori_umkm->id),
            ],
        ], [
            'nama.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        $kategori_umkm->update($validated);

        return redirect()->route('admin.kategori-umkm.index')
            ->with('success', 'Kategori UMKM berhasil diperbarui.');
    }

    public function destroy(KategoriUmkm $kategori_umkm)
    {
        $jumlahUmkm = $kategori_umkm->umkm()->count();

        if ($jumlahUmkm > 0) {
            return redirect()->route('admin.kategori-umkm.index')
                ->with('error', "Kategori \"{$kategori_umkm->nama}\" tidak bisa dihapus karena masih digunakan oleh {$jumlahUmkm} UMKM. Pindahkan UMKM ke kategori lain terlebih dahulu.");
        }

        $kategori_umkm->delete();

        return redirect()->route('admin.kategori-umkm.index')
            ->with('success', 'Kategori UMKM berhasil dihapus.');
    }
}

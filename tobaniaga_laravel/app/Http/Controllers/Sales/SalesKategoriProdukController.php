<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesKategoriProdukController extends Controller
{
    public function index(): View
    {
        $kategori = KategoriProduk::orderBy('nama')->paginate(20);
        return view('sales.kategori-produk.index', compact('kategori'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kategori_produk,nama'],
        ]);

        KategoriProduk::create($validated);

        return back()->with('status', "Kategori \"{$validated['nama']}\" berhasil ditambahkan.");
    }

    public function update(Request $request, KategoriProduk $kategoriProduk): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:kategori_produk,nama,' . $kategoriProduk->id],
        ]);

        $kategoriProduk->update($validated);

        return back()->with('status', "Kategori berhasil diperbarui.");
    }

    public function destroy(KategoriProduk $kategoriProduk): RedirectResponse
    {
        $kategoriProduk->delete();
        return back()->with('status', "Kategori berhasil dihapus.");
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerKeranjangController extends Controller
{
    public function index(): View
    {
        $items = Keranjang::with(['produk.umkm', 'produk.fotoProduk', 'produk.status'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Kelompokkan per UMKM — karena 1 pesanan = 1 toko (seperti Shopee/Tokopedia)
        $kelompok = $items->groupBy(fn ($item) => $item->produk->umkm_id);

        return view('customer.keranjang.index', compact('kelompok'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'produk_id' => ['required', 'exists:produk,id'],
            'jumlah'    => ['required', 'integer', 'min:1'],
        ]);

        $produk = Produk::findOrFail($validated['produk_id']);

        // Validasi stok saat tambah ke keranjang
        $jumlahDiKeranjang = Keranjang::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->value('jumlah') ?? 0;

        $totalDiminta = $jumlahDiKeranjang + $validated['jumlah'];

        if ($totalDiminta > $produk->stok) {
            $sisaBolehDitambah = max($produk->stok - $jumlahDiKeranjang, 0);
            return back()->with('error', "Stok \"{$produk->nama_produk}\" tidak cukup. Sisa stok: {$produk->stok}, kamu sudah punya {$jumlahDiKeranjang} di keranjang.");
        }

        $keranjang = Keranjang::firstOrNew([
            'user_id'   => Auth::id(),
            'produk_id' => $produk->id,
        ]);

        $keranjang->jumlah = ($keranjang->exists ? $keranjang->jumlah : 0) + $validated['jumlah'];
        $keranjang->save();

        return back()->with('status', "\"{$produk->nama_produk}\" ditambahkan ke keranjang.");
    }

    public function update(Request $request, Keranjang $keranjang): RedirectResponse
    {
        abort_if($keranjang->user_id !== Auth::id(), 403);

        $validated = $request->validate([
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        if ($validated['jumlah'] > $keranjang->produk->stok) {
            return back()->with('error', "Jumlah melebihi stok tersedia ({$keranjang->produk->stok}).");
        }

        $keranjang->update(['jumlah' => $validated['jumlah']]);

        return back()->with('status', 'Jumlah produk diperbarui.');
    }

    public function destroy(Keranjang $keranjang): RedirectResponse
    {
        abort_if($keranjang->user_id !== Auth::id(), 403);

        $keranjang->delete();

        return back()->with('status', 'Produk dihapus dari keranjang.');
    }
}

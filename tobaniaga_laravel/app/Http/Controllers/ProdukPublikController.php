<?php

namespace App\Http\Controllers;

use App\Models\KategoriProduk;
use App\Models\KategoriUmkm;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukPublikController extends Controller
{
    public function index(Request $request): View
    {
        $query = Produk::with(['umkm', 'kategori', 'fotoProduk', 'status'])
            ->whereHas('status', function ($q) {
                $q->where('kode', 'tersedia');
            });

        // ── Filter: kategori UMKM ───────────────────────────────
        if ($request->filled('kategori_umkm')) {
            $query->kategoriUmkm($request->kategori_umkm);
        }

        // ── Filter: kategori produk ─────────────────────────────
        if ($request->filled('kategori_produk')) {
            $query->where('kategori_id', $request->kategori_produk);
        }

        // ── Filter: rentang harga ────────────────────────────────
        if ($request->filled('harga_min')) {
            $query->hargaMin($request->harga_min);
        }
        if ($request->filled('harga_maks')) {
            $query->hargaMaks($request->harga_maks);
        }

        // ── Filter: stok ─────────────────────────────────────────
        if ($request->filled('stok')) {
            match ($request->stok) {
                'tersedia' => $query->tersediaSaja(),
                'habis'    => $query->where('stok', 0),
                default    => null,
            };
        }

        // ── Pencarian nama produk ────────────────────────────────
        if ($request->filled('cari')) {
            $query->where('nama_produk', 'like', '%' . $request->cari . '%');
        }

        // ── Urutan ────────────────────────────────────────────────
        match ($request->get('urut', 'terbaru')) {
            'harga_terendah'  => $query->orderBy('harga', 'asc'),
            'harga_tertinggi' => $query->orderBy('harga', 'desc'),
            'nama'            => $query->orderBy('nama_produk', 'asc'),
            default           => $query->latest(),
        };

        $produk = $query->paginate(12)->withQueryString();

        $kategoriUmkmList   = KategoriUmkm::orderBy('nama')->get();
        $kategoriProdukList = KategoriProduk::orderBy('nama')->get();

        return view('customer.products.index', [
            'produk'             => $produk,
            'kategoriUmkmList'   => $kategoriUmkmList,
            'kategoriProdukList' => $kategoriProdukList,
        ]);
    }

    public function detail(string $slug): View
    {
        $produk = Produk::with(['umkm', 'kategori', 'fotoProduk', 'status'])
            ->where('slug', $slug)
            ->firstOrFail();

        $ulasan = $produk->ulasan()
            ->with('user')
            ->latest()
            ->paginate(10, ['*'], 'ulasan_page');

        $produkTerkait = Produk::with(['fotoProduk', 'umkm'])
            ->where('kategori_id', $produk->kategori_id)
            ->where('id', '!=', $produk->id)
            ->whereHas('status', fn ($q) => $q->where('kode', 'tersedia'))
            ->latest()
            ->take(4)
            ->get();

        return view('customer.products.detail', compact('produk', 'ulasan', 'produkTerkait'));
    }
}

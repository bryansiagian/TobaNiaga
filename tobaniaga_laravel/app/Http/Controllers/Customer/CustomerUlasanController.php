<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CustomerUlasanController extends Controller
{
    public function create(Pesanan $pesanan)
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);
        abort_if(($pesanan->status->kode ?? '') !== 'selesai', 403);

        $pesanan->load(['detail.produk', 'umkm', 'ulasan']);

        // Produk yang belum diulas di pesanan ini
        $sudahDiulasProdukIds = $pesanan->ulasan->pluck('produk_id')->toArray();
        $produkBelumDiulas = $pesanan->detail->filter(
            fn($d) => !in_array($d->produk_id, $sudahDiulasProdukIds)
        );

        if ($produkBelumDiulas->isEmpty()) {
            return redirect()->route('customer.pesanan.show', $pesanan)
                ->with('status', 'Kamu sudah mengulas semua produk di pesanan ini.');
        }

        return view('customer.ulasan.create', compact('pesanan', 'produkBelumDiulas'));
    }

    public function store(Request $request, Pesanan $pesanan)
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);
        abort_if(($pesanan->status->kode ?? '') !== 'selesai', 403);

        $request->validate([
            'ulasan'                => 'required|array',
            'ulasan.*.produk_id'    => 'required|exists:produk,id',
            'ulasan.*.rating'       => 'required|integer|min:1|max:5',
            'ulasan.*.komentar'     => 'nullable|string|max:1000',
            'ulasan.*.is_anonim'    => 'nullable|boolean',
            'ulasan.*.foto'         => 'nullable|array|max:3',
            'ulasan.*.foto.*'       => 'image|max:2048',
        ]);

        $pesanan->load('umkm');

        foreach ($request->ulasan as $idx => $data) {
            // Cek duplikat
            $sudahAda = Ulasan::where('pesanan_id', $pesanan->id)
                ->where('user_id', Auth::id())
                ->where('produk_id', $data['produk_id'])
                ->exists();

            if ($sudahAda) continue;

            // Upload foto
            $fotoPaths = [];
            if ($request->hasFile("ulasan.{$idx}.foto")) {
                foreach ($request->file("ulasan.{$idx}.foto") as $file) {
                    $fotoPaths[] = $file->store('ulasan', 'public');
                }
            }

            Ulasan::create([
                'pesanan_id' => $pesanan->id,
                'user_id'    => Auth::id(),
                'umkm_id'    => $pesanan->umkm_id,
                'produk_id'  => $data['produk_id'],
                'rating'     => $data['rating'],
                'komentar'   => $data['komentar'] ?? null,
                'is_anonim'  => isset($data['is_anonim']) ? (bool)$data['is_anonim'] : false,
                'foto'       => !empty($fotoPaths) ? $fotoPaths : null,
            ]);
        }

        return redirect()->route('customer.pesanan.show', $pesanan)
            ->with('status', 'Terima kasih! Ulasan kamu sudah disimpan.');
    }
}

<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AlamatCustomer;
use App\Models\Keranjang;
use App\Models\MetodePengiriman;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\Promo;
use App\Models\StatusPembayaran;
use App\Models\StatusPesanan;
use App\Models\Umkm;
use App\Models\OngkosKirimTrayek;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerCheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $request->validate([
            'umkm_id'         => ['required', 'exists:umkm,id'],
            'keranjang_ids'   => ['required', 'array', 'min:1'],
            'keranjang_ids.*' => ['integer', 'exists:keranjang,id'],
        ]);

        $items = Keranjang::with(['produk.fotoProduk', 'produk.status'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $request->keranjang_ids)
            ->whereHas('produk', fn($q) => $q->where('umkm_id', $request->umkm_id))
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('customer.keranjang.index')
                ->with('error', 'Item yang dipilih tidak valid.');
        }

        foreach ($items as $item) {
            if ($item->jumlah > $item->produk->stok) {
                return redirect()->route('customer.keranjang.index')
                    ->with('error', "Stok \"{$item->produk->nama_produk}\" tidak mencukupi (tersisa {$item->produk->stok}).");
            }
        }

        $umkm             = Umkm::findOrFail($request->umkm_id);
        $alamatList       = AlamatCustomer::where('user_id', Auth::id())->orderByDesc('is_utama')->get();
        $metodePengiriman = MetodePengiriman::all();
        $subtotal         = $items->sum(fn($i) => $i->produk->harga * $i->jumlah);

        session(['checkout_data' => [
            'umkm_id'       => $request->umkm_id,
            'keranjang_ids' => $request->keranjang_ids,
        ]]);
        session(['checkout_return_url' => route('customer.checkout.resume')]);

        return view('customer.checkout.create', compact(
            'items', 'umkm', 'alamatList', 'metodePengiriman', 'subtotal',
        ))->with('keranjang_ids', $request->keranjang_ids);
    }

    public function resume(): View|RedirectResponse
    {
        $data = session('checkout_data');
        if (!$data) {
            return redirect()->route('customer.keranjang.index');
        }

        return $this->create(new Request($data));
    }

    // ── AJAX: hitung ongkir berdasarkan alamat yang dipilih ─────
    public function hitungOngkir(Request $request): JsonResponse
    {
        $request->validate([
            'umkm_id'   => ['required', 'exists:umkm,id'],
            'alamat_id' => ['required', 'exists:alamat_customer,id'],
        ]);

        $umkm   = Umkm::findOrFail($request->umkm_id);
        $alamat = AlamatCustomer::where('id', $request->alamat_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $ongkos = OngkosKirimTrayek::cariOngkos($umkm->kecamatan, $alamat->kecamatan);

        return response()->json([
            'ongkos'     => $ongkos ?? 0,
            'tersedia'   => $ongkos !== null,
            'ongkos_fmt' => 'Rp' . number_format($ongkos ?? 0, 0, ',', '.'),
        ]);
    }

    // ── AJAX: validasi & hitung diskon promo ────────────────
    public function applyPromo(Request $request): JsonResponse
    {
        $request->validate([
            'kode'     => ['required', 'string'],
            'umkm_id'  => ['required', 'exists:umkm,id'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $promo = Promo::where('kode', strtoupper(trim($request->kode)))->first();

        if (!$promo) {
            return response()->json(['valid' => false, 'message' => 'Kode promo tidak ditemukan.'], 422);
        }

        if (!$promo->isValid($request->subtotal, $request->umkm_id)) {
            // Beri pesan spesifik
            if (!$promo->is_aktif) {
                $msg = 'Promo ini tidak aktif.';
            } elseif (now()->toDateString() > $promo->berlaku_sampai->toDateString()) {
                $msg = 'Promo sudah kadaluarsa.';
            } elseif ($promo->kuota !== null && $promo->terpakai >= $promo->kuota) {
                $msg = 'Kuota promo sudah habis.';
            } elseif ($request->subtotal < $promo->min_belanja) {
                $msg = 'Minimum belanja Rp' . number_format($promo->min_belanja, 0, ',', '.') . ' untuk promo ini.';
            } elseif ($promo->umkm_id !== null && $promo->umkm_id != $request->umkm_id) {
                $msg = 'Promo ini tidak berlaku untuk toko ini.';
            } else {
                $msg = 'Promo tidak dapat digunakan.';
            }

            return response()->json(['valid' => false, 'message' => $msg], 422);
        }

        $diskon = $promo->hitungDiskon($request->subtotal);

        return response()->json([
            'valid'       => true,
            'promo_id'    => $promo->id,
            'nama_promo'  => $promo->nama_promo,
            'tipe'        => $promo->tipe,
            'nilai'       => $promo->nilai,
            'diskon'      => $diskon,
            'diskon_fmt'  => 'Rp' . number_format($diskon, 0, ',', '.'),
            'message'     => 'Promo berhasil diterapkan!',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'umkm_id'              => ['required', 'exists:umkm,id'],
            'keranjang_ids'        => ['required', 'array', 'min:1'],
            'keranjang_ids.*'      => ['integer', 'exists:keranjang,id'],
            'alamat_id'            => ['nullable', 'exists:alamat_customer,id'],
            'metode_pengiriman_id' => ['required', 'exists:metode_pengiriman,id'],
            'catatan_customer'     => ['nullable', 'string', 'max:500'],
            'promo_kode'           => ['nullable', 'string'],
        ]);

        $items = Keranjang::with('produk')
            ->where('user_id', Auth::id())
            ->whereIn('id', $validated['keranjang_ids'])
            ->whereHas('produk', fn($q) => $q->where('umkm_id', $validated['umkm_id']))
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('customer.keranjang.index')->with('error', 'Item tidak valid.');
        }

        foreach ($items as $item) {
            if ($item->jumlah > $item->produk->stok) {
                return redirect()->route('customer.keranjang.index')
                    ->with('error', "Stok \"{$item->produk->nama_produk}\" tidak mencukupi.");
            }
        }

        $umkm   = Umkm::findOrFail($validated['umkm_id']);
        $metode = MetodePengiriman::findOrFail($validated['metode_pengiriman_id']);

        if ($metode->kode !== 'ambil_ditempat' && empty($validated['alamat_id'])) {
            return back()->with('error', 'Alamat pengiriman wajib diisi untuk metode kurir.');
        }

        // Hitung ongkir real berdasarkan trayek
        $ongkosKirim = 0;
        if ($metode->kode !== 'ambil_ditempat') {
            $alamat = AlamatCustomer::findOrFail($validated['alamat_id']);
            $ongkosKirim = OngkosKirimTrayek::cariOngkos($umkm->kecamatan, $alamat->kecamatan) ?? 0;
        }

        $subtotal = $items->sum(fn($i) => $i->produk->harga * $i->jumlah);
        $diskon   = 0;
        $promo    = null;

        if (!empty($validated['promo_kode'])) {
            $promo = Promo::where('kode', strtoupper(trim($validated['promo_kode'])))->first();
            if ($promo && $promo->isValid($subtotal, $validated['umkm_id'])) {
                $diskon = $promo->hitungDiskon($subtotal);
            }
        }

        $totalHarga = max(0, $subtotal + $ongkosKirim - $diskon);

        DB::transaction(function () use ($validated, $items, $metode, $subtotal, $ongkosKirim, $diskon, $totalHarga, $promo) {
            $statusMenunggu = StatusPesanan::where('kode', 'menunggu_pembayaran')->firstOrFail();
            $statusPending  = StatusPembayaran::where('kode', 'pending')->firstOrFail();

            $noPesanan = 'TBN-' . strtoupper(uniqid());

            $pesanan = Pesanan::create([
                'no_pesanan'           => $noPesanan,
                'customer_id'          => Auth::id(),
                'umkm_id'              => $validated['umkm_id'],
                'alamat_id'            => $validated['alamat_id'] ?? null,
                'metode_pengiriman_id' => $validated['metode_pengiriman_id'],
                'promo_id'             => $promo?->id,
                'ongkos_kirim'         => $ongkosKirim,
                'diskon'               => $diskon,
                'total_harga'          => $totalHarga,
                'status_id'            => $statusMenunggu->id,
                'catatan_customer'     => $validated['catatan_customer'] ?? null,
            ]);

            foreach ($items as $item) {
                PesananDetail::create([
                    'pesanan_id'            => $pesanan->id,
                    'produk_id'             => $item->produk_id,
                    'nama_produk_snapshot'  => $item->produk->nama_produk,
                    'harga_satuan_snapshot' => $item->produk->harga,
                    'jumlah'                => $item->jumlah,
                    'subtotal'              => $item->produk->harga * $item->jumlah,
                ]);
            }

            if ($promo) {
                $promo->increment('terpakai');
            }

            Pembayaran::create([
                'pesanan_id'        => $pesanan->id,
                'midtrans_order_id' => $noPesanan . '-' . time(),
                'jumlah'            => $totalHarga,
                'status_id'         => $statusPending->id,
            ]);

            session(['checkout_pesanan_id' => $pesanan->id]);
        });

        return redirect()->route('customer.payment.show', session('checkout_pesanan_id'));
    }
}

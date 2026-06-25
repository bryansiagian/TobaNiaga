<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AlamatCustomer;
use App\Models\Keranjang;
use App\Models\MetodePengiriman;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\StatusPembayaran;
use App\Models\StatusPesanan;
use App\Models\Umkm;
use App\Services\MidtransService;
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
            'umkm_id'        => ['required', 'exists:umkm,id'],
            'keranjang_ids'  => ['required', 'array', 'min:1'],
            'keranjang_ids.*'=> ['integer', 'exists:keranjang,id'],
        ]);

        // Ambil item keranjang yang dipilih, pastikan milik user & dari UMKM yang benar
        $items = Keranjang::with(['produk.fotoProduk', 'produk.status'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $request->keranjang_ids)
            ->whereHas('produk', fn ($q) => $q->where('umkm_id', $request->umkm_id))
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('customer.keranjang.index')
                ->with('error', 'Item yang dipilih tidak valid.');
        }

        // Validasi stok ulang
        foreach ($items as $item) {
            if ($item->jumlah > $item->produk->stok) {
                return redirect()->route('customer.keranjang.index')
                    ->with('error', "Stok \"{$item->produk->nama_produk}\" tidak mencukupi (tersisa {$item->produk->stok}).");
            }
        }

        $umkm             = Umkm::findOrFail($request->umkm_id);
        $alamatList       = AlamatCustomer::where('user_id', Auth::id())->orderByDesc('is_utama')->get();
        $metodePengiriman = MetodePengiriman::all();

        $subtotal = $items->sum(fn ($i) => $i->produk->harga * $i->jumlah);

        return view('customer.checkout.create', compact(
            'items', 'umkm', 'alamatList', 'metodePengiriman', 'subtotal',
        ))->with('keranjang_ids', $request->keranjang_ids);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'umkm_id'          => ['required', 'exists:umkm,id'],
            'keranjang_ids'    => ['required', 'array', 'min:1'],
            'keranjang_ids.*'  => ['integer', 'exists:keranjang,id'],
            'alamat_id'        => ['nullable', 'exists:alamat_customer,id'],
            'metode_pengiriman_id' => ['required', 'exists:metode_pengiriman,id'],
            'catatan_customer' => ['nullable', 'string', 'max:500'],
        ]);

        $items = Keranjang::with('produk')
            ->where('user_id', Auth::id())
            ->whereIn('id', $validated['keranjang_ids'])
            ->whereHas('produk', fn ($q) => $q->where('umkm_id', $validated['umkm_id']))
            ->get();

        if ($items->isEmpty()) {
            return redirect()->route('customer.keranjang.index')
                ->with('error', 'Item tidak valid.');
        }

        // Validasi stok ulang sebelum buat pesanan
        foreach ($items as $item) {
            if ($item->jumlah > $item->produk->stok) {
                return redirect()->route('customer.keranjang.index')
                    ->with('error', "Stok \"{$item->produk->nama_produk}\" tidak mencukupi.");
            }
        }

        // Validasi alamat wajib jika metode pengiriman bukan ambil_ditempat
        $metode = MetodePengiriman::findOrFail($validated['metode_pengiriman_id']);
        if ($metode->kode !== 'ambil_ditempat' && empty($validated['alamat_id'])) {
            return back()->with('error', 'Alamat pengiriman wajib diisi untuk metode kurir.');
        }

        if (!empty($validated['alamat_id'])) {
            $alamat = AlamatCustomer::where('id', $validated['alamat_id'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        }

        DB::transaction(function () use ($validated, $items, $metode) {
            $statusMenunggu = StatusPesanan::where('kode', 'menunggu_pembayaran')->firstOrFail();
            $statusPending  = StatusPembayaran::where('kode', 'pending')->firstOrFail();

            $subtotal    = $items->sum(fn ($i) => $i->produk->harga * $i->jumlah);
            $ongkosKirim = $metode->kode === 'ambil_ditempat' ? 0 : 10000; // flat sementara
            $totalHarga  = $subtotal + $ongkosKirim;

            $noPesanan = 'TBN-' . strtoupper(uniqid());

            $pesanan = Pesanan::create([
                'no_pesanan'           => $noPesanan,
                'customer_id'          => Auth::id(),
                'umkm_id'              => $validated['umkm_id'],
                'alamat_id'            => $validated['alamat_id'] ?? null,
                'metode_pengiriman_id' => $validated['metode_pengiriman_id'],
                'ongkos_kirim'         => $ongkosKirim,
                'total_harga'          => $totalHarga,
                'status_id'            => $statusMenunggu->id,
                'catatan_customer'     => $validated['catatan_customer'] ?? null,
            ]);

            foreach ($items as $item) {
                PesananDetail::create([
                    'pesanan_id'              => $pesanan->id,
                    'produk_id'               => $item->produk_id,
                    'nama_produk_snapshot'    => $item->produk->nama_produk,
                    'harga_satuan_snapshot'   => $item->produk->harga,
                    'jumlah'                  => $item->jumlah,
                    'subtotal'                => $item->produk->harga * $item->jumlah,
                ]);
            }

            // Buat record pembayaran + ambil Snap token
            $midtransOrderId = $noPesanan . '-' . time();
            $user = Auth::user();

            Pembayaran::create([
                'pesanan_id'        => $pesanan->id,
                'midtrans_order_id' => $midtransOrderId,
                'jumlah'            => $totalHarga,
                'status_id'         => $statusPending->id,
            ]);

            Keranjang::whereIn('id', $items->pluck('id'))->delete();

            session(['checkout_pesanan_id' => $pesanan->id]);
        });

        return redirect()->route('customer.payment.show', session('checkout_pesanan_id'));
    }
}

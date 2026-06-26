<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use App\Models\Pengiriman;
use App\Models\StatusPengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesPesananController extends Controller
{
    public function index(Request $request): View
    {
        $umkm = Auth::user()->umkm;
        abort_unless($umkm, 403);

        $statusList = StatusPesanan::orderBy('urutan')->get();

        $query = $umkm->pesanan()
            ->with(['status', 'pembayaran.status', 'customer', 'detail', 'metodePengiriman', 'pengiriman.status']);

        // Filter status
        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('kode', $request->status));
        }

        // Filter pencarian
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('no_pesanan', 'like', "%{$cari}%")
                  ->orWhereHas('customer', fn($q2) => $q2->where('nama', 'like', "%{$cari}%"));
            });
        }

        // Filter tanggal
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $pesanan = $query->latest()->paginate(15)->withQueryString();

        // Hitung per status untuk badge tab
        $hitungStatus = $statusList->mapWithKeys(fn($s) => [
            $s->kode => $umkm->pesanan()->where('status_id', $s->id)->count()
        ]);

        return view('sales.pesanan.index', compact(
            'umkm', 'pesanan', 'statusList', 'hitungStatus'
        ));
    }

    public function update(Request $request, Pesanan $pesanan): \Illuminate\Http\RedirectResponse
    {
        abort_unless($pesanan->umkm_id === Auth::user()->umkm?->id, 403);

        $request->validate(['status_id' => 'required|exists:status_pesanan,id']);

        $pesanan->update(['status_id' => $request->status_id]);

        return back()->with('status', 'Status pesanan berhasil diperbarui.');
    }

    public function approve(Pesanan $pesanan): \Illuminate\Http\RedirectResponse
    {
        $umkm = Auth::user()->umkm;
        abort_unless($pesanan->umkm_id === $umkm?->id, 403);

        // Hanya bisa approve jika pembayaran sudah settlement
        $bayarKode = $pesanan->pembayaran?->status?->kode;
        abort_unless($bayarKode === 'settlement', 422, 'Pesanan belum dibayar.');

        // Hanya bisa approve dari status diproses (sudah lewat webhook)
        abort_unless($pesanan->status?->kode === 'diproses', 422, 'Status pesanan tidak valid.');

        // Cek metode pengiriman — kalau ambil_ditempat, langsung selesai
        $metode = $pesanan->metodePengiriman?->kode;

        if ($metode === 'ambil_ditempat') {
            $statusSelesai = StatusPesanan::where('kode', 'selesai')->firstOrFail();
            $pesanan->update(['status_id' => $statusSelesai->id]);
            return back()->with('status', 'Pesanan ditandai selesai (ambil di tempat).');
        }

        // Metode kurir — buat record pengiriman, masuk pool
        $statusMenunggu = StatusPengiriman::where('kode', 'menunggu_kurir')->firstOrFail();

        Pengiriman::firstOrCreate(
            ['pesanan_id' => $pesanan->id],
            ['status_id'  => $statusMenunggu->id]
        );

        $statusDikirim = StatusPesanan::where('kode', 'dikirim')->firstOrFail();
        $pesanan->update(['status_id' => $statusDikirim->id]);

        return back()->with('status', 'Pesanan diapprove. Menunggu kurir mengambil tugas.');
    }
}

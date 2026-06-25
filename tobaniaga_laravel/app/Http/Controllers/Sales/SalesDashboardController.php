<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\StatusPesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesDashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $umkm = $user->umkm;

        if (!$umkm) {
            return view('sales.dashboard', [
                'umkm'            => null,
                'totalProdukAktif'=> 0,
                'totalPesananBaru'=> 0,
                'totalDiproses'   => 0,
                'pendapatanBulan' => 0,
                'pesananTerbaru'  => collect(),
            ]);
        }

        $totalProdukAktif = $umkm->produk()
            ->whereHas('status', fn($q) => $q->where('kode', 'tersedia'))
            ->count();

        $statusMenunggu = StatusPesanan::where('kode', 'menunggu_pembayaran')->value('id');
        $statusDiproses = StatusPesanan::where('kode', 'diproses')->value('id');

        $totalPesananBaru = $umkm->pesanan()
            ->where('status_id', $statusMenunggu)
            ->count();

        $totalDiproses = $umkm->pesanan()
            ->where('status_id', $statusDiproses)
            ->count();

        $pendapatanBulan = $umkm->pesanan()
            ->whereHas('pembayaran', fn($q) => $q->whereHas('status', fn($q2) => $q2->where('kode', 'settlement')))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_harga');

        $pesananTerbaru = $umkm->pesanan()
            ->with(['status', 'detail', 'customer', 'pembayaran.status'])
            ->latest()
            ->take(5)
            ->get();

        return view('sales.dashboard', compact(
            'umkm',
            'totalProdukAktif',
            'totalPesananBaru',
            'totalDiproses',
            'pendapatanBulan',
            'pesananTerbaru',
        ));
    }
}

<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\PesananDetail;
use App\Models\StatusPesanan;
use App\Models\StatusPembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class SalesPendapatanController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $umkm = $user->umkm;

        abort_unless($umkm, 403);

        // ── Filter periode ──────────────────────────────────────
        $periode  = $request->get('periode', 'bulan_ini');
        $tahun    = $request->get('tahun', now()->year);
        $bulan    = $request->get('bulan', now()->month);

        [$tglMulai, $tglAkhir] = match($periode) {
            '7_hari'      => [now()->subDays(6)->startOfDay(),  now()->endOfDay()],
            '30_hari'     => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'bulan_ini'   => [now()->startOfMonth(),            now()->endOfMonth()],
            'bulan_lalu'  => [now()->subMonth()->startOfMonth(),now()->subMonth()->endOfMonth()],
            'tahun_ini'   => [now()->startOfYear(),             now()->endOfYear()],
            'custom'      => [
                Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth(),
                Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth(),
            ],
            default       => [now()->startOfMonth(), now()->endOfMonth()],
        };

        $statusSettlement = StatusPembayaran::where('kode', 'settlement')->value('id');
        $statusSelesai    = StatusPesanan::where('kode', 'selesai')->value('id');
        $statusDiproses   = StatusPesanan::where('kode', 'diproses')->value('id');
        $statusDikirim    = StatusPesanan::where('kode', 'dikirim')->value('id');
        $statusBatal      = StatusPesanan::where('kode', 'batal')->value('id');

        // ── Base query pesanan lunas milik UMKM ini ─────────────
        $baseQuery = fn() => $umkm->pesanan()
            ->whereHas('pembayaran', fn($q) => $q->where('status_id', $statusSettlement))
            ->whereBetween('created_at', [$tglMulai, $tglAkhir]);

        // ── KPI utama ───────────────────────────────────────────
        $totalPendapatan   = $baseQuery()->sum('total_harga');
        $totalPesananLunas = $baseQuery()->count();

        $totalPesananBatal = $umkm->pesanan()
            ->where('status_id', $statusBatal)
            ->whereBetween('created_at', [$tglMulai, $tglAkhir])
            ->count();

        $rataOrderValue    = $totalPesananLunas > 0
            ? $totalPendapatan / $totalPesananLunas
            : 0;

        $ratingRata        = $umkm->ulasan()
            ->whereBetween('created_at', [$tglMulai, $tglAkhir])
            ->avg('rating') ?? 0;

        // ── Perbandingan periode sebelumnya ─────────────────────
        $selisih      = $tglAkhir->diffInDays($tglMulai) + 1;
        $prevMulai    = $tglMulai->copy()->subDays($selisih);
        $prevAkhir    = $tglMulai->copy()->subDay()->endOfDay();

        $prevPendapatan = $umkm->pesanan()
            ->whereHas('pembayaran', fn($q) => $q->where('status_id', $statusSettlement))
            ->whereBetween('created_at', [$prevMulai, $prevAkhir])
            ->sum('total_harga');

        $growthPendapatan = $prevPendapatan > 0
            ? round((($totalPendapatan - $prevPendapatan) / $prevPendapatan) * 100, 1)
            : null;

        // ── Grafik pendapatan harian ─────────────────────────────
        $grafikHarian = $umkm->pesanan()
            ->whereHas('pembayaran', fn($q) => $q->where('status_id', $statusSettlement))
            ->whereBetween('created_at', [$tglMulai, $tglAkhir])
            ->select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('SUM(total_harga) as total'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // Isi hari yang kosong
        $grafikData = [];
        $current    = $tglMulai->copy();
        while ($current <= $tglAkhir) {
            $tgl = $current->toDateString();
            $grafikData[] = [
                'tanggal' => $current->translatedFormat('d M'),
                'total'   => $grafikHarian[$tgl]->total ?? 0,
                'jumlah'  => $grafikHarian[$tgl]->jumlah ?? 0,
            ];
            $current->addDay();
        }

        // ── Produk terlaris ─────────────────────────────────────
        $produkTerlaris = PesananDetail::whereHas('pesanan', function ($q) use ($umkm, $tglMulai, $tglAkhir, $statusSettlement) {
                $q->where('umkm_id', $umkm->id)
                  ->whereHas('pembayaran', fn($q2) => $q2->where('status_id', $statusSettlement))
                  ->whereBetween('created_at', [$tglMulai, $tglAkhir]);
            })
            ->select('produk_id', 'nama_produk_snapshot',
                DB::raw('SUM(jumlah) as total_terjual'),
                DB::raw('SUM(subtotal) as total_pendapatan')
            )
            ->groupBy('produk_id', 'nama_produk_snapshot')
            ->orderByDesc('total_terjual')
            ->take(10)
            ->get();

        // ── Pesanan terbaru ─────────────────────────────────────
        $pesananTerbaru = $umkm->pesanan()
            ->with(['status', 'pembayaran.status', 'customer'])
            ->whereBetween('created_at', [$tglMulai, $tglAkhir])
            ->latest()
            ->take(10)
            ->get();

        // ── Breakdown metode pembayaran ─────────────────────────
        $metodeBreakdown = $umkm->pesanan()
            ->whereHas('pembayaran', fn($q) => $q->where('status_id', $statusSettlement))
            ->whereBetween('pesanan.created_at', [$tglMulai, $tglAkhir])
            ->join('pembayaran', 'pesanan.id', '=', 'pembayaran.pesanan_id')
            ->select('pembayaran.metode', DB::raw('COUNT(*) as jumlah'), DB::raw('SUM(pesanan.total_harga) as total'))
            ->groupBy('pembayaran.metode')
            ->orderByDesc('total')
            ->get();

        // ── Ulasan terbaru ──────────────────────────────────────
        $ulasanTerbaru = $umkm->ulasan()
            ->with(['user', 'produk'])
            ->whereBetween('created_at', [$tglMulai, $tglAkhir])
            ->latest()
            ->take(5)
            ->get();

        return view('sales.pendapatan.index', compact(
            'umkm', 'periode', 'tahun', 'bulan',
            'tglMulai', 'tglAkhir',
            'totalPendapatan', 'totalPesananLunas', 'totalPesananBatal',
            'rataOrderValue', 'ratingRata',
            'growthPendapatan', 'prevPendapatan',
            'grafikData',
            'produkTerlaris',
            'pesananTerbaru',
            'metodeBreakdown',
            'ulasanTerbaru',
        ));
    }
}

<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class CourierPendapatanController extends Controller
{
    public function index(Request $request): View
    {
        $courier = Auth::user();

        // ── Filter periode ──────────────────────────────────────
        $periode = $request->get('periode', 'bulan_ini');
        $tahun   = $request->get('tahun', now()->year);
        $bulan   = $request->get('bulan', now()->month);

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

        // ── Base query pengiriman selesai milik kurir ini ───────
        $baseQuery = fn() => $courier->pengirimanSelesai()
            ->whereHas('status', fn($q) => $q->where('kode', 'selesai'))
            ->whereBetween('waktu_selesai', [$tglMulai, $tglAkhir]);

        $totalPengirimanSelesai = (clone $baseQuery())->count();

        $totalPendapatan = (clone $baseQuery())
            ->with('pesanan')
            ->get()
            ->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0);

        $rataOngkir = $totalPengirimanSelesai > 0
            ? $totalPendapatan / $totalPengirimanSelesai
            : 0;

        // ── Perbandingan periode sebelumnya ─────────────────────
        $selisih   = $tglAkhir->diffInDays($tglMulai) + 1;
        $prevMulai = $tglMulai->copy()->subDays($selisih);
        $prevAkhir = $tglMulai->copy()->subDay()->endOfDay();

        $prevPendapatan = $courier->pengirimanSelesai()
            ->whereHas('status', fn($q) => $q->where('kode', 'selesai'))
            ->whereBetween('waktu_selesai', [$prevMulai, $prevAkhir])
            ->with('pesanan')
            ->get()
            ->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0);

        $growthPendapatan = $prevPendapatan > 0
            ? round((($totalPendapatan - $prevPendapatan) / $prevPendapatan) * 100, 1)
            : null;

        // ── Grafik pendapatan harian ─────────────────────────────
        $grafikHarian = (clone $baseQuery())
            ->with('pesanan')
            ->get()
            ->groupBy(fn($p) => $p->waktu_selesai->toDateString());

        $grafikData = [];
        $current    = $tglMulai->copy();
        while ($current <= $tglAkhir) {
            $tgl   = $current->toDateString();
            $group = $grafikHarian->get($tgl, collect());
            $grafikData[] = [
                'tanggal' => $current->translatedFormat('d M'),
                'total'   => $group->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0),
                'jumlah'  => $group->count(),
            ];
            $current->addDay();
        }

        // ── Pengiriman terbaru ───────────────────────────────────
        $pengirimanTerbaru = (clone $baseQuery())
            ->with(['pesanan.customer', 'pesanan.umkm', 'status'])
            ->latest('waktu_selesai')
            ->take(10)
            ->get();

        return view('courier.pendapatan.index', compact(
            'periode', 'tahun', 'bulan', 'tglMulai', 'tglAkhir',
            'totalPendapatan', 'totalPengirimanSelesai', 'rataOngkir',
            'growthPendapatan', 'grafikData', 'pengirimanTerbaru',
        ));
    }
}

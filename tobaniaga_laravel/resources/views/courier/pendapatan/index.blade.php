@extends('layouts.backoffice')

@section('title', 'Analisis Pendapatan')
@section('role_label', 'Kurir')
@section('page_title', 'Analisis Pendapatan')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')

@php
    $periodeLabel = match($periode) {
        '7_hari'     => '7 Hari Terakhir',
        '30_hari'    => '30 Hari Terakhir',
        'bulan_ini'  => 'Bulan Ini',
        'bulan_lalu' => 'Bulan Lalu',
        'tahun_ini'  => 'Tahun Ini',
        'custom'     => $tglMulai->translatedFormat('F Y'),
        default      => 'Bulan Ini',
    };
@endphp

{{-- Header --}}
<div class="mb-8 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Pendapatan Kurir</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Analisis Pendapatan</h2>
        <p class="text-sm text-ink/50 mt-1">{{ $tglMulai->translatedFormat('d M Y') }} — {{ $tglAkhir->translatedFormat('d M Y') }}</p>
    </div>

    <form method="GET" action="{{ route('courier.pendapatan.index') }}" class="flex flex-wrap items-center gap-2">
        <select name="periode" onchange="this.form.submit()"
                class="border border-lake-900/15 rounded-lg px-3 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
            <option value="7_hari"     {{ $periode === '7_hari'     ? 'selected' : '' }}>7 Hari Terakhir</option>
            <option value="30_hari"    {{ $periode === '30_hari'    ? 'selected' : '' }}>30 Hari Terakhir</option>
            <option value="bulan_ini"  {{ $periode === 'bulan_ini'  ? 'selected' : '' }}>Bulan Ini</option>
            <option value="bulan_lalu" {{ $periode === 'bulan_lalu' ? 'selected' : '' }}>Bulan Lalu</option>
            <option value="tahun_ini"  {{ $periode === 'tahun_ini'  ? 'selected' : '' }}>Tahun Ini</option>
            <option value="custom"     {{ $periode === 'custom'     ? 'selected' : '' }}>Pilih Bulan</option>
        </select>

        @if($periode === 'custom')
        <select name="bulan" class="border border-lake-900/15 rounded-lg px-3 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
            @foreach(range(1, 12) as $m)
            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
            </option>
            @endforeach
        </select>
        <select name="tahun" class="border border-lake-900/15 rounded-lg px-3 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
            @foreach(range(now()->year, now()->year - 3) as $y)
            <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
            Tampilkan
        </button>
        @endif
    </form>
</div>

{{-- ── KPI Cards ───────────────────────────────────────────────── --}}
<div class="grid sm:grid-cols-3 gap-4 mb-6">

    <div class="bg-lake-900 rounded-xl p-5 text-paper">
        <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Total Pendapatan Ongkir</p>
        <p class="font-display text-2xl font-medium leading-tight">
            Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
        </p>
        <div class="mt-3 flex items-center gap-1.5">
            @if($growthPendapatan !== null)
                @if($growthPendapatan >= 0)
                    <span class="inline-flex items-center gap-1 text-xs text-green-300 font-medium">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                        +{{ $growthPendapatan }}%
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs text-red-300 font-medium">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        {{ $growthPendapatan }}%
                    </span>
                @endif
                <span class="text-xs text-paper/30">vs periode sebelumnya</span>
            @else
                <span class="text-xs text-paper/30">Tidak ada data sebelumnya</span>
            @endif
        </div>
    </div>

    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Pengiriman Selesai</p>
        <p class="font-display text-3xl font-medium text-lake-900">{{ $totalPengirimanSelesai }}</p>
        <p class="text-xs text-ink/40 mt-3">Di periode ini</p>
    </div>

    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Rata-rata Ongkir</p>
        <p class="font-display text-2xl font-medium text-lake-900">
            Rp{{ number_format($rataOngkir, 0, ',', '.') }}
        </p>
        <p class="text-xs text-ink/40 mt-3">Per pengiriman selesai</p>
    </div>
</div>

{{-- ── Grafik ──────────────────────────────────────────────────── --}}
<div class="bg-paper border border-lake-900/10 rounded-xl p-5 mb-4">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-display text-base font-medium text-lake-900">Tren Pendapatan</h3>
        <span class="font-mono text-xs text-ink/40">{{ $periodeLabel }}</span>
    </div>
    @if(count($grafikData) > 0 && $totalPendapatan > 0)
        <canvas id="grafikPendapatan" height="200"></canvas>
    @else
        <div class="flex items-center justify-center h-48 text-sm text-ink/30">
            Belum ada pengiriman selesai di periode ini.
        </div>
    @endif
</div>

{{-- ── Pengiriman Terbaru ─────────────────────────────────────── --}}
<div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-lake-900/8 flex items-center justify-between">
        <h3 class="font-display text-base font-medium text-lake-900">Pengiriman di Periode Ini</h3>
        <span class="font-mono text-xs text-ink/40">{{ $pengirimanTerbaru->count() }} terakhir</span>
    </div>
    @if($pengirimanTerbaru->isEmpty())
        <div class="px-5 py-12 text-center text-sm text-ink/30">Belum ada pengiriman selesai.</div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-lake-900/6 text-left">
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">No. Pesanan</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Toko</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Penerima</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Selesai</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5 text-right">Ongkir</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lake-900/5">
                @foreach($pengirimanTerbaru as $p)
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-ink/70">{{ $p->pesanan->no_pesanan ?? '—' }}</td>
                    <td class="px-5 py-3 text-ink/70">{{ $p->pesanan->umkm->nama_umkm ?? '—' }}</td>
                    <td class="px-5 py-3 text-ink/70">{{ $p->nama_penerima ?? ($p->pesanan->customer->nama ?? '—') }}</td>
                    <td class="px-5 py-3 text-ink/40 text-xs">{{ $p->waktu_selesai?->translatedFormat('d M, H:i') ?? '—' }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-lake-900">
                        Rp{{ number_format($p->pesanan->ongkos_kirim ?? 0, 0, ',', '.') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('grafikPendapatan');
    if (!canvas) return;

    const grafikData = @json($grafikData);
    const labels = grafikData.map(d => d.tanggal);
    const totals = grafikData.map(d => d.total);

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Pendapatan',
                data: totals,
                borderColor: '#0A2C2D',
                backgroundColor: 'rgba(10,44,45,0.06)',
                borderWidth: 2,
                pointRadius: totals.length > 20 ? 0 : 3,
                pointBackgroundColor: '#0A2C2D',
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => 'Rp' + ctx.parsed.y.toLocaleString('id-ID'),
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: 'JetBrains Mono', size: 10 }, color: '#2A262266', maxTicksLimit: 10 }
                },
                y: {
                    grid: { color: 'rgba(10,44,45,0.05)' },
                    ticks: {
                        font: { family: 'JetBrains Mono', size: 10 },
                        color: '#2A262266',
                        callback: v => 'Rp' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v >= 1000 ? (v/1000).toFixed(0)+'rb' : v)
                    }
                }
            }
        }
    });
});
</script>
@endpush

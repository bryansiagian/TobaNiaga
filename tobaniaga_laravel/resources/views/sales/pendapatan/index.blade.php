@extends('layouts.backoffice')

@section('title', 'Analisis Pendapatan')
@section('role_label', 'Sales')
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
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">{{ $umkm->nama_umkm }}</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Analisis Pendapatan</h2>
        <p class="text-sm text-ink/50 mt-1">{{ $tglMulai->translatedFormat('d M Y') }} — {{ $tglAkhir->translatedFormat('d M Y') }}</p>
    </div>

    {{-- Filter periode --}}
    <form method="GET" action="{{ route('sales.pendapatan.index') }}" class="flex flex-wrap items-center gap-2">
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
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Pendapatan --}}
    <div class="bg-lake-900 rounded-xl p-5 text-paper">
        <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Total Pendapatan</p>
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

    {{-- Pesanan Lunas --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Pesanan Lunas</p>
        <p class="font-display text-3xl font-medium text-lake-900">{{ $totalPesananLunas }}</p>
        <p class="text-xs text-ink/40 mt-3">
            {{ $totalPesananBatal }} pesanan dibatalkan
        </p>
    </div>

    {{-- Rata-rata nilai order --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Rata-rata Order</p>
        <p class="font-display text-2xl font-medium text-lake-900">
            Rp{{ number_format($rataOrderValue, 0, ',', '.') }}
        </p>
        <p class="text-xs text-ink/40 mt-3">Per transaksi berhasil</p>
    </div>

    {{-- Rating --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Rating Toko</p>
        <div class="flex items-end gap-2">
            <p class="font-display text-3xl font-medium text-lake-900">{{ number_format($ratingRata, 1) }}</p>
            <p class="text-sm text-ink/40 mb-1">/ 5.0</p>
        </div>
        <div class="flex gap-0.5 mt-2">
            @for($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= round($ratingRata) ? 'text-ulos-gold' : 'text-ink/15' }}"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
        </div>
    </div>
</div>

{{-- ── Grafik + Metode Bayar ──────────────────────────────────── --}}
<div class="grid lg:grid-cols-[1fr_280px] gap-4 mb-4">

    {{-- Grafik pendapatan harian --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-display text-base font-medium text-lake-900">Tren Pendapatan</h3>
            <span class="font-mono text-xs text-ink/40">{{ $periodeLabel }}</span>
        </div>
        @if(count($grafikData) > 0 && $totalPendapatan > 0)
            <canvas id="grafikPendapatan" height="200"></canvas>
        @else
            <div class="flex items-center justify-center h-48 text-sm text-ink/30">
                Belum ada transaksi di periode ini.
            </div>
        @endif
    </div>

    {{-- Metode pembayaran --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <h3 class="font-display text-base font-medium text-lake-900 mb-4">Metode Pembayaran</h3>
        @if($metodeBreakdown->isEmpty())
            <div class="flex items-center justify-center h-32 text-sm text-ink/30">Belum ada data.</div>
        @else
            <div class="space-y-3">
                @php $maxMetode = $metodeBreakdown->max('total'); @endphp
                @foreach($metodeBreakdown as $m)
                @php
                    $pct   = $maxMetode > 0 ? ($m->total / $maxMetode) * 100 : 0;
                    $label = match(true) {
                        str_contains($m->metode ?? '', 'bca')     => 'VA BCA',
                        str_contains($m->metode ?? '', 'bni')     => 'VA BNI',
                        str_contains($m->metode ?? '', 'bri')     => 'VA BRI',
                        str_contains($m->metode ?? '', 'mandiri') => 'VA Mandiri',
                        str_contains($m->metode ?? '', 'qris')    => 'QRIS',
                        str_contains($m->metode ?? '', 'gopay')   => 'GoPay',
                        str_contains($m->metode ?? '', 'credit')  => 'Kartu Kredit',
                        default => strtoupper($m->metode ?? '—'),
                    };
                @endphp
                <div>
                    <div class="flex justify-between text-xs mb-1">
                        <span class="text-ink/70 font-medium">{{ $label }}</span>
                        <span class="text-ink/40">{{ $m->jumlah }}x · Rp{{ number_format($m->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-1.5 bg-lake-50 rounded-full overflow-hidden">
                        <div class="h-full bg-lake-900 rounded-full" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Produk Terlaris + Ulasan ───────────────────────────────── --}}
<div class="grid lg:grid-cols-[1fr_340px] gap-4 mb-4">

    {{-- Produk terlaris --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-lake-900/8">
            <h3 class="font-display text-base font-medium text-lake-900">Produk Terlaris</h3>
        </div>
        @if($produkTerlaris->isEmpty())
            <div class="px-5 py-12 text-center text-sm text-ink/30">Belum ada penjualan di periode ini.</div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/6 text-left">
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">#</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Produk</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5 text-right">Terjual</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5 text-right">Pendapatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/5">
                    @foreach($produkTerlaris as $i => $p)
                    <tr class="{{ $i === 0 ? 'bg-lake-50/50' : '' }}">
                        <td class="px-5 py-3 text-ink/30 font-mono text-xs">{{ $i + 1 }}</td>
                        <td class="px-5 py-3 text-ink/80 font-medium">{{ $p->nama_produk_snapshot }}</td>
                        <td class="px-5 py-3 text-right text-ink/60">{{ number_format($p->total_terjual, 0, ',', '.') }} pcs</td>
                        <td class="px-5 py-3 text-right font-semibold text-lake-900">
                            Rp{{ number_format($p->total_pendapatan, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Ulasan terbaru --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-lake-900/8">
            <h3 class="font-display text-base font-medium text-lake-900">Ulasan Terbaru</h3>
        </div>
        @if($ulasanTerbaru->isEmpty())
            <div class="px-5 py-12 text-center text-sm text-ink/30">Belum ada ulasan.</div>
        @else
            <div class="divide-y divide-lake-900/6">
                @foreach($ulasanTerbaru as $u)
                <div class="px-5 py-4">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <p class="text-sm font-medium text-ink">
                            {{ $u->is_anonim ? 'Anonim' : ($u->user->nama ?? '—') }}
                        </p>
                        <div class="flex gap-0.5 flex-shrink-0">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-3 h-3 {{ $i <= $u->rating ? 'text-ulos-gold' : 'text-ink/15' }}"
                                     fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>
                    </div>
                    <p class="text-xs text-ink/40 mb-1.5">{{ $u->produk->nama_produk ?? '—' }} · {{ $u->created_at->diffForHumans() }}</p>
                    @if($u->komentar)
                        <p class="text-xs text-ink/60 leading-relaxed line-clamp-2">{{ $u->komentar }}</p>
                    @endif
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- ── Transaksi Terbaru ───────────────────────────────────────── --}}
<div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-lake-900/8 flex items-center justify-between">
        <h3 class="font-display text-base font-medium text-lake-900">Transaksi di Periode Ini</h3>
        <span class="font-mono text-xs text-ink/40">{{ $pesananTerbaru->count() }} terakhir</span>
    </div>
    @if($pesananTerbaru->isEmpty())
        <div class="px-5 py-12 text-center text-sm text-ink/30">Belum ada transaksi.</div>
    @else
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-lake-900/6 text-left">
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">No. Pesanan</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Pembeli</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Waktu</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5">Status</th>
                    <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-2.5 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lake-900/5">
                @foreach($pesananTerbaru as $p)
                @php
                    $kode  = $p->status->kode ?? '';
                    $badge = match($kode) {
                        'menunggu_pembayaran' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'diproses'            => 'bg-blue-50 text-blue-700 border-blue-200',
                        'dikirim'             => 'bg-purple-50 text-purple-700 border-purple-200',
                        'selesai'             => 'bg-green-50 text-green-700 border-green-200',
                        'batal'               => 'bg-red-50 text-red-700 border-red-200',
                        default               => 'bg-gray-50 text-gray-600 border-gray-200',
                    };
                    $bayarKode = $p->pembayaran?->status?->kode ?? '';
                @endphp
                <tr>
                    <td class="px-5 py-3 font-mono text-xs text-ink/70">{{ $p->no_pesanan }}</td>
                    <td class="px-5 py-3 text-ink/70">{{ $p->customer->nama ?? '—' }}</td>
                    <td class="px-5 py-3 text-ink/40 text-xs">{{ $p->created_at->translatedFormat('d M, H:i') }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge }}">
                            {{ $p->status->label ?? '—' }}
                        </span>
                        @if($bayarKode === 'settlement')
                        <span class="ml-1 text-xs text-green-600 font-medium">✓ Lunas</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-lake-900">
                        Rp{{ number_format($p->total_harga, 0, ',', '.') }}
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
    const labels  = grafikData.map(d => d.tanggal);
    const totals  = grafikData.map(d => d.total);

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

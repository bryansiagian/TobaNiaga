@extends('layouts.backoffice')
@section('title', 'Pencairan Dana — TobaNiaga')
@section('page_title', 'Pencairan Dana')

@section('content')
<div x-data="pencairanPage()" x-init="init()">

    {{-- Flash --}}
    @if(session('status'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('status') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Stat saldo --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Saldo Tersedia</p>
            <p class="font-display text-2xl font-medium text-lake-900">Rp{{ number_format($saldoTersedia, 0, ',', '.') }}</p>
            <p class="text-xs text-ink/50 mt-1">{{ $pengirimanEligible->count() }} pengiriman siap dicairkan</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Sedang Diproses</p>
            <p class="font-display text-2xl font-medium text-ulos-gold">Rp{{ number_format($totalDiproses, 0, ',', '.') }}</p>
            <p class="text-xs text-ink/50 mt-1">Menunggu transfer admin</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Total Dicairkan</p>
            <p class="font-display text-2xl font-medium text-green-700">Rp{{ number_format($totalDicairkan, 0, ',', '.') }}</p>
            <p class="text-xs text-ink/50 mt-1">Sepanjang waktu</p>
        </div>
    </div>

    @if($rekening->isEmpty())
    <div class="mb-6 px-5 py-4 rounded-xl bg-yellow-50 border border-yellow-200 flex items-start gap-3">
        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-yellow-700">Belum ada rekening bank</p>
            <p class="text-xs text-yellow-600 mt-0.5">
                Tambahkan rekening dulu di menu
                <a href="{{ route('courier.rekening.index') }}" class="underline font-medium">Rekening Bank</a>
                sebelum bisa mengajukan pencairan.
            </p>
        </div>
    </div>
    @endif

    {{-- Form ajukan pencairan --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
            <h2 class="font-display text-base font-medium text-lake-900">Ajukan Pencairan</h2>
            @if($pengirimanEligible->isNotEmpty())
            <button type="button" @click="pilihSemua()"
                    class="text-xs text-lake-800 font-medium hover:underline"
                    x-text="semuaTerpilih ? 'Batalkan Semua' : 'Pilih Semua'">
            </button>
            @endif
        </div>

        @if($pengirimanEligible->isEmpty())
        <div class="px-6 py-14 text-center">
            <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-sm text-ink/40">Belum ada pengiriman yang siap dicairkan.</p>
            <p class="text-xs text-ink/30 mt-1">Pengiriman akan muncul di sini setelah berstatus "Terkirim".</p>
        </div>
        @else
        <form action="{{ route('courier.pencairan.store') }}" method="POST">
            @csrf

            {{-- Pilih rekening --}}
            <div class="px-6 py-4 border-b border-lake-900/8">
                <label class="block text-xs font-medium text-lake-900 mb-2">Cairkan ke Rekening</label>
                <div class="space-y-2">
                    @foreach($rekening as $r)
                    <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                           :class="rekeningId == '{{ $r->id }}' ? 'border-lake-800 bg-lake-50' : 'border-lake-900/10 hover:bg-lake-50'">
                        <input type="radio" name="rekening_bank_kurir_id" value="{{ $r->id }}"
                               x-model="rekeningId" class="accent-lake-800">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-lake-900">
                                {{ $r->nama_bank }}
                                @if($r->is_utama)<span class="text-[10px] ml-1 px-1.5 py-0.5 rounded bg-lake-800 text-paper">UTAMA</span>@endif
                            </p>
                            <p class="text-xs text-ink/50">{{ $r->nama_pemilik }} · {{ $r->no_rekening }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Daftar pengiriman --}}
            <div class="divide-y divide-lake-900/6 max-h-96 overflow-y-auto">
                @foreach($pengirimanEligible as $p)
                <label class="flex items-center justify-between gap-4 px-6 py-3.5 cursor-pointer hover:bg-lake-50/50">
                    <div class="flex items-center gap-3 min-w-0">
                        <input type="checkbox" name="pengiriman_ids[]" value="{{ $p->id }}"
                               x-model="terpilih"
                               class="w-4 h-4 accent-lake-800 rounded flex-shrink-0">
                        <div class="min-w-0">
                            <p class="text-sm font-mono font-medium text-ink truncate">{{ $p->pesanan->no_pesanan ?? '—' }}</p>
                            <p class="text-xs text-ink/40">{{ $p->waktu_selesai?->format('d M Y') ?? '—' }} · {{ $p->pesanan->umkm->nama_umkm ?? '—' }}</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-lake-900 flex-shrink-0">
                        Rp{{ number_format($p->pesanan->ongkos_kirim ?? 0, 0, ',', '.') }}
                    </p>
                </label>
                @endforeach
            </div>

            {{-- Footer total + submit --}}
            <div class="px-6 py-4 bg-lake-50 flex items-center justify-between">
                <div>
                    <p class="text-xs text-ink/50">Total Diajukan</p>
                    <p class="text-lg font-bold text-lake-900" x-text="formatRupiah(totalTerpilih)"></p>
                </div>
                <button type="submit"
                        :disabled="terpilih.length === 0 || !rekeningId"
                        class="px-6 py-2.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                    Ajukan Pencairan
                </button>
            </div>
        </form>
        @endif
    </div>

    {{-- Riwayat pencairan --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-lake-900/8">
            <h2 class="font-display text-base font-medium text-lake-900">Riwayat Pengajuan</h2>
        </div>

        @if($riwayat->isEmpty())
        <div class="px-6 py-14 text-center">
            <p class="text-sm text-ink/40">Belum ada riwayat pencairan.</p>
        </div>
        @else
        <div class="divide-y divide-lake-900/6">
            @foreach($riwayat as $pc)
            @php
                $badge = match($pc->status) {
                    'diajukan' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Diproses'],
                    'diproses' => ['bg-blue-50 text-blue-700 border-blue-200', 'Sedang Diproses'],
                    'selesai'  => ['bg-green-50 text-green-700 border-green-200', 'Selesai'],
                    'ditolak'  => ['bg-red-50 text-red-700 border-red-200', 'Ditolak'],
                    default    => ['bg-gray-50 text-gray-600 border-gray-200', $pc->status],
                };
            @endphp
            <div class="px-6 py-4 flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-mono font-semibold text-ink">{{ $pc->no_pencairan }}</p>
                    <p class="text-xs text-ink/40 mt-0.5">
                        {{ $pc->created_at->format('d M Y, H:i') }} · {{ $pc->detail->count() }} pengiriman · {{ $pc->rekeningBankKurir->nama_bank ?? '—' }}
                    </p>
                    @if($pc->status === 'ditolak' && $pc->catatan_admin)
                    <p class="text-xs text-red-600 mt-1">Alasan: {{ $pc->catatan_admin }}</p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                    <span class="text-xs px-2 py-0.5 rounded-full border {{ $badge[0] }}">{{ $badge[1] }}</span>
                    <p class="text-sm font-semibold text-lake-900">Rp{{ number_format($pc->jumlah, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>
        <div class="px-6 py-4 border-t border-lake-900/8">{{ $riwayat->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function pencairanPage() {
    return {
        terpilih: [],
        rekeningId: '{{ $rekening->firstWhere('is_utama', true)?->id ?? $rekening->first()?->id ?? '' }}',
        semuaTerpilih: false,
        pengirimanData: {!! $pengirimanEligible->mapWithKeys(fn($p) => [$p->id => $p->pesanan->ongkos_kirim ?? 0])->toJson() !!},

        init() {},

        get totalTerpilih() {
            return this.terpilih.reduce((sum, id) => sum + (this.pengirimanData[id] || 0), 0);
        },

        pilihSemua() {
            if (this.semuaTerpilih) {
                this.terpilih = [];
            } else {
                this.terpilih = Object.keys(this.pengirimanData).map(Number);
            }
            this.semuaTerpilih = !this.semuaTerpilih;
        },

        formatRupiah(nilai) {
            return 'Rp ' + nilai.toLocaleString('id-ID');
        },
    }
}
</script>
@endpush
@endsection

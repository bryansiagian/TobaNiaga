@extends('layouts.backoffice')
@section('title', 'Detail Dokumen — TobaNiaga')
@section('page_title', 'Detail Dokumen')

@section('content')
<div class="max-w-2xl">

    <a href="{{ route('admin.verifikasi.dokumen.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-ink/50 hover:text-ink mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    @if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    @php
        $kode  = $user->statusVerifikasiDokumen?->kode;
        $badge = match($kode) {
            'pending'  => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Verifikasi'],
            'verified' => ['bg-green-50 text-green-700 border-green-200',   'Terverifikasi'],
            'rejected' => ['bg-red-50 text-red-700 border-red-200',         'Ditolak'],
            default    => ['bg-gray-50 text-gray-600 border-gray-200',      $kode],
        };
        $role = $user->getRoleNames()->first();
    @endphp

    {{-- Info user --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden mb-6">
        <div class="bg-lake-900 text-paper px-6 py-5 flex items-start justify-between">
            <div>
                <p class="text-xs text-paper/50 mb-0.5">Pemohon</p>
                <p class="font-display text-lg font-medium">{{ $user->nama }}</p>
                <p class="text-xs text-paper/50 mt-1">{{ $user->email }}</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="text-[10px] font-mono px-2 py-1 rounded bg-paper/10 text-paper uppercase">{{ $role }}</span>
                <span class="text-xs px-2.5 py-1 rounded-full border {{ $badge[0] }}">{{ $badge[1] }}</span>
            </div>
        </div>

        {{-- Data pribadi --}}
        <div class="px-6 py-5 grid grid-cols-2 gap-4 border-b border-lake-900/8">
            <div>
                <p class="text-xs text-ink/40 mb-1">NIK</p>
                <p class="text-sm font-mono font-medium text-ink">{{ $user->nikMasked() ?? '—' }}</p>
                <p class="text-[11px] text-ink/30 mt-0.5">Tampil sebagian untuk keamanan</p>
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-1">Tanggal Lahir</p>
                <p class="text-sm text-ink">{{ $user->tanggal_lahir?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-1">No. HP</p>
                <p class="text-sm text-ink">{{ $user->no_hp ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-1">Alamat KTP</p>
                <p class="text-sm text-ink">{{ $user->alamat_ktp ?? '—' }}</p>
            </div>
        </div>

        {{-- Preview dokumen --}}
        <div class="px-6 py-5 grid grid-cols-2 gap-4 border-b border-lake-900/8">
            <div>
                <p class="text-xs text-ink/40 mb-2">Foto KTP</p>
                @if($user->foto_ktp)
                <a href="{{ route('dokumen.lihat', [$user, 'ktp']) }}" target="_blank"
                   class="block rounded-lg overflow-hidden border border-lake-900/10 hover:opacity-90 transition-opacity">
                    <img src="{{ route('dokumen.lihat', [$user, 'ktp']) }}"
                         alt="Foto KTP {{ $user->nama }}"
                         class="w-full h-36 object-cover">
                </a>
                <p class="text-[11px] text-ink/40 mt-1">Klik untuk buka penuh</p>
                @else
                <div class="h-36 rounded-lg border border-dashed border-lake-900/20 flex items-center justify-center">
                    <p class="text-xs text-ink/30">Belum diupload</p>
                </div>
                @endif
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-2">Foto KK</p>
                @if($user->foto_kk)
                <a href="{{ route('dokumen.lihat', [$user, 'kk']) }}" target="_blank"
                   class="block rounded-lg overflow-hidden border border-lake-900/10 hover:opacity-90 transition-opacity">
                    <img src="{{ route('dokumen.lihat', [$user, 'kk']) }}"
                         alt="Foto KK {{ $user->nama }}"
                         class="w-full h-36 object-cover">
                </a>
                <p class="text-[11px] text-ink/40 mt-1">Klik untuk buka penuh</p>
                @else
                <div class="h-36 rounded-lg border border-dashed border-lake-900/20 flex items-center justify-center">
                    <p class="text-xs text-ink/30">Belum diupload</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Data UMKM kalau sales --}}
        @if($user->umkm)
        <div class="px-6 py-5 border-b border-lake-900/8">
            <p class="text-xs text-ink/40 mb-2">UMKM Terkait</p>
            <p class="text-sm font-semibold text-lake-900">{{ $user->umkm->nama_umkm }}</p>
            <p class="text-xs text-ink/50 mt-0.5">{{ $user->umkm->kecamatan }}, {{ $user->umkm->kabupaten }}</p>
        </div>
        @endif

        @if($kode === 'rejected' && $user->catatan_penolakan_dokumen)
        <div class="px-6 py-4 bg-red-50 border-b border-lake-900/8">
            <p class="text-xs text-ink/40 mb-1">Alasan Penolakan Sebelumnya</p>
            <p class="text-sm text-red-700">{{ $user->catatan_penolakan_dokumen }}</p>
        </div>
        @endif
    </div>

    {{-- Actions --}}
    @if($kode === 'pending')
    <div class="flex gap-3">
        <form action="{{ route('admin.verifikasi.dokumen.approve', $user) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit"
                    class="w-full py-2.5 bg-green-700 text-paper text-sm font-semibold rounded-lg hover:bg-green-800 transition-colors">
                Setujui Dokumen
            </button>
        </form>
        <button type="button" onclick="document.getElementById('modalTolak').showModal()"
                class="px-6 py-2.5 border border-ulos-maroon/30 text-ulos-maroon text-sm font-medium rounded-lg hover:bg-ulos-maroon/5 transition-colors">
            Tolak
        </button>
    </div>

    <dialog id="modalTolak" class="rounded-xl p-0 backdrop:bg-ink/40 w-full max-w-sm">
        <form action="{{ route('admin.verifikasi.dokumen.reject', $user) }}" method="POST" class="p-5 space-y-3">
            @csrf
            <h3 class="font-display text-base font-medium text-lake-900">Tolak Dokumen</h3>
            <p class="text-xs text-ink/50">Alasan akan ditampilkan ke pengguna di dashboard mereka.</p>
            <textarea name="catatan_penolakan_dokumen" rows="3" required
                      placeholder="Contoh: Foto KTP buram, mohon upload ulang dengan kualitas lebih baik."
                      class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none"></textarea>
            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 py-2 bg-ulos-maroon text-paper text-sm font-medium rounded-lg hover:opacity-90">
                    Tolak Dokumen
                </button>
                <button type="button" onclick="document.getElementById('modalTolak').close()"
                        class="px-4 py-2 border border-lake-200 text-lake-900 text-sm rounded-lg hover:bg-lake-50">
                    Batal
                </button>
            </div>
        </form>
    </dialog>
    @endif

</div>
@endsection

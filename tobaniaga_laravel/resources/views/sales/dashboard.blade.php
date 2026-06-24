@extends('layouts.backoffice')

@section('title', 'Dashboard Sales')
@section('role_label', 'Sales / UMKM')
@section('page_title', 'Dashboard Sales')

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Selamat datang kembali</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">{{ auth()->user()->nama ?? 'Sales' }}</h2>
    </div>

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Produk Aktif</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Belum ada produk</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Pesanan Masuk</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Belum ada pesanan</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Diproses</p>
            <p class="font-display text-3xl font-medium text-ulos-maroon">—</p>
            <p class="text-xs text-ink/50 mt-1">Perlu konfirmasi</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Pendapatan</p>
            <p class="font-display text-3xl font-medium text-lake-900">Rp —</p>
            <p class="text-xs text-ink/50 mt-1">Bulan ini</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_320px] gap-6">

        {{-- Pesanan terbaru --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <h3 class="font-display text-base font-medium text-lake-900">Pesanan Terbaru</h3>
                <a href="#" class="font-mono text-xs text-lake-800 hover:underline">Lihat semua</a>
            </div>
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Belum ada pesanan masuk.</p>
                <p class="text-xs text-ink/30 mt-1">Pesanan dari pembeli akan muncul di sini.</p>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="space-y-4">
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Aksi Cepat</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-lake-50 transition-colors group">
                        <span class="w-8 h-8 rounded-lg bg-lake-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-lake-900">Tambah Produk</p>
                            <p class="text-xs text-ink/40">Tambahkan produk baru ke tokomu</p>
                        </div>
                    </a>
                    <a href="#" class="flex items-center gap-3 p-3 rounded-lg hover:bg-lake-50 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-lake-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-lake-900">Edit Profil UMKM</p>
                            <p class="text-xs text-ink/40">Perbarui info tokomu</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Status toko --}}
            <div class="bg-lake-900 rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-2">Status Toko</p>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full bg-ulos-gold"></span>
                    <p class="text-sm font-semibold">Menunggu Verifikasi</p>
                </div>
                <p class="text-xs text-paper/50 leading-relaxed">Tokomu sedang ditinjau oleh tim TobaNiaga. Kamu akan dihubungi segera.</p>
            </div>
        </div>
    </div>

@endsection

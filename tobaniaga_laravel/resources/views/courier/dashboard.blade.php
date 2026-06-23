@extends('layouts.backoffice')

@section('title', 'Dashboard Kurir')
@section('role_label', 'Kurir')
@section('page_title', 'Dashboard Kurir')

@section('sidebar_nav')
    <a href="{{ route('courier.dashboard') }}" class="sidebar-link active">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Beranda
    </a>

    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Pengiriman</p>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        Tugas Aktif
    </a>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M5 12h14"/>
        </svg>
        Riwayat Pengiriman
    </a>

    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Akun</p>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
        </svg>
        Profil Saya
    </a>
@endsection

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Kurir Aktif</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Halo, {{ auth()->user()->nama ?? 'Kurir' }}</h2>
    </div>

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Tugas Hari Ini</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Pengiriman aktif</p>
        </div>
        <div class="stat-card border-l-2 border-l-ulos-gold">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Dalam Perjalanan</p>
            <p class="font-display text-3xl font-medium text-ulos-gold" style="color:#c49044">—</p>
            <p class="text-xs text-ink/50 mt-1">Sedang diantar</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Selesai</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Berhasil dikirim</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_280px] gap-6">

        {{-- Tugas pengiriman --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <h3 class="font-display text-base font-medium text-lake-900">Tugas Pengiriman</h3>
                <span class="font-mono text-[10px] bg-lake-50 text-lake-800 border border-lake-900/10 px-2 py-0.5 rounded-full uppercase tracking-wider">Hari ini</span>
            </div>
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Belum ada tugas pengiriman.</p>
                <p class="text-xs text-ink/30 mt-1">Pesanan yang ditugaskan untukmu akan muncul di sini.</p>
            </div>
        </div>

        {{-- Status & info kurir --}}
        <div class="space-y-4">
            {{-- Status kurir --}}
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Status Saya</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Ketersediaan</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-lake-800"></span>
                            <span class="text-sm font-medium text-lake-900">Aktif</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Area tugas</span>
                        <span class="font-mono text-xs text-ink/40">Belum diatur</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Total pengiriman</span>
                        <span class="font-mono text-xs text-ink/40">—</span>
                    </div>
                </div>
            </div>

            {{-- Panduan singkat --}}
            <div class="bg-lake-900 rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Cara Kerja</p>
                <div class="space-y-2.5">
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">01</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Admin menugaskan paket kepadamu</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">02</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Ambil paket dari alamat UMKM</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">03</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Antar ke pembeli dan konfirmasi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

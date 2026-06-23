@extends('layouts.backoffice')

@section('title', 'Dashboard Admin')
@section('role_label', 'Administrator')
@section('page_title', 'Dashboard Admin')

@section('sidebar_nav')
    <a href="{{ route('admin.dashboard') }}" class="sidebar-link active">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        Beranda
    </a>

    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Manajemen</p>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Kelola Pengguna
    </a>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
        </svg>
        Verifikasi UMKM
    </a>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
        </svg>
        Semua Pesanan
    </a>

    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Sistem</p>

    <a href="#" class="sidebar-link">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
        </svg>
        Pengaturan
    </a>
@endsection

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Panel Kontrol</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Halo, {{ auth()->user()->nama ?? 'Admin' }}</h2>
    </div>

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Total Pengguna</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Semua role</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">UMKM Terdaftar</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Aktif di platform</p>
        </div>
        <div class="stat-card border-l-2 border-l-ulos-maroon">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Menunggu Verifikasi</p>
            <p class="font-display text-3xl font-medium text-ulos-maroon">—</p>
            <p class="text-xs text-ink/50 mt-1">Perlu ditinjau</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Total Pesanan</p>
            <p class="font-display text-3xl font-medium text-lake-900">—</p>
            <p class="text-xs text-ink/50 mt-1">Sepanjang waktu</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_300px] gap-6">

        {{-- UMKM menunggu verifikasi --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="font-display text-base font-medium text-lake-900">UMKM Menunggu Verifikasi</h3>
                    <span class="font-mono text-[10px] bg-ulos-maroon/10 text-ulos-maroon px-2 py-0.5 rounded-full uppercase tracking-wider">Perlu Aksi</span>
                </div>
                <a href="#" class="font-mono text-xs text-lake-800 hover:underline">Lihat semua</a>
            </div>
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Tidak ada antrian verifikasi.</p>
                <p class="text-xs text-ink/30 mt-1">UMKM yang mendaftar akan muncul di sini.</p>
            </div>
        </div>

        {{-- Ringkasan role --}}
        <div class="space-y-4">
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Pengguna per Role</h3>
                </div>
                <div class="divide-y divide-lake-900/6">
                    @foreach([
                        ['label' => 'Customer',  'color' => '#1a4a6b'],
                        ['label' => 'Sales',     'color' => '#c49044'],
                        ['label' => 'Courier',   'color' => '#0f2d45'],
                        ['label' => 'Admin',     'color' => '#8f2333'],
                    ] as $role)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2 h-2 rounded-full" style="background:{{ $role['color'] }}"></span>
                            <span class="text-sm text-ink/70">{{ $role['label'] }}</span>
                        </div>
                        <span class="font-mono text-xs text-ink/40">—</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Aksi cepat admin --}}
            <div class="bg-lake-900 rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Aksi Admin</p>
                <a href="#" class="flex items-center gap-2.5 py-2 text-sm text-paper/80 hover:text-paper transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Tambah pengguna baru
                </a>
                <a href="#" class="flex items-center gap-2.5 py-2 text-sm text-paper/80 hover:text-paper transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Proses verifikasi UMKM
                </a>
            </div>
        </div>
    </div>

@endsection

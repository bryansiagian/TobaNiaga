@extends('layouts.guest')

@section('title', 'Daftar — TobaNiaga')

@section('content')
<div class="min-h-screen flex">

    {{-- Kolom kiri: panel visual --}}
    <div class="hidden lg:block lg:w-[42%] relative bg-lake-800 overflow-hidden">
        <div class="absolute inset-0 opacity-90 ulos-stripe-v"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-lake-900/20 via-transparent to-lake-900/70"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-14">
            <p class="font-display italic text-paper text-2xl leading-snug">
                "Setiap helai punya cerita, setiap produk punya pembuatnya."
            </p>
            <p class="mt-5 font-mono text-xs uppercase tracking-[0.2em] text-paper/60">TobaNiaga — Pasar UMKM Danau Toba</p>
        </div>
    </div>

    {{-- Kolom kanan: form --}}
    <div class="w-full lg:w-[58%] flex flex-col px-6 sm:px-12 lg:px-20 py-10">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded w-fit">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>

        <div class="flex-1 flex flex-col justify-center max-w-md w-full mx-auto py-12">
            <h1 class="font-display text-3xl font-medium text-lake-900 mb-2">Buat akun baru</h1>
            <p class="text-ink/60 mb-8">Daftar sebagai pembeli atau pemilik UMKM.</p>

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-ulos-maroon text-sm px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="space-y-5" x-data="{ role: '{{ old('role', 'customer') }}' }">
                @csrf

                {{-- Pilihan role --}}
                <div>
                    <span class="block text-sm font-medium text-lake-900 mb-2.5">Daftar sebagai</span>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="customer" x-model="role"
                                   class="peer sr-only" {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}>
                            <div class="rounded-lg border-2 border-lake-900/15 px-4 py-3.5 text-center transition-colors peer-checked:border-lake-800 peer-checked:bg-lake-50">
                                <span class="block text-sm font-semibold text-lake-900">Pembeli</span>
                                <span class="block text-xs text-ink/50 mt-0.5">Belanja produk</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="umkm" x-model="role"
                                   class="peer sr-only" {{ old('role') === 'umkm' ? 'checked' : '' }}>
                            <div class="rounded-lg border-2 border-lake-900/15 px-4 py-3.5 text-center transition-colors peer-checked:border-ulos-maroon peer-checked:bg-ulos-maroon/5">
                                <span class="block text-sm font-semibold text-lake-900">Pemilik UMKM</span>
                                <span class="block text-xs text-ink/50 mt-0.5">Jual produk</span>
                            </div>
                        </label>
                    </div>
                    <p x-show="role === 'umkm'" x-cloak class="mt-2.5 text-xs text-ulos-maroon/80 leading-relaxed">
                        Akun UMKM akan diverifikasi oleh admin sebelum bisa digunakan untuk berjualan.
                    </p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-lake-900 mb-1.5">Nama Lengkap</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                           autocomplete="name"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="Nama sesuai identitas">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-lake-900 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           autocomplete="email"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-lake-900 mb-1.5">Nomor HP</label>
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" required
                           autocomplete="tel"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="08xxxxxxxxxx">
                </div>

                {{-- Field tambahan khusus UMKM --}}
                <div x-show="role === 'umkm'" x-cloak class="space-y-5 pt-1 border-t border-lake-900/10">
                    <div class="pt-4">
                        <label for="nama_umkm" class="block text-sm font-medium text-lake-900 mb-1.5">Nama UMKM</label>
                        <input id="nama_umkm" name="nama_umkm" type="text" value="{{ old('nama_umkm') }}"
                               class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                               placeholder="Contoh: Kopi Lintong Pak Manik">
                    </div>
                    <div>
                        <label for="alamat_umkm" class="block text-sm font-medium text-lake-900 mb-1.5">Alamat Usaha</label>
                        <textarea id="alamat_umkm" name="alamat_umkm" rows="2"
                                  class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors resize-none"
                                  placeholder="Desa/Kecamatan, Kabupaten">{{ old('alamat_umkm') }}</textarea>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-lake-900 mb-1.5">Kata Sandi</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="Minimal 8 karakter">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-lake-900 mb-1.5">Konfirmasi Kata Sandi</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="Ulangi kata sandi">
                </div>

                <button type="submit"
                        class="w-full bg-lake-800 text-paper font-semibold py-3 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                    Daftar
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-ink/60">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="font-semibold text-ulos-maroon hover:underline focus-ring rounded">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush
@endsection

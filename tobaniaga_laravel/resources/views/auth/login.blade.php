@extends('layouts.guest')

@section('title', 'Masuk — TobaNiaga')
@section('hide_navbar', true)
@section('content')
<div class="min-h-screen flex">

    {{-- Kolom kiri: form --}}
    <div class="w-full lg:w-[46%] flex flex-col px-6 sm:px-12 lg:px-16 py-10">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded w-fit">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>

        <div class="flex-1 flex flex-col justify-center max-w-sm w-full mx-auto py-12">
            <h1 class="font-display text-3xl font-medium text-lake-900 mb-2">Selamat datang kembali</h1>
            <p class="text-ink/60 mb-9">Masuk untuk lanjut belanja atau kelola UMKM-mu.</p>

            @if (session('status'))
                <div class="mb-6 rounded-lg bg-lake-50 border border-lake-400/20 text-lake-800 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-ulos-maroon text-sm px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-lake-900 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                           autocomplete="email"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-lake-900">Kata Sandi</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-medium text-ulos-maroon hover:underline focus-ring rounded">Lupa kata sandi?</a>
                    </div>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 focus-ring transition-colors"
                           placeholder="••••••••">
                </div>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-lake-900/25 text-lake-800 focus:ring-lake-400">
                    <span class="text-sm text-ink/70">Ingat saya</span>
                </label>

                <button type="submit"
                        class="w-full bg-lake-800 text-paper font-semibold py-3 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                    Masuk
                </button>
            </form>

            <p class="mt-8 text-center text-sm text-ink/60">
                Belum punya akun?
                <a href="{{ route('register') }}" class="font-semibold text-ulos-maroon hover:underline focus-ring rounded">Daftar di sini</a>
            </p>
        </div>
    </div>

    {{-- Kolom kanan: panel visual --}}
    <div class="hidden lg:block lg:w-[54%] relative bg-lake-800 overflow-hidden">
        <div class="absolute inset-0 opacity-90 ulos-stripe-v"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-lake-900/20 via-transparent to-lake-900/70"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-16">
            <p class="font-display italic text-paper text-3xl leading-snug max-w-md">
                "Pasar yang menjaga jarak antara pembuat dan pembeli tetap dekat."
            </p>
            <p class="mt-5 font-mono text-xs uppercase tracking-[0.2em] text-paper/60">TobaNiaga — Pasar UMKM Danau Toba</p>
        </div>
    </div>
</div>
@endsection

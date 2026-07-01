@extends('layouts.backoffice')

@section('title', 'Profil Saya')
@section('role_label', 'Kurir')
@section('page_title', 'Profil Saya')

@section('content')
<div class="max-w-3xl" x-data="{ tab: 'profil' }">

    {{-- Tab navigation --}}
    <div class="flex gap-1 bg-lake-50 rounded-xl p-1 mb-8 border border-lake-900/10">
        @foreach([['profil','Profil Pribadi'],['email','Email'],['password','Password']] as [$key,$label])
        <button @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-paper shadow-sm text-lake-900 font-semibold' : 'text-ink/50 hover:text-ink'"
                class="flex-1 text-sm py-2 px-3 rounded-lg transition-all">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── Tab Profil Pribadi ── --}}
    <div x-show="tab === 'profil'" x-cloak>
        @if(session('status_profil'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_profil') }}
        </div>
        @endif

        <div class="bg-paper border border-lake-900/10 rounded-xl p-6">
            <form action="{{ route('courier.profil.pribadi.update') }}" method="POST"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-lake-50 border border-lake-900/10 flex-shrink-0">
                        @if($user->foto_profil)
                            <img src="{{ Storage::url($user->foto_profil) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl font-bold text-lake-900/30">
                                {{ Str::upper(Str::substr($user->nama, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-lake-900 mb-1">Foto Profil</p>
                        <input type="file" name="foto_profil" accept="image/*"
                               class="text-xs text-ink/60 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                        <p class="text-xs text-ink/40 mt-1">JPG/PNG, maks. 2MB</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('nama')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Nomor HP / WA</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    @error('no_hp')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    @error('tanggal_lahir')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tab Email ── --}}
    <div x-show="tab === 'email'" x-cloak>
        @if(session('status_email'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_email') }}
        </div>
        @endif

        <div class="bg-paper border border-lake-900/10 rounded-xl p-6 space-y-6">
            <div>
                <p class="text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Email Saat Ini</p>
                <p class="text-sm font-medium text-ink">{{ $user->email }}</p>
            </div>

            <div class="border-t border-lake-900/10 pt-5">
                <p class="text-sm font-semibold text-ink mb-3">Langkah 1 — Masukkan email baru</p>
                <form action="{{ route('courier.profil.email.otp') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="email" name="email_baru"
                           placeholder="emailbaru@contoh.com"
                           value="{{ old('email_baru') }}"
                           class="flex-1 border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    <button type="submit"
                            class="px-4 py-2.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 whitespace-nowrap">
                        Kirim OTP
                    </button>
                </form>
                @error('email_baru')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="border-t border-lake-900/10 pt-5">
                <p class="text-sm font-semibold text-ink mb-3">Langkah 2 — Masukkan kode OTP</p>
                <form action="{{ route('courier.profil.email.verifikasi') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="otp" maxlength="6"
                           placeholder="6 digit kode OTP"
                           class="flex-1 border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    <button type="submit"
                            class="px-4 py-2.5 bg-ulos-maroon text-paper text-sm font-semibold rounded-lg hover:opacity-90 whitespace-nowrap">
                        Verifikasi
                    </button>
                </form>
                @error('otp')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Tab Password ── --}}
    <div x-show="tab === 'password'" x-cloak>
        @if(session('status_password'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_password') }}
        </div>
        @endif

        <div class="bg-paper border border-lake-900/10 rounded-xl p-6">
            <form action="{{ route('courier.profil.password') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Password Lama</label>
                    <input type="password" name="password_lama"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('password_lama')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Password Baru</label>
                    <input type="password" name="password_baru"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('password_baru')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-ink/40 uppercase tracking-wide mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

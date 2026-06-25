@extends('layouts.guest')

@section('title', 'Daftar — TobaNiaga')
@section('hide_navbar', true)
@section('content')
<div class="min-h-screen flex">

    {{-- Panel kiri visual --}}
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
    <div class="w-full lg:w-[58%] flex flex-col px-6 sm:px-12 lg:px-20 py-10 overflow-y-auto">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded w-fit">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>

        <div class="max-w-md w-full mx-auto py-10">
            <h1 class="font-display text-3xl font-medium text-lake-900 mb-2">Buat akun baru</h1>
            <p class="text-ink/60 mb-8">Daftar sebagai pembeli atau pemilik UMKM.</p>

            @if($errors->any())
                <div class="mb-6 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-ulos-maroon text-sm px-4 py-3">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}"
                  class="space-y-5"
                  x-data="registerForm()">
                @csrf

                {{-- ── Pilihan role ── --}}
                <div>
                    <span class="block text-sm font-medium text-lake-900 mb-2.5">Daftar sebagai</span>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="customer" x-model="role" class="peer sr-only">
                            <div class="rounded-lg border-2 border-lake-900/15 px-4 py-3.5 text-center transition-colors peer-checked:border-lake-800 peer-checked:bg-lake-50">
                                <span class="block text-sm font-semibold text-lake-900">Pembeli</span>
                                <span class="block text-xs text-ink/50 mt-0.5">Belanja produk</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="role" value="sales" x-model="role" class="peer sr-only">
                            <div class="rounded-lg border-2 border-lake-900/15 px-4 py-3.5 text-center transition-colors peer-checked:border-ulos-maroon peer-checked:bg-ulos-maroon/5">
                                <span class="block text-sm font-semibold text-lake-900">Pemilik UMKM</span>
                                <span class="block text-xs text-ink/50 mt-0.5">Jual produk</span>
                            </div>
                        </label>
                    </div>
                    <p x-show="role === 'sales'" x-cloak class="mt-2.5 text-xs text-ulos-maroon/80 leading-relaxed">
                        Akun UMKM akan diverifikasi admin sebelum bisa digunakan untuk berjualan.
                    </p>
                </div>

                {{-- ── Data diri ── --}}
                <div>
                    <label for="nama" class="block text-sm font-medium text-lake-900 mb-1.5">Nama Lengkap</label>
                    <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required autofocus
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                           placeholder="Nama sesuai identitas">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-lake-900 mb-1.5">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                           placeholder="nama@email.com">
                </div>

                <div>
                    <label for="no_hp" class="block text-sm font-medium text-lake-900 mb-1.5">Nomor HP</label>
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" required
                           class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                           placeholder="08xxxxxxxxxx">
                </div>

                {{-- ── Data UMKM (hanya tampil jika role = sales) ── --}}
                <div x-show="role === 'sales'" x-cloak class="space-y-5 pt-3 border-t border-lake-900/10">
                    <p class="font-mono text-xs uppercase tracking-widest text-ink/40 pt-1">Informasi UMKM</p>

                    {{-- Kategori --}}
                    <div>
                        <label for="kategori_id" class="block text-sm font-medium text-lake-900 mb-1.5">Kategori UMKM</label>
                        <select id="kategori_id" name="kategori_id"
                                class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors bg-paper">
                            <option value="">Pilih kategori...</option>
                            @foreach($kategori as $kat)
                                <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Nama UMKM --}}
                    <div>
                        <label for="nama_umkm" class="block text-sm font-medium text-lake-900 mb-1.5">Nama UMKM</label>
                        <input id="nama_umkm" name="nama_umkm" type="text" value="{{ old('nama_umkm') }}"
                               class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                               placeholder="Contoh: Kopi Lintong Pak Manik">
                        {{-- Preview slug --}}
                        <p class="mt-1.5 font-mono text-xs text-ink/40"
                           x-show="slugPreview"
                           x-text="'tobaniaga.com/umkm/' + slugPreview"></p>
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="deskripsi" class="block text-sm font-medium text-lake-900 mb-1.5">Deskripsi UMKM</label>
                        <textarea id="deskripsi" name="deskripsi" rows="3"
                                  class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors resize-none"
                                  placeholder="Ceritakan singkat tentang usahamu...">{{ old('deskripsi') }}</textarea>
                    </div>

                    {{-- Alamat --}}
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-lake-900 mb-1.5">Alamat Usaha</label>
                        <input id="alamat" name="alamat" type="text" value="{{ old('alamat') }}"
                               class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                               placeholder="Nama jalan / gang / RT-RW">
                    </div>

                    {{-- Kecamatan (dari emsifa) --}}
                    <div>
                        <label for="kecamatan_id" class="block text-sm font-medium text-lake-900 mb-1.5">
                            Kecamatan
                            <span class="font-normal text-ink/40 text-xs ml-1">— Kabupaten Toba, Sumatera Utara</span>
                        </label>
                        <select id="kecamatan_id" name="kecamatan"
                                x-model="kecamatanDipilih"
                                @change="loadDesa()"
                                class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors bg-paper">
                            <option value="">Pilih kecamatan...</option>
                            <template x-for="kec in kecamatanList" :key="kec.id">
                                <option :value="kec.name" :data-id="kec.id" x-text="kec.name"
                                        :selected="kec.name === '{{ old('kecamatan') }}'"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Desa (cascade dari kecamatan) --}}
                    <div>
                        <label for="desa" class="block text-sm font-medium text-lake-900 mb-1.5">Desa / Kelurahan</label>
                        <select id="desa" name="desa"
                                :disabled="desaList.length === 0"
                                class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors bg-paper disabled:opacity-50">
                            <option value="">
                                <span x-text="kecamatanDipilih ? 'Pilih desa...' : 'Pilih kecamatan dulu'"></span>
                            </option>
                            <template x-for="desa in desaList" :key="desa.id">
                                <option :value="desa.name" x-text="desa.name"
                                        :selected="desa.name === '{{ old('desa') }}'"></option>
                            </template>
                        </select>
                    </div>

                    {{-- No HP WA --}}
                    <div>
                        <label for="no_hp_wa" class="block text-sm font-medium text-lake-900 mb-1.5">Nomor WhatsApp Toko</label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-ink/40 font-mono">+62</span>
                            <input id="no_hp_wa" name="no_hp_wa" type="tel" value="{{ old('no_hp_wa') }}"
                                   class="w-full rounded-lg border border-lake-900/15 pl-12 pr-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                                   placeholder="8xxxxxxxxxx">
                        </div>
                        <p class="mt-1.5 text-xs text-ink/40">Nomor ini ditampilkan kepada pembeli untuk chat WA langsung.</p>
                    </div>
                </div>

                {{-- ── Password ── --}}
                <div class="pt-3 border-t border-lake-900/10 space-y-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-lake-900 mb-1.5">Kata Sandi</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password"
                               class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                               placeholder="Minimal 8 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-lake-900 mb-1.5">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full rounded-lg border border-lake-900/15 px-4 py-2.5 text-ink placeholder:text-ink/35 focus:border-lake-400 focus:ring-1 focus:ring-lake-400 transition-colors"
                               placeholder="Ulangi kata sandi">
                    </div>
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
<script>
// Kode kabupaten Toba di emsifa: 1218
const KODE_KABUPATEN_TOBA = '1206';

function registerForm() {
    return {
        role: '{{ old('role', 'customer') }}',
        slugPreview: '',
        kecamatanList: [],
        desaList: [],
        kecamatanDipilih: '',
        kecamatanKode: '',

        init() {
            this.$watch('role', val => {
                if (val === 'sales' && this.kecamatanList.length === 0) {
                    this.loadKecamatan();
                }
            });

            // Watch nama_umkm untuk generate slug preview
            const namaInput = document.getElementById('nama_umkm');
            if (namaInput) {
                namaInput.addEventListener('input', (e) => {
                    this.slugPreview = this.toSlug(e.target.value);
                });
                // Set dari old value jika ada
                if (namaInput.value) {
                    this.slugPreview = this.toSlug(namaInput.value);
                }
            }

            // Jika ada old('role') = sales, load kecamatan langsung
            if (this.role === 'sales') {
                this.loadKecamatan();
            }
        },

        async loadKecamatan() {
            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${KODE_KABUPATEN_TOBA}.json`);
                this.kecamatanList = await res.json();
            } catch (e) {
                console.error('Gagal memuat data kecamatan:', e);
            }
        },

        async loadDesa() {
            this.desaList = [];
            const kec = this.kecamatanList.find(k => k.name === this.kecamatanDipilih);
            if (!kec) return;

            try {
                const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kec.id}.json`);
                this.desaList = await res.json();
            } catch (e) {
                console.error('Gagal memuat data desa:', e);
            }
        },

        toSlug(str) {
            return str.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .trim()
                .replace(/\s+/g, '-');
        }
    }
}
</script>
@endpush
@endsection

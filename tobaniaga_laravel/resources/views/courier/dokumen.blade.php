@extends('layouts.guest')
@section('title', 'Lengkapi Dokumen — TobaNiaga')
@section('hide_navbar', true)

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <a href="{{ route('courier.dashboard') }}"
       class="inline-flex items-center gap-1.5 text-sm text-ink/50 hover:text-ink mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Dashboard
    </a>

    <div class="text-center mb-8">
        <h1 class="font-display text-2xl font-semibold text-lake-900">Lengkapi Dokumen Identitas</h1>
        <p class="text-sm text-ink/50 mt-1">Diperlukan untuk verifikasi sebelum kamu bisa menerima tugas pengiriman.</p>
    </div>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('courier.dokumen.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-lake-100 overflow-hidden">
        @csrf

        <div class="px-6 py-5 space-y-4">

            <div>
                <label class="block text-xs font-medium text-lake-900 mb-1">NIK (16 digit)</label>
                <input type="text" name="nik"
                       value="{{ old('nik', $user->nik ? str_repeat('*', 12) . substr($user->nik, -4) : '') }}"
                       maxlength="16" inputmode="numeric"
                       placeholder="3201xxxxxxxxxxxx"
                       class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                @if($user->nik)
                <p class="text-xs text-ink/40 mt-1">NIK sudah tersimpan. Isi ulang hanya jika ingin menggantinya.</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">No. HP</label>
                    <input type="text" name="no_hp"
                           value="{{ old('no_hp', $user->no_hp) }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-lake-900 mb-1">Alamat Sesuai KTP</label>
                <textarea name="alamat_ktp" rows="2"
                          class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none">{{ old('alamat_ktp', $user->alamat_ktp) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Foto KTP</label>
                    @if($user->foto_ktp)
                    <p class="text-xs text-green-600 mb-1">✓ Sudah diupload sebelumnya</p>
                    @endif
                    <input type="file" name="foto_ktp" accept="image/*"
                           class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                    <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB{{ $user->foto_ktp ? '. Kosongkan jika tidak ingin mengganti.' : '' }}</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Foto Kartu Keluarga</label>
                    @if($user->foto_kk)
                    <p class="text-xs text-green-600 mb-1">✓ Sudah diupload sebelumnya</p>
                    @endif
                    <input type="file" name="foto_kk" accept="image/*"
                           class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                    <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB{{ $user->foto_kk ? '. Kosongkan jika tidak ingin mengganti.' : '' }}</p>
                </div>
            </div>

        </div>

        <div class="px-6 py-5 bg-lake-50 border-t border-lake-100">
            <button type="submit"
                    class="w-full px-5 py-3 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 transition-colors">
                Kirim untuk Diverifikasi
            </button>
            <p class="text-xs text-ink/40 text-center mt-3">
                Data ini bersifat rahasia dan hanya digunakan untuk verifikasi identitas oleh admin TobaNiaga.
            </p>
        </div>
    </form>
</div>
@endsection

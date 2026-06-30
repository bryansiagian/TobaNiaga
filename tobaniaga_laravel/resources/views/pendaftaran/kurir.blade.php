@extends('layouts.guest')
@section('title', 'Daftar Jadi Kurir — TobaNiaga')
@section('hide_navbar', true)

@section('content')
<div class="max-w-xl mx-auto py-10 px-4">
    <div class="text-center mb-8">
        <h1 class="font-display text-2xl font-semibold text-lake-900">Daftar Jadi Kurir</h1>
        <p class="text-sm text-ink/50 mt-1">Lengkapi data diri untuk mulai mengantar pesanan di TobaNiaga</p>
    </div>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('daftar.kurir.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-lake-100 overflow-hidden">
        @csrf

        <div class="px-6 py-5 space-y-4">
            <div>
                <label class="block text-xs font-medium text-lake-900 mb-1">NIK (16 digit)</label>
                <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric"
                       placeholder="3201xxxxxxxxxxxx"
                       class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">No. HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-lake-900 mb-1">Alamat Sesuai KTP</label>
                <textarea name="alamat_ktp" rows="2"
                          class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none">{{ old('alamat_ktp') }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Foto KTP</label>
                    <input type="file" name="foto_ktp" accept="image/*"
                           class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lake-900/20 file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                    <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Foto Kartu Keluarga</label>
                    <input type="file" name="foto_kk" accept="image/*"
                           class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lake-900/20 file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                    <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB</p>
                </div>
            </div>
        </div>

        <div class="px-6 py-5 bg-lake-50 border-t border-lake-100">
            <button type="submit"
                    class="w-full px-5 py-3 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 transition-colors">
                Kirim Pendaftaran
            </button>
            <p class="text-xs text-ink/40 text-center mt-3">
                Dengan mendaftar, kamu setuju datamu diverifikasi oleh admin TobaNiaga.
            </p>
        </div>
    </form>
</div>
@endsection

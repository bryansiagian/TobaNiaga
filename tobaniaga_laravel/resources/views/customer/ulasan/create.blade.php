@extends('layouts.guest')

@section('title', 'Tulis Ulasan — TobaNiaga')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <a href="{{ route('customer.pesanan.show', $pesanan) }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-ink mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Detail Pesanan
    </a>

    <div class="mb-6">
        <h1 class="font-display text-2xl font-semibold text-ink">Tulis Ulasan</h1>
        <p class="text-sm text-gray-500 mt-1">Pesanan <span class="font-mono font-medium">{{ $pesanan->no_pesanan }}</span> · {{ $pesanan->umkm->nama_umkm }}</p>
    </div>

    <form action="{{ route('customer.pesanan.ulasan.store', $pesanan) }}" method="POST"
          enctype="multipart/form-data" class="space-y-5">
        @csrf

        @foreach($produkBelumDiulas as $idx => $detail)
        <div class="bg-white rounded-xl border border-lake-100 overflow-hidden"
             x-data="{ rating: 0, hover: 0 }">

            {{-- Header produk --}}
            <div class="flex items-center gap-3 px-5 py-4 border-b border-lake-100">
                @php
                    $foto = $detail->produk?->fotoProduk?->first();
                @endphp
                @if($foto)
                    @php $fotoUrl = Str::startsWith($foto->url_foto, ['http://', 'https://']) ? $foto->url_foto : Storage::url($foto->url_foto); @endphp
                    <img src="{{ $fotoUrl }}" class="w-12 h-12 rounded-lg object-cover border border-lake-100 flex-shrink-0">
                @else
                    <div class="w-12 h-12 rounded-lg bg-lake-50 border border-lake-100 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-lake-900/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-ink truncate">{{ $detail->nama_produk_snapshot }}</p>
                    <p class="text-xs text-gray-400">{{ $detail->jumlah }} {{ $detail->produk?->satuan ?? 'pcs' }}</p>
                </div>
            </div>

            <div class="px-5 py-4 space-y-4">
                <input type="hidden" name="ulasan[{{ $idx }}][produk_id]" value="{{ $detail->produk_id }}">

                {{-- Bintang --}}
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Rating</p>
                    <div class="flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button"
                                @click="rating = {{ $i }}"
                                @mouseenter="hover = {{ $i }}"
                                @mouseleave="hover = 0"
                                class="focus:outline-none">
                            <svg class="w-8 h-8 transition-colors"
                                 :class="(hover || rating) >= {{ $i }} ? 'text-ulos-gold fill-current' : 'text-lake-900/15 fill-current'"
                                 viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </button>
                        @endfor
                    </div>
                    <input type="hidden" name="ulasan[{{ $idx }}][rating]" :value="rating" required>
                    <p class="text-xs text-gray-400 mt-1.5"
                       x-text="['', 'Sangat Buruk', 'Kurang Memuaskan', 'Cukup', 'Memuaskan', 'Sangat Memuaskan'][rating] || 'Pilih rating'">
                    </p>
                </div>

                {{-- Komentar --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                        Komentar <span class="normal-case font-normal text-gray-300">(opsional)</span>
                    </label>
                    <textarea name="ulasan[{{ $idx }}][komentar]" rows="3"
                              placeholder="Bagaimana pengalamanmu dengan produk ini?"
                              class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-white focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none"></textarea>
                </div>

                {{-- Foto --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">
                        Foto <span class="normal-case font-normal text-gray-300">(opsional, maks. 3 foto)</span>
                    </label>
                    <input type="file" name="ulasan[{{ $idx }}][foto][]"
                           multiple accept="image/*"
                           class="w-full text-sm text-ink/60 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                </div>

                {{-- Anonim --}}
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="ulasan[{{ $idx }}][is_anonim]" value="1"
                           class="w-4 h-4 rounded accent-lake-900">
                    <span class="text-sm text-gray-600">Kirim sebagai anonim</span>
                </label>
            </div>
        </div>
        @endforeach

        @error('ulasan')
            <p class="text-sm text-ulos-maroon">{{ $message }}</p>
        @enderror

        <div class="flex gap-3 pt-2">
            <a href="{{ route('customer.pesanan.show', $pesanan) }}"
               class="px-5 py-2.5 border border-lake-200 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
                Batal
            </a>
            <button type="submit"
                    class="flex-1 px-5 py-2.5 bg-lake-900 text-white text-sm font-semibold rounded-lg hover:bg-lake-800">
                Kirim Ulasan
            </button>
        </div>
    </form>
</div>
@endsection

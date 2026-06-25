@extends('layouts.guest')

@section('title', 'Pembayaran — TobaNiaga')

@section('content')

<header class="relative z-20 border-b border-lake-900/10">
    <nav class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between py-6">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>
    </nav>
</header>

<section class="bg-paper min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-auto px-6 py-14 text-center">
        <h1 class="font-display text-2xl font-medium text-lake-900 mb-2">Selesaikan Pembayaran</h1>
        <p class="text-sm text-ink/50 mb-2">No. Pesanan: <span class="font-mono text-ink/70">{{ $pesanan->no_pesanan }}</span></p>
        <p class="font-display text-xl font-semibold text-lake-900 mb-8">
            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
        </p>

        <button id="pay-button"
                class="w-full px-5 py-3.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-900/90 transition-colors">
            Bayar Sekarang
        </button>

        <p class="text-xs text-ink/40 mt-4">Kamu akan diarahkan ke halaman pembayaran Midtrans.</p>
    </div>
</section>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.getElementById('pay-button').addEventListener('click', function () {
        window.snap.pay('{{ $pembayaran->snap_token }}', {
            onSuccess: function (result) {
                window.location.href = '{{ route('welcome') }}';
            },
            onPending: function (result) {
                window.location.href = '{{ route('welcome') }}';
            },
            onError: function (result) {
                alert('Pembayaran gagal. Silakan coba lagi.');
            },
            onClose: function () {
                // user tutup popup tanpa bayar
            }
        });
    });
</script>

@endsection

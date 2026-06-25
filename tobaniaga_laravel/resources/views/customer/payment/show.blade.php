@extends('layouts.guest')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    <div class="bg-white rounded-xl shadow-sm border border-lake-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-lake-900 text-white px-6 py-5">
            <p class="text-sm text-lake-200">Pesanan</p>
            <h1 class="text-lg font-semibold">{{ $pesanan->no_pesanan }}</h1>
            <p class="text-2xl font-bold mt-1">Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</p>
        </div>

        <div class="p-6" id="payment-root" data-pesanan-id="{{ $pesanan->id }}">

            @if($pembayaran?->status?->kode === 'settlement')
                {{-- ── Tampilan Sukses ── --}}
                <div class="text-center py-8">
                    <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-ink mb-1">Pembayaran Berhasil</h2>
                    <p class="text-sm text-gray-500 mb-6">Pesanan kamu sedang diproses oleh penjual.</p>
                    <a href="{{ route('customer.pesanan.riwayat') }}"
                       class="inline-block px-5 py-2.5 rounded-lg bg-lake-900 text-white text-sm font-medium hover:bg-lake-800">
                        Lihat Riwayat Pesanan
                    </a>
                </div>

            @else
                {{-- ── Form Pembayaran ── --}}

                {{-- Status banner --}}
                <div id="status-banner" class="hidden mb-6 p-4 rounded-lg text-sm font-medium"></div>

                {{-- Step 1: Pilih metode --}}
                <div id="method-section">
                    <p class="text-sm font-medium text-gray-700 mb-3">Pilih metode pembayaran</p>

                    <div class="flex gap-2 mb-5" id="tab-buttons">
                        <button type="button" data-tab="va"   class="tab-btn flex-1 py-2 text-sm font-medium rounded-lg border border-lake-200 bg-lake-900 text-white">Virtual Account</button>
                        <button type="button" data-tab="qris" class="tab-btn flex-1 py-2 text-sm font-medium rounded-lg border border-lake-200 text-lake-900">QRIS</button>
                        <button type="button" data-tab="card" class="tab-btn flex-1 py-2 text-sm font-medium rounded-lg border border-lake-200 text-lake-900">Kartu</button>
                    </div>

                    {{-- VA Tab --}}
                    <div data-panel="va" class="tab-panel">
                        <p class="text-sm text-gray-500 mb-3">Pilih bank tujuan transfer</p>
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            @foreach(['bca' => 'BCA', 'bni' => 'BNI', 'bri' => 'BRI', 'mandiri' => 'Mandiri'] as $code => $label)
                            <button type="button"
                                    class="bank-btn border border-lake-200 rounded-lg py-3 text-sm font-medium text-gray-700 hover:border-lake-900 hover:bg-lake-50"
                                    data-bank="{{ $code }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- QRIS Tab --}}
                    <div data-panel="qris" class="tab-panel hidden">
                        <p class="text-sm text-gray-500 mb-4">Scan QRIS dengan GoPay, OVO, Dana, atau e-wallet lainnya.</p>
                        <button type="button" id="qris-charge-btn"
                                class="w-full py-3 rounded-lg bg-lake-900 text-white text-sm font-semibold hover:bg-lake-800">
                            Generate QRIS
                        </button>
                    </div>

                    {{-- Card Tab --}}
                    <div data-panel="card" class="tab-panel hidden">
                        <p class="text-sm text-gray-500 mb-4">
                            Pembayaran kartu memerlukan verifikasi 3DS dari bank penerbit.
                        </p>
                        <div class="space-y-3 mb-4">
                            <input type="text" id="card-number" placeholder="0000 0000 0000 0000" maxlength="19"
                                class="w-full border border-lake-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-500 font-mono tracking-widest">
                            <div class="flex gap-3">
                                <input type="text" id="card-exp-month" placeholder="MM" maxlength="2"
                                    class="w-1/3 border border-lake-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-500 text-center font-mono">
                                <input type="text" id="card-exp-year" placeholder="YY" maxlength="2"
                                    class="w-1/3 border border-lake-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-500 text-center font-mono">
                                <input type="text" id="card-cvv" placeholder="CVV" maxlength="3"
                                    class="w-1/3 border border-lake-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-500 text-center font-mono">
                            </div>
                        </div>
                        <button type="button" id="card-charge-btn"
                                class="w-full py-3 rounded-lg bg-lake-900 text-white text-sm font-semibold hover:bg-lake-800">
                            Bayar dengan Kartu
                        </button>
                        <p class="text-xs text-gray-400 mt-2">Sandbox test card: 4811 1111 1111 1114, exp 01/27, CVV 123</p>
                    </div>
                </div>

                {{-- Step 2: Instruksi pembayaran --}}
                <div id="instruction-section" class="hidden"></div>

                <p class="text-center text-xs text-gray-400 mt-6">
                    Status akan otomatis terupdate setelah pembayaran diverifikasi.
                </p>

            @endif
        </div>
    </div>
</div>

@if($pembayaran?->status?->kode !== 'settlement')
<script>
(function () {
    const root        = document.getElementById('payment-root');
    const pesananId   = root.dataset.pesananId;
    const chargeUrl   = "{{ route('customer.payment.charge', $pesanan) }}";
    const statusUrl   = "{{ route('customer.payment.status', $pesanan) }}";
    const csrfToken   = "{{ csrf_token() }}";

    const methodSection      = document.getElementById('method-section');
    const instructionSection = document.getElementById('instruction-section');
    const statusBanner       = document.getElementById('status-banner');

    // ── Card input formatting ───────────────────────────
    const cardNumberInput = document.getElementById('card-number');
    const cardExpMonth    = document.getElementById('card-exp-month');
    const cardExpYear     = document.getElementById('card-exp-year');
    const cardCvv         = document.getElementById('card-cvv');

    // Format nomor kartu: spasi setiap 4 digit, max 16 digit
    cardNumberInput.addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, '').slice(0, 16);
        e.target.value = val.replace(/(.{4})/g, '$1 ').trim();
    });

    // MM: hanya angka, auto-pindah ke YY setelah 2 digit
    cardExpMonth.addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, '').slice(0, 2);
        e.target.value = val;
        if (val.length === 2) cardExpYear.focus();
    });

    // YY: hanya angka, auto-pindah ke CVV setelah 2 digit
    cardExpYear.addEventListener('input', (e) => {
        let val = e.target.value.replace(/\D/g, '').slice(0, 2);
        e.target.value = val;
        if (val.length === 2) cardCvv.focus();
    });

    // CVV: hanya angka, max 3 digit
    cardCvv.addEventListener('input', (e) => {
        e.target.value = e.target.value.replace(/\D/g, '').slice(0, 3);
    });

    // ── Tab switching ───────────────────────────────────
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-lake-900', 'text-white');
                b.classList.add('text-lake-900');
            });
            btn.classList.add('bg-lake-900', 'text-white');
            btn.classList.remove('text-lake-900');

            document.querySelectorAll('.tab-panel').forEach(p => p.classList.add('hidden'));
            document.querySelector(`[data-panel="${btn.dataset.tab}"]`).classList.remove('hidden');
        });
    });

    // ── Helper: POST ke /charge ─────────────────────────
    async function charge(payload) {
        setLoading(true);
        try {
            const res = await fetch(chargeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok) {
                showBanner(data.message || 'Gagal memproses pembayaran.', 'error');
                setLoading(false);
                return;
            }

            renderInstruction(payload.metode, data);
        } catch (e) {
            showBanner('Tidak bisa menghubungi server.', 'error');
            setLoading(false);
        }
    }

    function setLoading(state) {
        document.querySelectorAll('.bank-btn, #qris-charge-btn, #card-charge-btn').forEach(el => {
            el.disabled = state;
            el.classList.toggle('opacity-50', state);
        });
    }

    function showBanner(message, type) {
        statusBanner.textContent = message;
        statusBanner.className = 'mb-6 p-4 rounded-lg text-sm font-medium ' +
            (type === 'error'
                ? 'bg-red-50 text-red-700 border border-red-200'
                : 'bg-green-50 text-green-700 border border-green-200');
        statusBanner.classList.remove('hidden');
    }

    // ── Bank VA buttons ─────────────────────────────────
    document.querySelectorAll('.bank-btn').forEach(btn => {
        btn.addEventListener('click', () => charge({ metode: btn.dataset.bank }));
    });

    // ── QRIS button ─────────────────────────────────────
    document.getElementById('qris-charge-btn').addEventListener('click', () => {
        charge({ metode: 'qris' });
    });

    // ── Card button — tokenisasi via Midtrans 3DS.js ────
    document.getElementById('card-charge-btn').addEventListener('click', () => {
        const cardNumber = document.getElementById('card-number').value.replace(/\s/g, '');
        const expMonth   = document.getElementById('card-exp-month').value.trim();
        const expYear    = document.getElementById('card-exp-year').value.trim();
        const cvv        = document.getElementById('card-cvv').value.trim();

        if (!cardNumber || !expMonth || !expYear || !cvv) {
            showBanner('Lengkapi semua data kartu.', 'error');
            return;
        }

        if (typeof MidtransNew3ds === 'undefined') {
            showBanner('Midtrans.js belum termuat. Refresh halaman dan coba lagi.', 'error');
            return;
        }

        setLoading(true);

        MidtransNew3ds.getCardToken({
            card_number: cardNumber,
            card_exp_month: expMonth,
            card_exp_year: '20' + expYear,
            card_cvv: cvv,
        }, {
            onSuccess: function (response) {
                charge({ metode: 'card', card_token: response.token_id });
            },
            onFailure: function (response) {
                setLoading(false);
                showBanner('Kartu tidak valid: ' + (response.status_message || 'gagal validasi'), 'error');
            },
            onPending: function () {
                setLoading(false);
                showBanner('Validasi kartu tertunda, coba lagi.', 'error');
            },
        });
    });

    // ── Render instruksi setelah charge sukses ──────────
    function renderInstruction(metode, data) {
        methodSection.classList.add('hidden');
        instructionSection.classList.remove('hidden');

        let html = '';

        if (['bca', 'bni', 'bri', 'mandiri'].includes(metode)) {
            const va = data.va_numbers?.[0]?.va_number || data.permata_va_number || '-';
            const bankLabel = metode.toUpperCase();
            html = `
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-1">Transfer ke Virtual Account ${bankLabel}</p>
                    <p class="text-2xl font-bold text-lake-900 tracking-wider mb-4">${va}</p>
                    <div class="bg-lake-50 border border-lake-100 rounded-lg p-4 text-left text-sm text-gray-600 space-y-1">
                        <p>1. Buka aplikasi mobile banking ${bankLabel}</p>
                        <p>2. Pilih menu Transfer &rarr; Virtual Account</p>
                        <p>3. Masukkan nomor VA di atas</p>
                        <p>4. Konfirmasi dan selesaikan pembayaran</p>
                    </div>
                </div>`;
        } else if (metode === 'qris') {
            const qrUrl = data.actions?.find(a => a.name === 'generate-qr-code')?.url || '';
            html = `
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">Scan QR berikut dengan e-wallet kamu</p>
                    ${qrUrl ? `<img src="${qrUrl}" class="w-56 h-56 mx-auto border border-lake-100 rounded-lg p-2" alt="QRIS">` : '<p class="text-red-500 text-sm">QR tidak tersedia</p>'}
                </div>`;
        } else if (metode === 'card') {
            const redirectUrl = data.redirect_url || '';
            if (redirectUrl) {
                // Buka 3DS di popup, halaman payment tetap di background
                const popup = window.open(redirectUrl, '3ds_popup', 'width=500,height=600,scrollbars=yes');

                html = `
                    <div class="text-center py-4">
                        <p class="text-sm text-gray-600 mb-2">Jendela verifikasi 3DS sudah dibuka.</p>
                        <p class="text-xs text-gray-400 mb-4">Selesaikan verifikasi di jendela tersebut, lalu kembali ke halaman ini.</p>
                        <button type="button" onclick="window.open('${redirectUrl}', '3ds_popup', 'width=500,height=600,scrollbars=yes')"
                                class="px-4 py-2 rounded-lg border border-lake-200 text-lake-900 text-xs font-medium hover:bg-lake-50">
                            Buka Ulang Jendela 3DS
                        </button>
                    </div>`;
            } else {
                html = `<p class="text-center text-sm text-green-600">Pembayaran kartu sedang diproses.</p>`;
            }
        }

        html += `<p class="text-center text-xs text-gray-400 mt-5" id="waiting-text">Menunggu pembayaran...</p>`;
        instructionSection.innerHTML = html;
        setLoading(false);

        startPolling();
    }

    // ── Polling status pembayaran ───────────────────────
    let pollInterval = null;

    function startPolling() {
        clearInterval(pollInterval); // hindari duplikat
        pollInterval = setInterval(checkStatus, 4000);
    }

    async function checkStatus() {
        try {
            const res = await fetch(statusUrl, {
                headers: { 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.status === 'settlement') {
                clearInterval(pollInterval);
                showBanner('Pembayaran berhasil! Mengarahkan...', 'success');
                setTimeout(() => window.location.reload(), 1500);
            } else if (['expire', 'cancel'].includes(data.status)) {
                clearInterval(pollInterval);
                showBanner('Pembayaran gagal atau kadaluarsa.', 'error');
            }
        } catch (e) {
            // coba lagi interval berikutnya
        }
    }

    // Restart polling saat user balik ke tab (setelah 3DS di tab lain)
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible') {
            checkStatus(); // cek langsung tanpa tunggu interval
            startPolling();
        } else {
            clearInterval(pollInterval);
        }
    });
})();
</script>

@push('scripts')
<script type="text/javascript"
    id="midtrans-script"
    src="https://api.{{ config('midtrans.is_production') ? '' : 'sandbox.' }}midtrans.com/v2/assets/js/midtrans-new-3ds.min.js"
    data-environment="{{ config('midtrans.is_production') ? 'production' : 'sandbox' }}"
    data-client-key="{{ config('midtrans.client_key') }}"></script>
@endpush

@endif
@endsection

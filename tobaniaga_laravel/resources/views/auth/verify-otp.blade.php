@extends('layouts.guest')

@section('title', 'Verifikasi Email — TobaNiaga')
@section('hide_navbar', true)
@section('content')
<div class="min-h-screen flex">

    {{-- Panel kiri visual --}}
    <div class="hidden lg:block lg:w-[42%] relative bg-lake-800 overflow-hidden">
        <div class="absolute inset-0 opacity-90 ulos-stripe-v"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-lake-900/20 via-transparent to-lake-900/70"></div>
        <div class="absolute inset-0 flex flex-col justify-end p-14">
            <p class="font-display italic text-paper text-2xl leading-snug">
                "Satu langkah lagi untuk bergabung bersama UMKM Toba."
            </p>
            <p class="mt-5 font-mono text-xs uppercase tracking-[0.2em] text-paper/60">TobaNiaga — Verifikasi Email</p>
        </div>
    </div>

    {{-- Kolom kanan: form OTP --}}
    <div class="w-full lg:w-[58%] flex flex-col px-6 sm:px-12 lg:px-20 py-10">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded w-fit">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>

        <div class="flex-1 flex flex-col justify-center max-w-md w-full mx-auto py-12">

            {{-- Icon --}}
            <div class="w-14 h-14 rounded-2xl bg-lake-800 flex items-center justify-center mb-7">
                <svg class="w-6 h-6 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="font-display text-3xl font-medium text-lake-900 mb-2">Cek emailmu</h1>
            <p class="text-ink/60 mb-1">Kami mengirim kode 6 digit ke:</p>
            <p class="font-mono text-sm font-medium text-lake-900 mb-8">{{ $email }}</p>

            {{-- Flash info --}}
            @if(session('info'))
                <div class="mb-5 rounded-lg bg-lake-50 border border-lake-800/20 text-lake-900 text-sm px-4 py-3">
                    {{ session('info') }}
                </div>
            @endif

            {{-- Error --}}
            @if($errors->any())
                <div class="mb-5 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-ulos-maroon text-sm px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Form OTP --}}
            <form method="POST" action="{{ route('otp.verify') }}" x-data="otpInput()">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="mb-6">
                    <label class="block text-sm font-medium text-lake-900 mb-3">Kode Verifikasi</label>
                    {{-- 6 kotak OTP --}}
                    <div class="flex gap-3" @paste.prevent="handlePaste($event)">
                        @for($i = 0; $i < 6; $i++)
                        <input type="text"
                               inputmode="numeric"
                               maxlength="1"
                               x-ref="digit{{ $i }}"
                               @input="handleInput($event, {{ $i }})"
                               @keydown.backspace="handleBackspace($event, {{ $i }})"
                               @keydown.left="$refs['digit{{ max(0, $i-1) }}'].focus()"
                               @keydown.right="$refs['digit{{ min(5, $i+1) }}'].focus()"
                               class="w-12 h-14 text-center text-xl font-mono font-semibold text-lake-900 border-2 border-lake-900/15 rounded-xl focus:border-lake-800 focus:outline-none transition-colors bg-paper"
                               autocomplete="one-time-code">
                        @endfor
                    </div>
                    {{-- Hidden input yang dikumpulkan --}}
                    <input type="hidden" name="kode" x-bind:value="kode">
                </div>

                <button type="submit"
                        class="w-full bg-lake-800 text-paper font-semibold py-3 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                    Verifikasi
                </button>
            </form>

            {{-- Kirim ulang --}}
            <div class="mt-7 text-center" x-data="resendTimer()">
                <p class="text-sm text-ink/60">Tidak menerima kode?</p>
                <div x-show="detik > 0" class="mt-1">
                    <span class="font-mono text-xs text-ink/40">Kirim ulang dalam <span x-text="detik"></span>s</span>
                </div>
                <div x-show="detik === 0" class="mt-1">
                    <form method="POST" action="{{ route('otp.resend') }}" class="inline">
                        @csrf
                        <input type="hidden" name="email" value="{{ $email }}">
                        <button type="submit"
                                class="text-sm font-semibold text-ulos-maroon hover:underline focus-ring rounded">
                            Kirim ulang kode
                        </button>
                    </form>
                </div>
            </div>

            <p class="mt-8 text-center text-sm text-ink/60">
                Email salah?
                <a href="{{ route('register') }}" class="font-semibold text-ulos-maroon hover:underline focus-ring rounded">Daftar ulang</a>
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
function otpInput() {
    return {
        kode: '',
        handleInput(e, idx) {
            const val = e.target.value.replace(/\D/g, '');
            e.target.value = val;
            if (val && idx < 5) {
                this.$refs['digit' + (idx + 1)].focus();
            }
            this.updateKode();
        },
        handleBackspace(e, idx) {
            if (!e.target.value && idx > 0) {
                this.$refs['digit' + (idx - 1)].focus();
            }
            this.updateKode();
        },
        handlePaste(e) {
            const text = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
            text.split('').forEach((ch, i) => {
                if (this.$refs['digit' + i]) this.$refs['digit' + i].value = ch;
            });
            if (text.length > 0) {
                const last = Math.min(text.length, 5);
                this.$refs['digit' + last].focus();
            }
            this.updateKode();
        },
        updateKode() {
            this.kode = [0,1,2,3,4,5].map(i => this.$refs['digit'+i]?.value || '').join('');
        }
    }
}

function resendTimer() {
    return {
        detik: 60,
        init() {
            const interval = setInterval(() => {
                if (this.detik > 0) this.detik--;
                else clearInterval(interval);
            }, 1000);
        }
    }
}
</script>
@endpush
@endsection

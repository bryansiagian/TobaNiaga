<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpMail;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class OtpController extends Controller
{
    /**
     * Tampilkan halaman input OTP.
     * Email disimpan di session saat register berhasil.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('otp_email')) {
            return redirect()->route('register');
        }

        return view('auth.verify-otp', [
            'email' => $request->session()->get('otp_email'),
        ]);
    }

    /**
     * Verifikasi kode OTP yang diinput user.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'kode' => ['required', 'string', 'size:6'],
        ], [
            'kode.required' => 'Kode OTP wajib diisi.',
            'kode.size'     => 'Kode OTP harus terdiri dari 6 digit.',
        ]);

        $email = $request->session()->get('otp_email');

        if (! $email) {
            return redirect()->route('register')
                ->withErrors(['kode' => 'Sesi tidak valid. Silakan daftar ulang.']);
        }

        $otp = OtpCode::where('email', $email)
            ->where('kode', $request->kode)
            ->where('digunakan', false)
            ->where('kadaluarsa_at', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return back()->withErrors([
                'kode' => 'Kode OTP tidak valid atau sudah kadaluarsa.',
            ]);
        }

        // Tandai OTP sudah digunakan
        $otp->update(['digunakan' => true]);

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('register')
                ->withErrors(['kode' => 'Akun tidak ditemukan. Silakan daftar ulang.']);
        }

        // Customer → langsung aktifkan
        // Sales → tetap nonaktif, tunggu approval admin
        if ($user->hasRole('customer')) {
            $statusAktifId = DB::table('status_user')
                ->where('kode', 'aktif')
                ->value('id');

            $user->update([
                'status_id'          => $statusAktifId,
                'email_verified_at'  => now(),
            ]);

            $request->session()->forget('otp_email');

            return redirect()->route('login')
                ->with('status', 'Email berhasil diverifikasi! Silakan masuk ke akunmu.');
        }

        // Sales: email verified, tapi akun masih menunggu approval admin
        $user->update(['email_verified_at' => now()]);

        $request->session()->forget('otp_email');

        return redirect()->route('login')
            ->with('status', 'Email berhasil diverifikasi! Akunmu sedang menunggu persetujuan admin. Kamu akan mendapat notifikasi melalui email.');
    }

    /**
     * Kirim ulang OTP (resend).
     */
    public function resend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('otp_email');

        if (! $email) {
            return redirect()->route('register');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('register');
        }

        // Cek apakah masih ada OTP valid yang belum kadaluarsa (rate limit sederhana)
        $otpAktif = OtpCode::where('email', $email)
            ->where('digunakan', false)
            ->where('kadaluarsa_at', '>', now()->subMinutes(1)) // minimal tunggu 1 menit
            ->exists();

        if ($otpAktif) {
            return back()->withErrors([
                'kode' => 'Tunggu sebentar sebelum meminta kode baru.',
            ]);
        }

        $kode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email'         => $email,
            'kode'          => $kode,
            'kadaluarsa_at' => now()->addMinutes(10),
        ]);

        Mail::to($email)->send(new OtpMail($kode, $user->nama));

        return back()->with('status', 'Kode OTP baru telah dikirim ke emailmu.');
    }
}

<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\EmailChangeToken;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CourierProfilController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        return view('courier.profil.index', compact('user'));
    }

    // ── Update profil pribadi ──────────────────────────────────
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'no_hp'         => 'nullable|string|max:20',
            'tanggal_lahir' => 'nullable|date|before:today',
            'foto_profil'   => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $validated['foto_profil'] = $request->file('foto_profil')
                ->store('foto_profil', 'public');
        } else {
            unset($validated['foto_profil']);
        }

        $user->update($validated);

        return back()->with('status_profil', 'Profil pribadi berhasil diperbarui.');
    }

    // ── Kirim OTP ke email baru ─────────────────────────────────
    public function kirimOtpEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email_baru' => 'required|email|unique:users,email',
        ]);

        $user = Auth::user();
        $otp  = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailChangeToken::where('user_id', $user->id)->delete();

        EmailChangeToken::create([
            'user_id'    => $user->id,
            'email_baru' => $request->email_baru,
            'otp'        => $otp,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($request->email_baru)->send(
            new \App\Mail\EmailChangeOtp($otp, $user->nama)
        );

        return back()->with('status_email', 'Kode OTP dikirim ke ' . $request->email_baru . '. Berlaku 10 menit.');
    }

    // ── Verifikasi OTP dan ganti email ──────────────────────────
    public function verifikasiOtpEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => 'required|string|size:6',
        ]);

        $user  = Auth::user();
        $token = EmailChangeToken::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$token) {
            return back()->withErrors(['otp' => 'Kode OTP salah atau sudah kadaluarsa.']);
        }

        $user->update(['email' => $token->email_baru]);
        $token->delete();

        return back()->with('status_email', 'Email berhasil diperbarui.');
    }

    // ── Update password ──────────────────────────────────────────
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama' => 'required|string',
            'password_baru' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('status_password', 'Password berhasil diperbarui.');
    }
}

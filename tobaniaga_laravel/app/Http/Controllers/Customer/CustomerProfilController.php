<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AlamatCustomer;
use App\Models\EmailChangeToken;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class CustomerProfilController extends Controller
{
    public function index(): View
    {
        $user   = Auth::user();
        $alamat = AlamatCustomer::where('user_id', $user->id)
            ->orderByDesc('is_utama')
            ->orderBy('created_at')
            ->get();

        return view('customer.profil.index', compact('user', 'alamat'));
    }

    // ── Update profil umum ──────────────────────────────────────
    public function updateProfil(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama'           => 'required|string|max:100',
            'no_hp'          => 'nullable|string|max:20',
            'tanggal_lahir'  => 'nullable|date|before:today',
            'foto_profil'    => 'nullable|image|max:2048',
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

        return back()->with('status_profil', 'Profil berhasil diperbarui.');
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

    // ── Update password ─────────────────────────────────────────
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password_lama'     => 'required|string',
            'password_baru'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password_lama, $user->password)) {
            return back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return back()->with('status_password', 'Password berhasil diperbarui.');
    }

    // ── Set alamat utama ────────────────────────────────────────
    public function setAlamatUtama(AlamatCustomer $alamat): RedirectResponse
    {
        abort_unless($alamat->user_id === Auth::id(), 403);

        AlamatCustomer::where('user_id', Auth::id())->update(['is_utama' => false]);
        $alamat->update(['is_utama' => true]);

        return back()->with('status_alamat', 'Alamat utama berhasil diubah.');
    }

    // ── Update alamat ───────────────────────────────────────────
    public function updateAlamat(Request $request, AlamatCustomer $alamat): RedirectResponse
    {
        abort_unless($alamat->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'label'          => 'required|string|max:50',
            'nama_penerima'  => 'required|string|max:100',
            'no_hp_penerima' => 'required|string|max:20',
            'provinsi'       => 'required|string|max:100',
            'kota'           => 'required|string|max:100',
            'kecamatan'      => 'required|string|max:100',
            'kelurahan'      => 'required|string|max:100',
            'kode_pos'       => 'nullable|string|max:10',
            'alamat_lengkap' => 'required|string|max:500',
        ]);

        $alamat->update($validated);

        return back()->with('status_alamat', 'Alamat berhasil diperbarui.');
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\KirimOtp;
use App\Models\OtpCode;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    // ── Login ──────────────────────────────────────────────────

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi yang dimasukkan salah.'])
                ->onlyInput('email');
        }

        // Email belum diverifikasi OTP
        if (is_null($user->email_verified_at)) {
            // Kirim ulang OTP dan arahkan ke halaman verifikasi
            $this->buatDanKirimOtp($user);
            return redirect()
                ->route('otp.form', ['email' => $user->email])
                ->with('info', 'Email kamu belum diverifikasi. Kode OTP baru telah dikirim.');
        }

        // Cek status akun
        $kodeStatus = $user->status?->kode;

        if ($kodeStatus === 'nonaktif') {
            // Bisa berarti sales yang menunggu approval admin
            $umkm = $user->umkm;
            if ($umkm && $umkm->statusVerifikasi?->kode === 'pending') {
                return back()
                    ->withErrors(['email' => 'Akunmu masih menunggu verifikasi admin. Kami akan menghubungimu melalui email setelah disetujui.'])
                    ->onlyInput('email');
            }

            if ($umkm && $umkm->statusVerifikasi?->kode === 'rejected') {
                return back()
                    ->withErrors(['email' => 'Pendaftaran UMKM-mu ditolak. Hubungi admin untuk informasi lebih lanjut.'])
                    ->onlyInput('email');
            }

            return back()
                ->withErrors(['email' => 'Akun ini tidak aktif. Hubungi admin.'])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi yang dimasukkan salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectByRole($request->user());
    }

    // ── Register ───────────────────────────────────────────────

    public function showRegister(): View
    {
        $kategori = DB::table('kategori_umkm')->orderBy('nama')->get();
        return view('auth.register', compact('kategori'));
    }

    public function register(Request $request): RedirectResponse
    {
        $rules = [
            'role'     => ['required', 'in:customer,sales'],
            'nama'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_hp'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];

        // Validasi tambahan khusus sales
        if ($request->input('role') === 'sales') {
            $rules = array_merge($rules, [
                'kategori_id'  => ['required', 'exists:kategori_umkm,id'],
                'nama_umkm'    => ['required', 'string', 'max:150'],
                'deskripsi'    => ['required', 'string', 'max:1000'],
                'alamat'       => ['required', 'string', 'max:255'],
                'kecamatan'    => ['required', 'string', 'max:100'],
                'desa'         => ['required', 'string', 'max:100'],
                'no_hp_wa'     => ['required', 'string', 'max:20'],
            ]);
        }

        $validated = $request->validate($rules, [
            'kategori_id.required'  => 'Kategori UMKM wajib dipilih.',
            'kategori_id.exists'    => 'Kategori tidak valid.',
            'nama_umkm.required'    => 'Nama UMKM wajib diisi.',
            'deskripsi.required'    => 'Deskripsi UMKM wajib diisi.',
            'alamat.required'       => 'Alamat usaha wajib diisi.',
            'kecamatan.required'    => 'Kecamatan wajib dipilih.',
            'desa.required'         => 'Desa/Kelurahan wajib dipilih.',
            'no_hp_wa.required'     => 'Nomor WhatsApp wajib diisi.',
        ]);

        $statusNonaktifId = DB::table('status_user')->where('kode', 'nonaktif')->value('id');
        $statusAktifId    = DB::table('status_user')->where('kode', 'aktif')->value('id');

        $user = DB::transaction(function () use ($validated, $statusNonaktifId, $statusAktifId) {
            $isSales = $validated['role'] === 'sales';

            $user = User::create([
                'nama'      => $validated['nama'],
                'email'     => $validated['email'],
                'no_hp'     => $validated['no_hp'],
                'password'  => Hash::make($validated['password']),
                // Semua user baru nonaktif dulu sampai OTP diverifikasi
                'status_id' => $statusNonaktifId,
            ]);

            $user->assignRole($validated['role']);

            if ($isSales) {
                $statusVerifPendingId = DB::table('status_verifikasi_umkm')->where('kode', 'pending')->value('id');
                $statusUmkmAktifId    = DB::table('status_umkm')->where('kode', 'aktif')->value('id');

                Umkm::create([
                    'owner_id'             => $user->id,
                    'kategori_id'          => $validated['kategori_id'],
                    'nama_umkm'            => $validated['nama_umkm'],
                    'slug'                 => $this->buatSlugUnik($validated['nama_umkm']),
                    'deskripsi'            => $validated['deskripsi'],
                    'alamat'               => $validated['alamat'],
                    'provinsi'             => 'Sumatera Utara',
                    'kabupaten'            => 'Kabupaten Toba',
                    'kecamatan'            => $validated['kecamatan'],
                    'desa'                 => $validated['desa'],
                    'no_hp_wa'             => $validated['no_hp_wa'],
                    'status_verifikasi_id' => $statusVerifPendingId,
                    'status_id'            => $statusUmkmAktifId,
                ]);
            }

            return $user;
        });

        // Kirim OTP
        $this->buatDanKirimOtp($user);

        return redirect()
            ->route('otp.form', ['email' => $user->email])
            ->with('info', 'Kode verifikasi telah dikirim ke ' . $user->email . '. Periksa folder inbox atau spam.');
    }

    // ── OTP ────────────────────────────────────────────────────

    public function showOtpForm(Request $request): View
    {
        return view('auth.verify-otp', [
            'email' => $request->query('email', ''),
        ]);
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'kode'  => ['required', 'digits:6'],
        ], [
            'kode.digits' => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        $otp = OtpCode::where('email', $request->email)
            ->where('kode', $request->kode)
            ->where('sudah_dipakai', false)
            ->where('kadaluarsa_pada', '>', now())
            ->latest()
            ->first();

        if (! $otp) {
            return back()
                ->withErrors(['kode' => 'Kode OTP tidak valid atau sudah kadaluarsa.'])
                ->withInput();
        }

        // Tandai OTP sudah dipakai
        $otp->update(['sudah_dipakai' => true]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Verifikasi email
        $user->update(['email_verified_at' => now()]);

        $statusAktifId = DB::table('status_user')->where('kode', 'aktif')->value('id');

        if ($user->hasRole('customer')) {
            // Customer langsung aktif
            $user->update(['status_id' => $statusAktifId]);
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('welcome')
                ->with('status', 'Email berhasil diverifikasi. Selamat datang di TobaNiaga!');
        }

        // Sales — tetap nonaktif, tunggu admin
        return redirect()
            ->route('login')
            ->with('status', 'Email berhasil diverifikasi! Akunmu sedang menunggu verifikasi admin. Kami akan menghubungimu via email setelah disetujui.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }

        if (! is_null($user->email_verified_at)) {
            return redirect()->route('login')
                ->with('info', 'Email sudah diverifikasi. Silakan login.');
        }

        // Rate limit sederhana: cek apakah OTP terakhir dibuat < 1 menit yang lalu
        $otpTerakhir = OtpCode::where('email', $user->email)
            ->latest()
            ->first();

        if ($otpTerakhir && $otpTerakhir->created_at->diffInSeconds(now()) < 60) {
            return back()->withErrors(['kode' => 'Tunggu sebentar sebelum meminta kode baru.']);
        }

        $this->buatDanKirimOtp($user);

        return back()->with('info', 'Kode OTP baru telah dikirim ke ' . $user->email);
    }

    // ── Logout ─────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    // ── Helpers ────────────────────────────────────────────────

    private function buatDanKirimOtp(User $user): void
    {
        // Hapus OTP lama yang belum dipakai untuk email ini
        OtpCode::where('email', $user->email)
            ->where('sudah_dipakai', false)
            ->delete();

        $kode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'email'            => $user->email,
            'kode'             => $kode,
            'kadaluarsa_pada'  => now()->addMinutes(10),
            'sudah_dipakai'    => false,
        ]);

        Mail::to($user->email)->send(new KirimOtp($kode, $user->nama));
    }

    private function buatSlugUnik(string $namaUmkm): string
    {
        $slug = Str::slug($namaUmkm);
        $asli = $slug;
        $i    = 1;

        while (DB::table('umkm')->where('slug', $slug)->exists()) {
            $slug = $asli . '-' . $i++;
        }

        return $slug;
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole('admin'))   return redirect()->route('admin.dashboard');
        if ($user->hasRole('sales'))   return redirect()->route('sales.dashboard');
        if ($user->hasRole('courier')) return redirect()->route('courier.dashboard');
        return redirect()->route('welcome');
    }
}

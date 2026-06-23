<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Konstanta status user, disesuaikan dengan tabel referensi status_user.
     * 1 = Menunggu Persetujuan, 2 = Aktif, 3 = Ditolak
     */
    private const STATUS_PENDING = 1;
    private const STATUS_AKTIF   = 2;
    private const STATUS_DITOLAK = 3;

    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
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

        // Cek status approval sebelum izinkan login
        if ($user->status_user_id === self::STATUS_PENDING) {
            return back()
                ->withErrors(['email' => 'Akunmu masih menunggu persetujuan admin. Silakan tunggu konfirmasi melalui email.'])
                ->onlyInput('email');
        }

        if ($user->status_user_id === self::STATUS_DITOLAK) {
            return back()
                ->withErrors(['email' => 'Pendaftaran akunmu ditolak. Hubungi admin untuk informasi lebih lanjut.'])
                ->onlyInput('email');
        }

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Email atau kata sandi yang dimasukkan salah.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return $this->redirectByRole($request->user());
    }

    /**
     * Tampilkan halaman register.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Proses pendaftaran akun baru (customer atau pemilik UMKM).
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role'     => ['required', 'in:customer,sales'],
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'no_hp'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],

            // Wajib hanya jika role = umkm
            'nama_umkm'   => ['required_if:role,umkm', 'nullable', 'string', 'max:150'],
            'alamat_umkm' => ['required_if:role,umkm', 'nullable', 'string', 'max:255'],
        ], [
            'nama_umkm.required_if'   => 'Nama UMKM wajib diisi untuk pendaftaran pemilik UMKM.',
            'alamat_umkm.required_if' => 'Alamat usaha wajib diisi untuk pendaftaran pemilik UMKM.',
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'           => $validated['name'],
                'email'          => $validated['email'],
                'no_hp'          => $validated['no_hp'],
                'password'       => Hash::make($validated['password']),
                // Semua pendaftaran baru menunggu approval admin terlebih dahulu
                'status_user_id' => self::STATUS_PENDING,
            ]);

            // Assign role Spatie sesuai pilihan saat register
            $user->assignRole($validated['role'] === 'sales' ? 'sales' : 'customer');

            // Jika daftar sebagai pemilik UMKM, langsung buat entri UMKM terkait
            if ($validated['role'] === 'sales') {
                Umkm::create([
                    'user_id'         => $user->id,
                    'nama_umkm'       => $validated['nama_umkm'],
                    'alamat_umkm'     => $validated['alamat_umkm'],
                    'status_umkm_id'  => self::STATUS_PENDING,
                ]);
            }

            return $user;
        });

        return redirect()
            ->route('login')
            ->with('status', 'Pendaftaran berhasil! Akunmu sedang menunggu persetujuan admin. Kamu akan menerima email konfirmasi setelah disetujui.');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Arahkan user ke dashboard sesuai role aktifnya.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('sales')) {
            return redirect()->route('sales.dashboard');
        }

        if ($user->hasRole('courier')) {
            return redirect()->route('courier.dashboard');
        }

        // customer — kembali ke welcome
        return redirect()->route('welcome');
    }
}

<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SalesProfilController extends Controller
{
    private function umkm()
    {
        return Auth::user()->umkm;
    }

    public function index(): View
    {
        $umkm = $this->umkm();
        return view('sales.profil.index', compact('umkm'));
    }

    public function update(Request $request): RedirectResponse
    {
        $umkm = $this->umkm();

        $validated = $request->validate([
            'nama_umkm'   => ['required', 'string', 'max:255'],
            'deskripsi'   => ['nullable', 'string'],
            'alamat'      => ['nullable', 'string', 'max:500'],
            'provinsi'    => ['nullable', 'string', 'max:100'],
            'kabupaten'   => ['nullable', 'string', 'max:100'],
            'kecamatan'   => ['nullable', 'string', 'max:100'],
            'desa'        => ['nullable', 'string', 'max:100'],
            'no_hp_wa'    => ['nullable', 'string', 'max:20'],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:2048'],
            'foto_banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:2048'],
        ]);

        if ($request->hasFile('foto_profil')) {
            if ($umkm->foto_profil) {
                Storage::disk('public')->delete($umkm->foto_profil);
            }
            $validated['foto_profil'] = $request->file('foto_profil')->store('umkm/profil', 'public');
        } else {
            unset($validated['foto_profil']);
        }

        if ($request->hasFile('foto_banner')) {
            if ($umkm->foto_banner) {
                Storage::disk('public')->delete($umkm->foto_banner);
            }
            $validated['foto_banner'] = $request->file('foto_banner')->store('umkm/banner', 'public');
        } else {
            unset($validated['foto_banner']);
        }

        $umkm->update($validated);

        return back()->with('status', 'Profil UMKM berhasil diperbarui.');
    }
}

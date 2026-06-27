<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPromoController extends Controller
{
    public function index(): View
    {
        $promos = Promo::whereNull('umkm_id')
                       ->with(['umkmTarget'])
                       ->latest()
                       ->paginate(10);

        $umkmList = Umkm::whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'verified'))
                        ->orderBy('nama_umkm')
                        ->get();

        return view('admin.promo.index', compact('promos', 'umkmList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode'           => ['required', 'string', 'max:32', 'unique:promo,kode', 'regex:/^[A-Z0-9_-]+$/'],
            'nama_promo'     => ['required', 'string', 'max:100'],
            'deskripsi'      => ['nullable', 'string', 'max:300'],
            'tipe'           => ['required', 'in:persen,nominal'],
            'nilai'          => ['required', 'numeric', 'min:1'],
            'min_belanja'    => ['nullable', 'numeric', 'min:0'],
            'maks_diskon'    => ['nullable', 'numeric', 'min:0'],
            'kuota'          => ['nullable', 'integer', 'min:1'],
            'berlaku_mulai'  => ['required', 'date'],
            'berlaku_sampai' => ['required', 'date', 'after_or_equal:berlaku_mulai'],
            'umkm_ids'       => ['nullable', 'array'],
            'umkm_ids.*'     => ['exists:umkm,id'],
        ]);

        $promo = Promo::create([
            'umkm_id'        => null, // selalu null untuk admin
            'kode'           => strtoupper($validated['kode']),
            'nama_promo'     => $validated['nama_promo'],
            'deskripsi'      => $validated['deskripsi'] ?? null,
            'tipe'           => $validated['tipe'],
            'nilai'          => $validated['nilai'],
            'min_belanja'    => $validated['min_belanja'] ?? 0,
            'maks_diskon'    => $validated['maks_diskon'] ?? null,
            'kuota'          => $validated['kuota'] ?? null,
            'terpakai'       => 0,
            'berlaku_mulai'  => $validated['berlaku_mulai'],
            'berlaku_sampai' => $validated['berlaku_sampai'],
            'is_aktif'       => true,
        ]);

        if (!empty($validated['umkm_ids'])) {
            $promo->umkmTarget()->sync($validated['umkm_ids']);
        }

        return back()->with('status', 'Promo berhasil dibuat.');
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        abort_if($promo->umkm_id !== null, 403);

        $validated = $request->validate([
            'kode'           => ['required', 'string', 'max:32', 'unique:promo,kode,' . $promo->id, 'regex:/^[A-Z0-9_-]+$/'],
            'nama_promo'     => ['required', 'string', 'max:100'],
            'deskripsi'      => ['nullable', 'string', 'max:300'],
            'tipe'           => ['required', 'in:persen,nominal'],
            'nilai'          => ['required', 'numeric', 'min:1'],
            'min_belanja'    => ['nullable', 'numeric', 'min:0'],
            'maks_diskon'    => ['nullable', 'numeric', 'min:0'],
            'kuota'          => ['nullable', 'integer', 'min:1'],
            'berlaku_mulai'  => ['required', 'date'],
            'berlaku_sampai' => ['required', 'date', 'after_or_equal:berlaku_mulai'],
            'is_aktif'       => ['nullable', 'boolean'],
            'umkm_ids'       => ['nullable', 'array'],
            'umkm_ids.*'     => ['exists:umkm,id'],
        ]);

        $promo->update([
            'kode'           => strtoupper($validated['kode']),
            'nama_promo'     => $validated['nama_promo'],
            'deskripsi'      => $validated['deskripsi'] ?? null,
            'tipe'           => $validated['tipe'],
            'nilai'          => $validated['nilai'],
            'min_belanja'    => $validated['min_belanja'] ?? 0,
            'maks_diskon'    => $validated['maks_diskon'] ?? null,
            'kuota'          => $validated['kuota'] ?? null,
            'berlaku_mulai'  => $validated['berlaku_mulai'],
            'berlaku_sampai' => $validated['berlaku_sampai'],
            'is_aktif'       => $request->boolean('is_aktif', true),
        ]);

        $promo->umkmTarget()->sync($validated['umkm_ids'] ?? []);

        return back()->with('status', 'Promo berhasil diperbarui.');
    }

    public function toggle(Promo $promo): RedirectResponse
    {
        abort_if($promo->umkm_id !== null, 403);
        $promo->update(['is_aktif' => !$promo->is_aktif]);
        return back()->with('status', $promo->is_aktif ? 'Promo diaktifkan.' : 'Promo dinonaktifkan.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        abort_if($promo->umkm_id !== null, 403);
        $promo->umkmTarget()->detach();
        $promo->produk()->detach();
        $promo->delete();
        return back()->with('status', 'Promo berhasil dihapus.');
    }
}

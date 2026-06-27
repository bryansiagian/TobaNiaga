<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Promo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesPromoController extends Controller
{
    private function umkm()
    {
        return Auth::user()->umkm;
    }

    public function index(): View
    {
        $umkm = $this->umkm();

        $promos = $umkm
            ? Promo::where(function ($q) use ($umkm) {
                    // Promo milik sales ini sendiri
                    $q->where('umkm_id', $umkm->id)
                    // ATAU promo admin yang menarget UMKM ini
                    ->orWhere(function ($q2) use ($umkm) {
                        $q2->whereNull('umkm_id')
                        ->whereHas('umkmTarget', fn($q3) => $q3->where('umkm_id', $umkm->id));
                    })
                    // ATAU promo admin platform-wide (tidak ada target spesifik)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('umkm_id')
                        ->whereDoesntHave('umkmTarget');
                    });
                })
                ->with('produk', 'umkmTarget')
                ->latest()
                ->paginate(10)
            : collect();

        $produk = $umkm?->produk()
            ->whereHas('status', fn($q) => $q->where('kode', 'tersedia'))
            ->get() ?? collect();

        return view('sales.promo.index', compact('promos', 'produk', 'umkm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if(!$umkm, 403);

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
            'produk_ids'     => ['nullable', 'array'],
            'produk_ids.*'   => ['exists:produk,id'],
        ]);

        $promo = Promo::create([
            'umkm_id'        => $umkm->id,
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

        if (!empty($validated['produk_ids'])) {
            $validIds = $umkm->produk()->whereIn('id', $validated['produk_ids'])->pluck('id');
            $promo->produk()->sync($validIds);
        }

        return back()->with('status', 'Promo berhasil dibuat.');
    }

    public function update(Request $request, Promo $promo): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if($promo->umkm_id !== $umkm?->id, 403);

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
            'produk_ids'     => ['nullable', 'array'],
            'produk_ids.*'   => ['exists:produk,id'],
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

        $validIds = !empty($validated['produk_ids'])
            ? $umkm->produk()->whereIn('id', $validated['produk_ids'])->pluck('id')
            : [];
        $promo->produk()->sync($validIds);

        return back()->with('status', 'Promo berhasil diperbarui.');
    }

    public function toggle(Promo $promo): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if($promo->umkm_id !== $umkm?->id, 403);

        $promo->update(['is_aktif' => !$promo->is_aktif]);

        return back()->with('status', $promo->is_aktif ? 'Promo diaktifkan.' : 'Promo dinonaktifkan.');
    }

    public function destroy(Promo $promo): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if($promo->umkm_id !== $umkm?->id, 403);

        $promo->produk()->detach();
        $promo->delete();

        return back()->with('status', 'Promo berhasil dihapus.');
    }
}

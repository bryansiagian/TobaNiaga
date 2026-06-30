<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OngkosKirimTrayek;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOngkirController extends Controller
{
    public function index(): View
    {
        // Ambil kecamatan dari emsifa API (Kabupaten Toba)
        $semuaKecamatan = collect();
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->get('https://www.emsifa.com/api-wilayah-indonesia/api/districts/1206.json');

            if ($response->successful()) {
                $semuaKecamatan = collect($response->json())->pluck('name')->sort()->values();
            }
        } catch (\Exception $e) {
            // Gagal fetch — fallback ke data lokal
        }

        // Fallback kalau API gagal/down: ambil dari data yang sudah ada di DB
        if ($semuaKecamatan->isEmpty()) {
            $kecamatanUmkm = Umkm::whereNotNull('kecamatan')->distinct()->pluck('kecamatan');
            $kecamatanCustomer = \App\Models\AlamatCustomer::whereNotNull('kecamatan')->distinct()->pluck('kecamatan');
            $semuaKecamatan = $kecamatanUmkm->merge($kecamatanCustomer)->unique()->sort()->values();
        }

        $trayek = OngkosKirimTrayek::orderBy('lokasi_asal')->orderBy('lokasi_tujuan')->get();

        $pasanganDitampilkan = collect();
        $trayekUnik = $trayek->filter(function ($t) use (&$pasanganDitampilkan) {
            $kunciBalik = $t->lokasi_tujuan . '|' . $t->lokasi_asal;
            if ($pasanganDitampilkan->contains($kunciBalik)) {
                return false;
            }
            $pasanganDitampilkan->push($t->lokasi_asal . '|' . $t->lokasi_tujuan);
            return true;
        })->values();

        return view('admin.ongkir.index', compact('trayekUnik', 'semuaKecamatan'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_asal'   => ['required', 'string', 'max:100'],
            'lokasi_tujuan' => ['required', 'string', 'max:100', 'different:lokasi_asal'],
            'ongkos'        => ['required', 'numeric', 'min:0'],
        ]);

        OngkosKirimTrayek::buatSimetris(
            $validated['lokasi_asal'],
            $validated['lokasi_tujuan'],
            $validated['ongkos'],
        );

        return back()->with('status', 'Trayek ongkir berhasil dibuat (dua arah).');
    }

    public function update(Request $request, OngkosKirimTrayek $ongkosKirimTrayek): RedirectResponse
    {
        $validated = $request->validate([
            'lokasi_asal'   => ['required', 'string', 'max:100'],
            'lokasi_tujuan' => ['required', 'string', 'max:100', 'different:lokasi_asal'],
            'ongkos'        => ['required', 'numeric', 'min:0'],
            'is_aktif'      => ['nullable', 'boolean'],
        ]);

        // Hapus pasangan lama dulu (kalau lokasi berubah) baru buat ulang simetris
        $asalLama   = $ongkosKirimTrayek->lokasi_asal;
        $tujuanLama = $ongkosKirimTrayek->lokasi_tujuan;

        OngkosKirimTrayek::where(function ($q) use ($asalLama, $tujuanLama) {
            $q->where(['lokasi_asal' => $asalLama, 'lokasi_tujuan' => $tujuanLama])
              ->orWhere(['lokasi_asal' => $tujuanLama, 'lokasi_tujuan' => $asalLama]);
        })->delete();

        OngkosKirimTrayek::buatSimetris(
            $validated['lokasi_asal'],
            $validated['lokasi_tujuan'],
            $validated['ongkos'],
            $request->boolean('is_aktif', true),
        );

        return back()->with('status', 'Trayek ongkir berhasil diperbarui.');
    }

    public function toggle(OngkosKirimTrayek $ongkosKirimTrayek): RedirectResponse
    {
        $statusBaru = !$ongkosKirimTrayek->is_aktif;

        // Toggle dua arah sekaligus supaya tetap simetris
        OngkosKirimTrayek::where(function ($q) use ($ongkosKirimTrayek) {
            $q->where(['lokasi_asal' => $ongkosKirimTrayek->lokasi_asal, 'lokasi_tujuan' => $ongkosKirimTrayek->lokasi_tujuan])
              ->orWhere(['lokasi_asal' => $ongkosKirimTrayek->lokasi_tujuan, 'lokasi_tujuan' => $ongkosKirimTrayek->lokasi_asal]);
        })->update(['is_aktif' => $statusBaru]);

        return back()->with('status', $statusBaru ? 'Trayek diaktifkan.' : 'Trayek dinonaktifkan.');
    }

    public function destroy(OngkosKirimTrayek $ongkosKirimTrayek): RedirectResponse
    {
        // Hapus dua arah sekaligus
        OngkosKirimTrayek::where(function ($q) use ($ongkosKirimTrayek) {
            $q->where(['lokasi_asal' => $ongkosKirimTrayek->lokasi_asal, 'lokasi_tujuan' => $ongkosKirimTrayek->lokasi_tujuan])
              ->orWhere(['lokasi_asal' => $ongkosKirimTrayek->lokasi_tujuan, 'lokasi_tujuan' => $ongkosKirimTrayek->lokasi_asal]);
        })->delete();

        return back()->with('status', 'Trayek ongkir berhasil dihapus.');
    }
}

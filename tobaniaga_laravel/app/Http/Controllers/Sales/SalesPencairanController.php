<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\PencairanDana;
use App\Models\PencairanDanaDetail;
use App\Models\RekeningBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesPencairanController extends Controller
{
    private function umkm()
    {
        return Auth::user()->umkm;
    }

    public function index(): View
    {
        $umkm = $this->umkm();

        if (!$umkm) {
            return view('sales.pencairan.index', [
                'umkm' => null, 'rekening' => collect(), 'pesananEligible' => collect(),
                'saldoTersedia' => 0, 'totalDiproses' => 0, 'totalDicairkan' => 0,
                'riwayat' => collect(),
            ]);
        }

        $rekening        = RekeningBank::where('umkm_id', $umkm->id)->orderByDesc('is_utama')->get();
        $pesananEligible = $umkm->pesananEligibleDicairkan()->with('detail')->latest()->get();
        $saldoTersedia   = $pesananEligible->sum(fn($p) => $p->total_harga - $p->ongkos_kirim);
        $totalDiproses   = $umkm->totalSedangDiproses();
        $totalDicairkan  = $umkm->totalSudahDicairkan();

        $riwayat = PencairanDana::where('umkm_id', $umkm->id)
            ->with('rekeningBank', 'detail.pesanan')
            ->latest()
            ->paginate(10);

        return view('sales.pencairan.index', compact(
            'umkm', 'rekening', 'pesananEligible', 'saldoTersedia',
            'totalDiproses', 'totalDicairkan', 'riwayat',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if(!$umkm, 403);

        $validated = $request->validate([
            'rekening_bank_id' => ['required', 'exists:rekening_bank,id'],
            'pesanan_ids'       => ['required', 'array', 'min:1'],
            'pesanan_ids.*'     => ['integer', 'exists:pesanan,id'],
        ]);

        $rekening = RekeningBank::where('id', $validated['rekening_bank_id'])
            ->where('umkm_id', $umkm->id)
            ->first();

        if (!$rekening) {
            return back()->with('error', 'Rekening tidak valid.');
        }

        DB::transaction(function () use ($umkm, $rekening, $validated) {
            // Ambil ulang pesanan eligible DI DALAM transaction supaya tidak race dengan pengajuan lain
            $pesananValid = $umkm->pesananEligibleDicairkan()
                ->whereIn('pesanan.id', $validated['pesanan_ids'])
                ->lockForUpdate()
                ->get();

            if ($pesananValid->isEmpty()) {
                throw new \Exception('Pesanan yang dipilih sudah tidak tersedia untuk dicairkan.');
            }

            $jumlahTotal = $pesananValid->sum(fn($p) => $p->total_harga - $p->ongkos_kirim);

            $pencairan = PencairanDana::create([
                'no_pencairan'      => 'PCR-' . strtoupper(uniqid()),
                'umkm_id'           => $umkm->id,
                'rekening_bank_id'  => $rekening->id,
                'jumlah'            => $jumlahTotal,
                'status'            => 'diajukan',
            ]);

            foreach ($pesananValid as $p) {
                PencairanDanaDetail::create([
                    'pencairan_dana_id' => $pencairan->id,
                    'pesanan_id'        => $p->id,
                    'jumlah'            => $p->total_harga - $p->ongkos_kirim,
                ]);
            }
        });

        return redirect()->route('sales.pencairan.index')
                         ->with('status', 'Pengajuan pencairan berhasil dikirim. Menunggu diproses admin.');
    }
}

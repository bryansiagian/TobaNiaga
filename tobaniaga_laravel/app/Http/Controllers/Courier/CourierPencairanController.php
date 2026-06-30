<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\PencairanDanaKurir;
use App\Models\PencairanDanaKurirDetail;
use App\Models\RekeningBankKurir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CourierPencairanController extends Controller
{
    public function index(): View
    {
        $courier = Auth::user();

        $rekening           = RekeningBankKurir::where('courier_id', $courier->id)->orderByDesc('is_utama')->get();
        $pengirimanEligible = $courier->pengirimanEligibleDicairkan()->with('pesanan')->latest()->get();
        $saldoTersedia      = $pengirimanEligible->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0);
        $totalDiproses      = $courier->totalSedangDiprosesKurir();
        $totalDicairkan     = $courier->totalSudahDicairkanKurir();

        $riwayat = PencairanDanaKurir::where('courier_id', $courier->id)
            ->with('rekeningBankKurir', 'detail.pengiriman.pesanan')
            ->latest()
            ->paginate(10);

        return view('courier.pencairan.index', compact(
            'rekening', 'pengirimanEligible', 'saldoTersedia',
            'totalDiproses', 'totalDicairkan', 'riwayat',
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $courier = Auth::user();

        $validated = $request->validate([
            'rekening_bank_kurir_id' => ['required', 'exists:rekening_bank_kurir,id'],
            'pengiriman_ids'         => ['required', 'array', 'min:1'],
            'pengiriman_ids.*'       => ['integer', 'exists:pengiriman,id'],
        ]);

        $rekening = RekeningBankKurir::where('id', $validated['rekening_bank_kurir_id'])
            ->where('courier_id', $courier->id)
            ->first();

        if (!$rekening) {
            return back()->with('error', 'Rekening tidak valid.');
        }

        DB::transaction(function () use ($courier, $rekening, $validated) {
            $pengirimanValid = $courier->pengirimanEligibleDicairkan()
                ->whereIn('pengiriman.id', $validated['pengiriman_ids'])
                ->with('pesanan')
                ->lockForUpdate()
                ->get();

            if ($pengirimanValid->isEmpty()) {
                throw new \Exception('Pengiriman yang dipilih sudah tidak tersedia untuk dicairkan.');
            }

            $jumlahTotal = $pengirimanValid->sum(fn($p) => $p->pesanan->ongkos_kirim ?? 0);

            $pencairan = PencairanDanaKurir::create([
                'no_pencairan'           => 'PCRK-' . strtoupper(uniqid()),
                'courier_id'             => $courier->id,
                'rekening_bank_kurir_id' => $rekening->id,
                'jumlah'                 => $jumlahTotal,
                'status'                 => 'diajukan',
            ]);

            foreach ($pengirimanValid as $p) {
                PencairanDanaKurirDetail::create([
                    'pencairan_dana_kurir_id' => $pencairan->id,
                    'pengiriman_id'           => $p->id,
                    'jumlah'                  => $p->pesanan->ongkos_kirim ?? 0,
                ]);
            }
        });

        return redirect()->route('courier.pencairan.index')
                         ->with('status', 'Pengajuan pencairan berhasil dikirim. Menunggu diproses admin.');
    }
}

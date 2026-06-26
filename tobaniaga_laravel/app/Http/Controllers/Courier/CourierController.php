<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\PengirimanLog;
use App\Models\StatusPengiriman;
use App\Models\StatusPesanan;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    // Dashboard — job pool + tugas aktif milik kurir ini
    public function dashboard()
    {
        $user = Auth::user();

        $statusMenunggu = StatusPengiriman::where('kode', 'menunggu_kurir')->firstOrFail();
        $statusSelesai  = StatusPengiriman::where('kode', 'selesai')->firstOrFail();

        // Pool — semua pengiriman yang belum diambil kurir
        $pool = Pengiriman::with([
                'pesanan.umkm',
                'pesanan.alamat',
                'pesanan.customer',
                'pesanan.detail',
                'status',
            ])
            ->where('status_id', $statusMenunggu->id)
            ->whereNull('courier_id')
            ->latest()
            ->get();

        // Tugas aktif milik kurir ini (belum selesai)
        $tugasAktif = Pengiriman::with([
                'pesanan.umkm',
                'pesanan.alamat',
                'pesanan.customer',
                'pesanan.detail',
                'status',
            ])
            ->where('courier_id', $user->id)
            ->where('status_id', '!=', $statusSelesai->id)
            ->latest()
            ->get();

        // Riwayat selesai milik kurir ini
        $riwayat = Pengiriman::with(['pesanan.umkm', 'pesanan.customer', 'status'])
            ->where('courier_id', $user->id)
            ->where('status_id', $statusSelesai->id)
            ->latest()
            ->take(10)
            ->get();

        return view('courier.dashboard', compact('pool', 'tugasAktif', 'riwayat'));
    }

    public function pengirimanIndex(Request $request)
    {
        $statusMenunggu = StatusPengiriman::where('kode', 'menunggu_kurir')->firstOrFail();
        $statusSelesai  = StatusPengiriman::where('kode', 'selesai')->firstOrFail();

        $query = Pengiriman::with([
                'pesanan.umkm',
                'pesanan.customer',
                'pesanan.alamat',
                'pesanan.detail',
                'pesanan.metodePengiriman',
                'status',
                'kurir',
            ])
            ->whereHas('status'); // hanya yang sudah punya record pengiriman (sudah diapprove)

        // Filter: status pengiriman
        if ($request->filled('status')) {
            $query->whereHas('status', fn($q) => $q->where('kode', $request->status));
        }

        // Filter: hanya tersedia (belum diambil kurir)
        if ($request->boolean('tersedia')) {
            $query->where('status_id', $statusMenunggu->id)->whereNull('courier_id');
        }

        // Filter: tanggal dibuat
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        // Filter: nama pembeli atau no pesanan
        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->whereHas('pesanan', function ($q) use ($cari) {
                $q->where('no_pesanan', 'like', "%{$cari}%")
                ->orWhereHas('customer', fn($q2) => $q2->where('nama', 'like', "%{$cari}%"));
            });
        }

        $pengiriman = $query->latest()->paginate(15)->withQueryString();

        $statusList = StatusPengiriman::orderBy('urutan')->get();

        return view('courier.pengiriman.index', compact('pengiriman', 'statusList'));
    }

    // Kurir claim tugas dari pool
    public function claim(Pengiriman $pengiriman)
    {
        abort_unless(is_null($pengiriman->courier_id), 409, 'Tugas sudah diambil kurir lain.');

        $statusDijemput = StatusPengiriman::where('kode', 'dijemput')->firstOrFail();

        DB::transaction(function () use ($pengiriman, $statusDijemput) {
            $pengiriman->update([
                'courier_id' => Auth::id(),
                'status_id'  => $statusDijemput->id,
                'waktu_pickup' => now(),
            ]);

            PengirimanLog::create([
                'pengiriman_id' => $pengiriman->id,
                'status_id'     => $statusDijemput->id,
                'catatan'       => 'Kurir mengambil tugas pengiriman.',
            ]);
        });

        return back()->with('status', 'Tugas berhasil diambil. Silakan jemput paket di UMKM.');
    }

    // Update status pengiriman (dijemput → diantar → selesai)
    public function updateStatus(Request $request, Pengiriman $pengiriman): RedirectResponse
    {
        abort_unless($pengiriman->courier_id === Auth::id(), 403);

        $kode = $request->input('status_kode');
        $status = StatusPengiriman::where('kode', $kode)->firstOrFail();

        $data = ['status_id' => $status->id];

        if ($kode === 'diantar') {
            $data['waktu_pickup'] = now();
        }

        if ($kode === 'selesai') {
            $request->validate([
                'nama_penerima' => ['required', 'string', 'max:100'],
                'relasi_penerima' => ['nullable', 'string', 'max:100'],
                'foto_bukti' => ['required', 'image', 'max:5120'], // max 5MB
            ]);

            $data['waktu_selesai']  = now();
            $data['nama_penerima']  = $request->nama_penerima;
            $data['relasi_penerima'] = $request->relasi_penerima;
            $data['foto_bukti'] = $request->file('foto_bukti')
                ->store('pengiriman/bukti', 'public');

            // Update status pesanan jadi selesai
            $statusSelesai = \App\Models\StatusPesanan::where('kode', 'selesai')->firstOrFail();
            $pengiriman->pesanan->update(['status_id' => $statusSelesai->id]);
        }

        $pengiriman->update($data);

        return back()->with('status', match($kode) {
            'diantar' => 'Status diperbarui: sedang diantar.',
            'selesai' => 'Pengiriman selesai. Bukti serah terima tersimpan.',
            default   => 'Status diperbarui.',
        });
    }
}

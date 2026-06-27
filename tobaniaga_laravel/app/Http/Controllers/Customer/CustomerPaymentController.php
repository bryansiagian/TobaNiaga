<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\StatusPembayaran;
use App\Models\StatusPesanan;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerPaymentController extends Controller
{
    public function show(Pesanan $pesanan): View|RedirectResponse
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);

        $pembayaran = $pesanan->pembayaran;
        $statusKode = $pembayaran?->status?->kode;

        // Sudah lunas → tampil halaman sukses
        if ($statusKode === 'settlement') {
            return view('customer.payment.show', compact('pesanan', 'pembayaran'));
        }

        // Belum ada pembayaran sama sekali
        if (!$pembayaran) {
            abort(404);
        }

        return view('customer.payment.show', compact('pesanan', 'pembayaran'));
    }

    public function charge(Request $request, Pesanan $pesanan): JsonResponse
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);

        $validated = $request->validate([
            'metode'   => ['required', 'in:bca,bni,bri,mandiri,qris,card'],
            'card_token' => ['required_if:metode,card', 'nullable', 'string'],
        ]);

        $pembayaran = $pesanan->pembayaran;

        if (!$pembayaran) {
            return response()->json(['message' => 'Data pembayaran tidak ditemukan.'], 404);
        }

        // Jangan charge ulang kalau sudah settlement
        if ($pembayaran->status->kode === 'settlement') {
            return response()->json(['message' => 'Pesanan sudah lunas.'], 422);
        }

        $user = Auth::user();

        $transactionDetails = [
            'order_id'     => $pembayaran->midtrans_order_id,
            'gross_amount' => (int) $pesanan->total_harga,
        ];

        $customerDetails = [
            'first_name' => $user->nama,
            'email'      => $user->email,
            'phone'      => $user->no_hp ?? '',
        ];

        $itemDetails = $pesanan->detail->map(fn ($d) => [
            'id'       => (string) $d->produk_id,
            'price'    => (int) $d->harga_satuan_snapshot,
            'quantity' => $d->jumlah,
            'name'     => mb_substr($d->nama_produk_snapshot, 0, 50),
        ])->toArray();

        if ($pesanan->ongkos_kirim > 0) {
            $itemDetails[] = [
                'id'       => 'ongkos_kirim',
                'price'    => (int) $pesanan->ongkos_kirim,
                'quantity' => 1,
                'name'     => 'Ongkos Kirim',
            ];
        }

        if ($pesanan->diskon > 0) {
            $itemDetails[] = [
                'id'       => 'diskon',
                'price'    => (int) -$pesanan->diskon,
                'quantity' => 1,
                'name'     => 'Diskon Promo',
            ];
        }

        $midtrans = new MidtransService();

        try {
            $response = match ($validated['metode']) {
                'bca', 'bni', 'bri', 'mandiri' => $midtrans->chargeVA(
                    $validated['metode'],
                    $transactionDetails,
                    $customerDetails,
                    $itemDetails
                ),
                'qris' => $midtrans->chargeQris(
                    $transactionDetails,
                    $customerDetails,
                    $itemDetails
                ),
                'card' => $midtrans->chargeCard(
                    $validated['card_token'],
                    $transactionDetails,
                    $customerDetails,
                    $itemDetails,
                    route('customer.payment.show', $pesanan)
                ),
            };
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menghubungi payment gateway: ' . $e->getMessage()], 500);
        }

        // Simpan response ke raw_response
        $pembayaran->update([
            'metode'       => $validated['metode'],
            'raw_response' => json_encode($response),
        ]);

        return response()->json($response);
    }

    public function callback(Request $request): JsonResponse
    {
        $midtrans     = new MidtransService();
        $notification = $midtrans->getNotification();

        $orderId     = $notification->order_id;
        $statusCode  = $notification->status_code;
        $grossAmount = $notification->gross_amount;
        $transStatus = $notification->transaction_status;
        $paymentType = $notification->payment_type;
        $transId     = $notification->transaction_id;

        // Verifikasi signature
        $serverKey = config('midtrans.server_key');
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $notification->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pembayaran = Pembayaran::where('midtrans_order_id', $orderId)->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        DB::transaction(function () use ($pembayaran, $transStatus, $paymentType, $transId) {
            $kodeStatus = match ($transStatus) {
                'capture', 'settlement' => 'settlement',
                'expire'                => 'expire',
                'cancel', 'deny'        => 'cancel',
                default                 => null,
            };

            if (!$kodeStatus) return;

            $statusPembayaran = StatusPembayaran::where('kode', $kodeStatus)->firstOrFail();

            $pembayaran->update([
                'status_id'         => $statusPembayaran->id,
                'midtrans_trans_id' => $transId,
                'metode'            => $paymentType,
                'paid_at'           => in_array($transStatus, ['capture', 'settlement']) ? now() : null,
            ]);

            $pesanan = $pembayaran->pesanan;

            if (in_array($transStatus, ['capture', 'settlement'])) {
                $statusDiproses = StatusPesanan::where('kode', 'diproses')->firstOrFail();
                $pesanan->update(['status_id' => $statusDiproses->id]);

                foreach ($pesanan->detail as $detail) {
                    $detail->produk()->decrement('stok', $detail->jumlah);
                }
            }

            if (in_array($transStatus, ['expire', 'cancel', 'deny'])) {
                $statusBatal = StatusPesanan::where('kode', 'batal')->firstOrFail();
                $pesanan->update(['status_id' => $statusBatal->id]);
            }
        });

        return response()->json(['message' => 'OK']);
    }

    public function status(Pesanan $pesanan): \Illuminate\Http\JsonResponse
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);

        $pembayaran = $pesanan->pembayaran;

        if (!$pembayaran) {
            return response()->json(['status' => 'not_found'], 404);
        }

        return response()->json([
            'status' => $pembayaran->status->kode,
        ]);
    }
}

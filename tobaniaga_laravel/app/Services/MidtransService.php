<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    public function chargeVA(string $bank, array $transactionDetails, array $customerDetails, array $itemDetails): array
    {
        $params = [
            'payment_type'       => 'bank_transfer',
            'transaction_details' => $transactionDetails,
            'customer_details'   => $customerDetails,
            'item_details'       => $itemDetails,
            'bank_transfer'      => ['bank' => $bank],
        ];

        $response = CoreApi::charge($params);
        return (array) $response;
    }

    public function chargeQris(array $transactionDetails, array $customerDetails, array $itemDetails): array
    {
        $params = [
            'payment_type'        => 'qris',
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
            'qris'                => ['acquirer' => 'gopay'],
        ];

        $response = CoreApi::charge($params);
        return (array) $response;
    }

    public function chargeCard(string $tokenId, array $transactionDetails, array $customerDetails, array $itemDetails, string $finishRedirectUrl = ''): array
    {
        $params = [
            'payment_type'        => 'credit_card',
            'transaction_details' => $transactionDetails,
            'customer_details'    => $customerDetails,
            'item_details'        => $itemDetails,
            'credit_card'         => [
                'token_id'            => $tokenId,
                'authentication'      => true,
            ],
        ];

        $response = CoreApi::charge($params);
        return (array) $response;
    }

    public function getNotification(): Notification
    {
        return new Notification();
    }
}

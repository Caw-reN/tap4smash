<?php

namespace App\Libraries;

use App\Models\SettingModel;
use Config\Services;

/**
 * PaymentKu Library — Tap4Smash
 *
 * Wrapper untuk PaymentKu API (Versi Terbaru).
 * Mendukung pembuatan transaksi QRIS / QR Code tanpa signature.
 */
class PaymentKu
{
    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $settingModel = new SettingModel();

        // Ambil mode (sandbox / production)
        $mode = $settingModel->getValue('paymentku_mode', 'sandbox');

        // Ambil API key dari database, fallback ke env
        $this->apiKey = $settingModel->getValue('paymentku_api_key', env('paymentku.secretKey', ''));

        // Base URL resmi PaymentKu adalah https://paymenku.com/api/v1 untuk kedua mode (live maupun sandbox)
        $defaultUrl = 'https://paymenku.com/api/v1';
        $savedUrl   = $settingModel->getValue('paymentku_base_url', '');

        // Jika URL yang tersimpan kosong atau masih menggunakan URL usang (sandbox.paymenku.com / api.paymentku.com),
        // gunakan defaultUrl resmi.
        if (empty($savedUrl) || str_contains($savedUrl, 'sandbox') || str_contains($savedUrl, 'paymentku.com')) {
            $this->baseUrl = $defaultUrl;
        } else {
            $this->baseUrl = rtrim($savedUrl, '/');
        }

        log_message('debug', '[PaymentKu] mode={m} baseUrl={u}', ['m' => $mode, 'u' => $this->baseUrl]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Buat transaksi baru di PaymentKu dan dapatkan QR Code.
     *
     * @param  string $orderId     Kode booking unik (T4S-XXXXXXXX)
     * @param  float  $amount      Nominal yang harus dibayar (sudah dihitung DP/Full)
     * @param  string $customerName Nama customer
     * @param  string $customerPhone No WA
     * @param  string $callbackUrl URL webhook / return
     * @return array  ['success' => bool, 'qr_url' => string, 'qr_string' => string, 'token' => string, 'error' => string, 'pay_url' => string]
     */
    public function createQrisTransaction(
        string $orderId,
        float  $amount,
        string $customerName,
        string $customerPhone,
        string $callbackUrl
    ): array {
        // Payload baru sesuai dokumentasi
        $payload = [
            'channel_code'   => 'qris',
            'amount'         => (int) $amount,
            'reference_id'   => $orderId,
            'customer_name'  => $customerName,
            'customer_email' => 'customer@tap4smash.com', // Placeholder email krn wajib tp ga ditanya
            'customer_phone' => $customerPhone,
            'return_url'     => $callbackUrl,
        ];

        try {
            $client   = Services::curlrequest();
            $response = $client->post($this->baseUrl . '/transaction/create', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'json'    => $payload,
                'timeout' => 10,
                'http_errors' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            if (($data['status'] ?? '') === 'success') {
                $trxId = $data['data']['trx_id'] ?? $orderId;
                $qrUrl = $data['data']['payment_info']['qr_url'] ?? '';
                if (empty($qrUrl) && !empty($trxId)) {
                    $qrUrl = 'https://paymenku.com/api/qris/' . $trxId;
                }
                return [
                    'success'   => true,
                    'qr_url'    => $qrUrl,
                    'qr_string' => $data['data']['payment_info']['qr_string'] ?? '',
                    'token'     => $trxId,
                    'pay_url'   => $data['data']['pay_url'] ?? '',
                    'error'     => '',
                ];
            }

            log_message('error', '[PaymentKu::createQrisTransaction] Failed: ' . json_encode($data));

            return [
                'success' => false,
                'qr_url'  => '',
                'qr_string' => '',
                'token'   => '',
                'pay_url' => '',
                'error'   => $data['message'] ?? 'Gagal membuat transaksi PaymentKu.',
            ];

        } catch (\Exception $e) {
            log_message('error', '[PaymentKu::createQrisTransaction] {err}', ['err' => $e->getMessage()]);
            return [
                'success' => false,
                'qr_url'  => '',
                'qr_string' => '',
                'token'   => '',
                'pay_url' => '',
                'error'   => 'Tidak dapat terhubung ke PaymentKu: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status transaksi via PaymentKu API.
     *
     * @param  string $trxId (ini bisa jadi trx_id dari paymenku atau reference_id)
     * @return array  ['status' => 'pending'|'success'|'failed'|'expired', 'raw' => [...]]
     */
    public function checkStatus(string $trxId): array
    {
        try {
            $client   = Services::curlrequest();
            $response = $client->get($this->baseUrl . '/check-status/' . urlencode($trxId), [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ],
                'timeout' => 10,
                'http_errors' => false,
            ]);

            $data   = json_decode($response->getBody(), true);
            
            // Asumsi response format: {"status":"success","data":{"status":"paid"}}
            $status = strtolower($data['data']['status'] ?? $data['status'] ?? 'pending');

            // Normalkan ke status internal
            $mapped = match($status) {
                'success', 'paid', 'settlement', 'success_paid' => 'success',
                'failed', 'cancel', 'cancelled', 'deny' => 'failed',
                'expire', 'expired' => 'expired',
                default => 'pending',
            };

            return ['status' => $mapped, 'raw' => $data];

        } catch (\Exception $e) {
            log_message('error', '[PaymentKu::checkStatus] {err}', ['err' => $e->getMessage()]);
            return ['status' => 'pending', 'raw' => []];
        }
    }
}

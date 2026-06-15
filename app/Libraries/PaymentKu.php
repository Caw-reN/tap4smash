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
        
        // Ambil dari database, fallback ke env jika belum di-set
        $this->apiKey  = $settingModel->getValue('paymentku_api_key', env('paymentku.secretKey', ''));
        
        $envBaseUrl = env('paymentku.baseUrl', 'https://paymenku.com/api/v1');
        // Jika env masih menggunakan URL usang (api.paymentku.com), paksa pakai URL baru
        if (strpos($envBaseUrl, 'api.paymentku.com') !== false) {
            $envBaseUrl = 'https://paymenku.com/api/v1';
        }
        $this->baseUrl = $settingModel->getValue('paymentku_base_url', $envBaseUrl);
        if (strpos($this->baseUrl, 'api.paymentku.com') !== false) {
            $this->baseUrl = 'https://paymenku.com/api/v1';
        }
        $this->baseUrl = rtrim($this->baseUrl, '/');
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
                return [
                    'success'   => true,
                    'qr_url'    => $data['data']['payment_info']['qr_url'] ?? '',
                    'qr_string' => $data['data']['payment_info']['qr_string'] ?? '',
                    'token'     => $data['data']['trx_id'] ?? $orderId,
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

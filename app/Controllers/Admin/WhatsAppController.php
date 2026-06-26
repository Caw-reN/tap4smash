<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Config\Services;

/**
 * WhatsAppController (Admin)
 *
 * Berperan sebagai proxy antara browser admin dan wa-service (Node.js).
 * API Key tidak pernah terekspos ke browser — selalu diproses di sisi server.
 *
 * Routes:
 *   GET  /admin/whatsapp          → halaman dashboard status WA
 *   GET  /admin/whatsapp/status   → proxy JSON status dari wa-service
 *   GET  /admin/whatsapp/qr       → proxy JSON QR dari wa-service
 */
class WhatsAppController extends BaseController
{
    private string $serviceUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->serviceUrl = rtrim(env('whatsapp.serviceUrl', 'http://127.0.0.1:3001'), '/');
        $this->apiKey     = env('whatsapp.apiKey', 'tap4smash_wa_secret_2025');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Halaman utama
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Render halaman dashboard status WhatsApp.
     */
    public function index(): string
    {
        return view('admin/whatsapp_status');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Proxy API (dipanggil via fetch() dari browser admin)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Proxy GET /status dari wa-service.
     * Browser → CI4 → wa-service (API Key tersembunyi di server)
     */
    public function status(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->callService('/status');
        return $this->response->setJSON($data);
    }

    /**
     * Proxy GET /qr dari wa-service.
     * Mengembalikan QR sebagai Base64 data URL untuk ditampilkan di <img>.
     */
    public function qr(): \CodeIgniter\HTTP\ResponseInterface
    {
        $data = $this->callService('/qr');
        return $this->response->setJSON($data);
    }

    /**
     * Proxy POST /logout ke wa-service untuk menghapus sesi dan merestart bot.
     */
    public function logout()
    {
        $client = Services::curlrequest();
        try {
            $client->post($this->serviceUrl . '/logout', [
                'headers' => ['X-Api-Key' => $this->apiKey],
                'timeout' => 5,
            ]);
        } catch (\Exception $e) {
            // Abaikan jika timeout/service mati (akan dihandle wa-service jika direstart manual)
            log_message('error', '[Admin\WhatsAppController] Gagal logout: {error}', ['error' => $e->getMessage()]);
        }

        return redirect()->to(site_url('admin/whatsapp'))->with('success', 'WhatsApp berhasil di-logout dan di-reset. Silakan scan ulang.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helper
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Panggil endpoint wa-service via cURL.
     *
     * @param  string $endpoint Contoh: '/status', '/qr'
     * @return array  Parsed JSON response
     */
    private function callService(string $endpoint): array
    {
        $client = Services::curlrequest();

        try {
            $response = $client->get($this->serviceUrl . $endpoint, [
                'headers' => ['X-Api-Key' => $this->apiKey],
                'timeout' => 5,
            ]);

            return json_decode($response->getBody(), true)
                ?? ['success' => false, 'message' => 'Respons tidak valid dari WA service.'];

        } catch (\Exception $e) {
            log_message('error', '[Admin\WhatsAppController] Gagal konek ke wa-service: {error}', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success'   => false,
                'connected' => false,
                'hasQR'     => false,
                'message'   => 'WA Service tidak dapat dijangkau. Pastikan `node wa-service/index.js` sudah berjalan.',
            ];
        }
    }
}

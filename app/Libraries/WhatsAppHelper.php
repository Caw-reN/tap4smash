<?php

namespace App\Libraries;

/**
 * WhatsAppHelper
 *
 * Library untuk mengirim pesan WhatsApp via Baileys microservice
 * yang berjalan di wa-service/index.js (Node.js).
 *
 * Cara pakai di Controller:
 *   $wa = new \App\Libraries\WhatsAppHelper();
 *   $wa->sendTicket($bookingData);
 */
class WhatsAppHelper
{
    private string $serviceUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->serviceUrl = rtrim(env('whatsapp.serviceUrl', 'http://127.0.0.1:3001'), '/');
        $this->apiKey     = env('whatsapp.apiKey', 'tap4smash_wa_secret_2025');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Kirim e-tiket booking sukses ke nomor WA pemesan.
     *
     * @param array $bookingData Harus berisi key:
     *   booking_code, nama_pemesan, nama_lapangan,
     *   tanggal_main, jam_mulai, jam_selesai,
     *   total_harga, skema_pembayaran, jumlah_dibayar, sisa_tagihan, nomor_wa
     */
    public function sendTicket(array $bookingData): bool
    {
        $message = $this->buildTicketMessage($bookingData);
        return $this->send($bookingData['nomor_wa'], $message);
    }

    /**
     * Kirim pesan teks bebas ke nomor WA tertentu.
     *
     * @param string $phone   Nomor WA format internasional: 628xxxxxxxxxx
     * @param string $message Teks yang akan dikirim
     */
    public function send(string $phone, string $message): bool
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->post($this->serviceUrl . '/send-message', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Api-Key'    => $this->apiKey,
                ],
                'body'    => json_encode([
                    'phone'   => $phone,
                    'message' => $message,
                ]),
                'timeout' => 10,
            ]);

            $body = json_decode($response->getBody(), true);

            if (! ($body['success'] ?? false)) {
                log_message('warning', '[WhatsAppHelper] WA service menolak request ke {phone}: {msg}', [
                    'phone' => $phone,
                    'msg'   => $body['message'] ?? 'unknown',
                ]);
                return false;
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', '[WhatsAppHelper] Gagal konek ke WA service untuk {phone}: {error}', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Cek apakah WA service sedang terhubung ke WhatsApp.
     */
    public function isServiceConnected(): bool
    {
        $client = \Config\Services::curlrequest();

        try {
            $response = $client->get($this->serviceUrl . '/status', [
                'headers' => ['X-Api-Key' => $this->apiKey],
                'timeout' => 3,
            ]);

            $body = json_decode($response->getBody(), true);
            return $body['connected'] ?? false;

        } catch (\Exception $e) {
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private Helpers
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Buat teks e-tiket booking yang akan dikirim via WA.
     */
    private function buildTicketMessage(array $d): string
    {
        $skemaLabel = $d['skema_pembayaran'] === 'dp'
            ? 'Uang Muka (DP 50%)'
            : 'Lunas (100%)';

        $sisaInfo = $d['skema_pembayaran'] === 'dp'
            ? "\n⚠️ *Sisa Tagihan:* Rp " . number_format($d['sisa_tagihan'], 0, ',', '.') .
              "\n_(Lunasi di kasir GOR sebelum bermain)_"
            : '';

        $tanggal = date('d F Y', strtotime($d['tanggal_main']));

        return
            "🏸 *TAP4SMASH — E-TIKET BOOKING*\n" .
            "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
            "📋 *Kode Booking:* `{$d['booking_code']}`\n" .
            "👤 *Pemesan:* {$d['nama_pemesan']}\n" .
            "🏟️ *Lapangan:* {$d['nama_lapangan']}\n" .
            "📅 *Tanggal:* {$tanggal}\n" .
            "⏰ *Jam Main:* {$d['jam_mulai']} – {$d['jam_selesai']} WIB\n" .
            "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
            "💰 *Total Harga:* Rp " . number_format($d['total_harga'], 0, ',', '.') . "\n" .
            "✅ *Skema Bayar:* {$skemaLabel}\n" .
            "💳 *Sudah Dibayar:* Rp " . number_format($d['jumlah_dibayar'], 0, ',', '.') .
            $sisaInfo . "\n" .
            "━━━━━━━━━━━━━━━━━━━━━━━━━\n" .
            "Tunjukkan pesan ini atau kode booking kepada petugas GOR saat tiba.\n\n" .
            "_Terima kasih telah booking di Tap4Smash! 🏸_";
    }
}

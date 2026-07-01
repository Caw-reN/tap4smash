<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LapanganModel;

/**
 * PaymentController
 *
 * Menangani callback / webhook dari PaymentKu setelah user menyelesaikan
 * pembayaran. Sesuai PRD S-01: validasi HMAC-SHA256 signature wajib.
 *
 * Flow:
 *   PaymentKu → POST /payment/callback → validasi signature → update status → kirim WA
 */
class PaymentController extends BaseController
{
    private BookingModel $bookingModel;
    private LapanganModel $lapanganModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->lapanganModel = new LapanganModel();
        helper('whatsapp');
    }

    /**
     * Endpoint: POST /payment/callback
     *
     * Menerima webhook dari PaymentKu, memvalidasi signature,
     * lalu mengubah status booking menjadi success.
     *
     * PRD S-01: HMAC-SHA256(merchant_id + order_id + amount + secret_key)
     */
    public function callback()
    {
        // Ambil raw body (JSON) dari Paymenku
        $rawBody = $this->request->getBody();
        if (empty($rawBody)) {
            log_message('warning', '[PaymentController] Payload kosong');
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid payload']);
        }

        // ── Validasi Signature (Sesuai Docs) ─────────────────────────────────
        $timestamp = $this->request->getHeaderLine('X-Paymenku-Timestamp');
        $signature = $this->request->getHeaderLine('X-Paymenku-Signature');
        
        $settingModel  = new \App\Models\SettingModel();
        $webhookSecret = $settingModel->getValue('paymentku_webhook_secret', env('paymentku.webhookSecret', ''));

        // Formula: signature = HMAC-SHA256(timestamp + "." + raw_body, webhook_secret)
        $computedSignature = hash_hmac('sha256', $timestamp . '.' . $rawBody, $webhookSecret);

        if (! hash_equals($computedSignature, $signature)) {
            log_message('warning', '[PaymentController] Signature tidak valid');
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Invalid signature']);
        }

        $payload = json_decode($rawBody, true);

        // ── Proses Booking ────────────────────────────────────────────────────
        $bookingCode = $payload['reference_id'] ?? '';
        $status      = strtolower($payload['status'] ?? '');

        $booking = $this->bookingModel
            ->where('booking_code', strtoupper(trim($bookingCode)))
            ->first();

        if (! $booking) {
            log_message('warning', '[PaymentController] Booking tidak ditemukan: {code}', [
                'code' => $bookingCode,
            ]);
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Booking not found']);
        }

        // Jika sudah success, abaikan (idempotent)
        if ($booking['status'] === 'success') {
            return $this->response->setJSON(['received' => true]);
        }

        // ── Update status berdasarkan callback PaymentKu ──────────────────────
        if ($status === 'success' || $status === 'paid') {

            // F-04: Kalkulasi DP/Full — terpusat di BookingModel::calculatePayment()
            $payment = $this->bookingModel->calculatePayment($booking);

            $this->bookingModel->update($booking['id'], [
                'status'           => 'success',
                'jumlah_dibayar'   => $payment['jumlah_dibayar'],
                'sisa_tagihan'     => $payment['sisa_tagihan'],
                'status_pelunasan' => $payment['status_pelunasan'],
                'payment_token'    => $payload['payment_token'] ?? $payload['transaction_id'] ?? null,
            ]);

            // ── F-06: Notifikasi WA ke pemesan ───────────────────────────────
            $updatedBooking            = $this->bookingModel->find($booking['id']);
            $updatedBooking['jumlah_dibayar'] = $jumlahDibayar;
            $updatedBooking['sisa_tagihan']   = $sisaTagihan;

            $lapangan = $this->lapanganModel->find($booking['lapangan_id']);

            if ($lapangan) {
                whatsapp_send_ticket($updatedBooking, $lapangan);
            }

            log_message('info', '[PaymentController] Booking {code} dikonfirmasi via Paymenku.', [
                'code' => $bookingCode,
            ]);

            return $this->response->setJSON(['received' => true]);

        } elseif (in_array($status, ['failed', 'cancelled', 'expired'])) {

            $this->bookingModel->update($booking['id'], [
                'status' => 'failed',
            ]);

            log_message('info', '[PaymentController] Booking {code} gagal: {status}', [
                'code'   => $bookingCode,
                'status' => $status,
            ]);

            return $this->response->setJSON(['received' => true]);
        }

        return $this->response->setJSON(['received' => true, 'status' => 'ignored']);
    }
}

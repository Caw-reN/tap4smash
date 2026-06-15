<?php

/**
 * WhatsApp Helper — Tap4Smash
 *
 * Mengirim notifikasi WhatsApp ke user via wa-service (Baileys/Node.js).
 * Dipanggil setelah booking dikonfirmasi (status → success).
 *
 * Fungsi yang tersedia:
 *   whatsapp_normalize_number(string $nomor): string
 *   whatsapp_send_ticket(array $booking, array $lapangan): bool
 *   whatsapp_send_message(string $to, string $message): bool
 */

// ────────────────────────────────────────────────────────────────────────────
// Helper: Normalisasi nomor WA ke format 628xxxxxxxxxx (PRD S-03)
// ────────────────────────────────────────────────────────────────────────────

if (! function_exists('whatsapp_normalize_number')) {
    /**
     * Normalisasi nomor telepon ke format internasional 628xxxxxxxxxx.
     *
     * Contoh:
     *   "08123456789"   → "628123456789"
     *   "+628123456789" → "628123456789"
     *   "628123456789"  → "628123456789"
     */
    function whatsapp_normalize_number(string $nomor): string
    {
        // Bersihkan semua karakter non-digit kecuali leading +
        $nomor = preg_replace('/[^0-9+]/', '', trim($nomor));

        if (str_starts_with($nomor, '+62')) {
            return '62' . substr($nomor, 3);
        }

        if (str_starts_with($nomor, '62')) {
            return $nomor;
        }

        if (str_starts_with($nomor, '0')) {
            return '62' . substr($nomor, 1);
        }

        // Jika sudah tanpa prefix, anggap nomor Indonesia
        return '62' . $nomor;
    }
}

// ────────────────────────────────────────────────────────────────────────────
// Helper: Kirim pesan raw ke WA service
// ────────────────────────────────────────────────────────────────────────────

if (! function_exists('whatsapp_send_message')) {
    /**
     * Kirim pesan teks ke nomor WA via wa-service (Node.js / Baileys).
     *
     * @param  string $to      Nomor tujuan (akan dinormalisasi otomatis)
     * @param  string $message Isi pesan
     * @return bool            true jika terkirim, false jika gagal
     */
    function whatsapp_send_message(string $to, string $message): bool
    {
        $serviceUrl = rtrim(env('whatsapp.serviceUrl', 'http://127.0.0.1:3001'), '/');
        $apiKey     = env('whatsapp.apiKey', 'tap4smash_wa_secret_2025');
        $to         = whatsapp_normalize_number($to);

        try {
            $client   = \Config\Services::curlrequest();
            $response = $client->post($serviceUrl . '/send', [
                'headers' => [
                    'X-Api-Key'    => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json'    => [
                    'to'      => $to,
                    'message' => $message,
                ],
                'timeout' => 8,
            ]);

            $data = json_decode($response->getBody(), true);
            return ($data['success'] ?? false) === true;

        } catch (\Exception $e) {
            log_message('error', '[WhatsAppHelper] Gagal kirim WA ke {to}: {error}', [
                'to'    => $to,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

// ────────────────────────────────────────────────────────────────────────────
// Helper: Kirim e-tiket booking ke user (PRD F-06)
// ────────────────────────────────────────────────────────────────────────────

if (! function_exists('whatsapp_send_ticket')) {
    /**
     * Kirim notifikasi e-tiket ke pemesan setelah booking dikonfirmasi.
     *
     * @param  array $booking  Row dari tabel bookings (joined)
     * @param  array $lapangan Row dari tabel lapangans
     * @return bool
     */
    function whatsapp_send_ticket(array $booking, array $lapangan): bool
    {
        $code        = $booking['booking_code'];
        $nama        = $booking['nama_pemesan'];
        $lapanganNm  = $lapangan['nama_lapangan'];
        $tanggal     = date('l, d F Y', strtotime($booking['tanggal_main']));
        $jams        = explode(',', $booking['jam_main']);
        $jamString   = implode(', ', array_map(fn($j) => sprintf('%02d:00', trim($j)), $jams));
        $skema       = $booking['skema_pembayaran'] === 'dp' ? 'DP 50%' : 'Full Payment';
        $totalHarga  = 'Rp ' . number_format($booking['total_harga'], 0, ',', '.');
        $dibayar     = 'Rp ' . number_format($booking['jumlah_dibayar'], 0, ',', '.');
        $sisaTagihan = 'Rp ' . number_format($booking['sisa_tagihan'], 0, ',', '.');

        $message  = "✅ *BOOKING DIKONFIRMASI — Tap4Smash GOR*\n";
        $message .= str_repeat('─', 30) . "\n";
        $message .= "🎫 Kode Booking: *{$code}*\n\n";
        $message .= "👤 Nama   : {$nama}\n";
        $message .= "🏟️ Lapangan: {$lapanganNm}\n";
        $message .= "📅 Tanggal : {$tanggal}\n";
        $message .= "🕐 Waktu   : {$jamString} WIB\n";
        $message .= str_repeat('─', 30) . "\n";
        $message .= "💰 Total Harga  : {$totalHarga}\n";
        $message .= "💳 Skema Bayar  : {$skema}\n";
        $message .= "✅ Sudah Dibayar: {$dibayar}\n";

        if ($booking['skema_pembayaran'] === 'dp') {
            $message .= "⚠️ Sisa Tagihan : *{$sisaTagihan}* (bayar di kasir GOR)\n";
        }

        $message .= str_repeat('─', 30) . "\n";
        $message .= "📌 Simpan kode ini untuk cek status:\n";
        $message .= site_url('cek-status?kode=' . $code) . "\n\n";
        $message .= "_Terima kasih sudah booking di Tap4Smash!_ 🏸";

        return whatsapp_send_message($booking['nomor_wa'], $message);
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LapanganModel;
use App\Libraries\PaymentKu;

class Home extends BaseController
{
    private BookingModel $bookingModel;
    private LapanganModel $lapanganModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->lapanganModel = new LapanganModel();
        helper('whatsapp');
    }

    /** Landing Page (F-01) */
    public function index(): string
    {
        $lapangans = $this->lapanganModel->getActive();

        return view('user/home', [
            'lapangans' => $lapangans,
        ]);
    }

    /** Halaman Form Booking (F-02 + F-03) */
    public function booking(): string
    {
        // F-02: cleanup slot expired setiap kali halaman kalender diakses
        $this->bookingModel->cleanupExpiredSlots();
        $lapangans = $this->lapanganModel->getActive();

        return view('user/booking', [
            'lapangans'   => $lapangans,
            'lapangan_id' => $this->request->getGet('lapangan_id'),
        ]);
    }

    /**
     * API: ambil slot terpakai untuk picker JS (F-02)
     * GET /api/slots?lapangan_id=X&tanggal=Y-M-D
     */
    public function getSlots()
    {
        $lapanganId = (int) $this->request->getGet('lapangan_id');
        $tanggal    = $this->request->getGet('tanggal');

        if (! $lapanganId || ! $tanggal) {
            return $this->response->setJSON(['slots' => []]);
        }

        $rows = $this->bookingModel
            ->where('lapangan_id', $lapanganId)
            ->where('tanggal_main', $tanggal)
            ->whereIn('status', ['pending', 'success'])
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->findAll();

        $usedSlots = [];
        foreach ($rows as $r) {
            $slots = explode(',', $r['jam_main']);
            foreach ($slots as $s) {
                if (is_numeric(trim($s))) {
                    $usedSlots[] = (int) trim($s);
                }
            }
        }

        return $this->response->setJSON(['slots' => array_unique($usedSlots)]);
    }

    /**
     * API: ambil harga lapangan
     * GET /api/lapangan-price?id=X
     */
    public function getLapanganPrice()
    {
        $id       = (int) $this->request->getGet('id');
        $lapangan = $this->lapanganModel->find($id);

        if (! $lapangan) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Not found']);
        }

        return $this->response->setJSON([
            'harga_per_jam'  => (float) $lapangan['harga_per_jam'],
            'nama_lapangan'  => $lapangan['nama_lapangan'],
            'jenis_lapangan' => $lapangan['jenis_lapangan'] ?? '',
        ]);
    }

    /**
     * Proses submit form booking (F-03, F-04, F-05)
     *
     * Sesuai PRD:
     * - Slot dikunci 15 menit (bukan 30)
     * - booking_code format T4S-XXXXXXXX
     * - Nomor WA dinormalisasi ke 628xxx (S-03)
     * - Insert dibungkus DB Transaction (S-04)
     */
    public function processBooking()
    {
        helper(['text', 'whatsapp']);

        $rules = [
            'lapangan_id'      => 'required|is_natural_no_zero',
            'tanggal_main'     => 'required|valid_date[Y-m-d]',
            'jam_main'         => 'required',
            'nama_pemesan'     => 'required|min_length[3]|max_length[100]',
            'nomor_wa'         => 'required|min_length[8]|max_length[20]',
            'skema_pembayaran' => 'required|in_list[dp,full]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $lapanganId  = (int) $this->request->getPost('lapangan_id');
        $tanggalMain = $this->request->getPost('tanggal_main');
        $jamMain     = $this->request->getPost('jam_main');
        $namaPemesan = $this->request->getPost('nama_pemesan');
        $skema       = $this->request->getPost('skema_pembayaran');

        // S-03: Normalisasi nomor WA
        $nomorWa = whatsapp_normalize_number(
            $this->request->getPost('nomor_wa')
        );

        $lapangan = $this->lapanganModel->find($lapanganId);
        if (! $lapangan || ! $lapangan['is_active']) {
            return redirect()->back()->withInput()
                ->with('errors', ['lapangan_id' => 'Lapangan tidak tersedia.']);
        }

        // Hitung jam & total harga
        $slotsRaw = explode(',', $jamMain);
        $slots = [];
        foreach($slotsRaw as $s) {
            $s = trim($s);
            if(is_numeric($s)) {
                $slots[] = (int)$s;
            }
        }
        
        $durasiJam = count($slots);

        if ($durasiJam < 1) {
            return redirect()->back()->withInput()
                ->with('errors', ['jam_main' => 'Pilih minimal 1 slot jam.']);
        }

        // F-04: Hitung skema pembayaran
        $totalHarga    = $lapangan['harga_per_jam'] * $durasiJam;
        $jumlahDibayar = ($skema === 'dp') ? $totalHarga * 0.5 : $totalHarga;
        $sisaTagihan   = $totalHarga - $jumlahDibayar;
        $statusPelunasan = ($skema === 'full') ? 'lunas' : 'belum_lunas';

        // Cleanup expired sebelum cek konflik
        $this->bookingModel->cleanupExpiredSlots();

        // S-04: Bungkus dalam DB Transaction untuk cegah race condition
        $db = db_connect();
        $db->transStart();

        try {
            // Ambil semua slot yg sudah dibooking hari itu
            $existingRows = $db->table('bookings')
                ->select('jam_main')
                ->where('lapangan_id', $lapanganId)
                ->where('tanggal_main', $tanggalMain)
                ->whereIn('status', ['pending', 'success'])
                ->where('expires_at >', date('Y-m-d H:i:s'))
                ->get()->getResultArray();
                
            $bookedSlots = [];
            foreach($existingRows as $row) {
                $rowSlots = explode(',', $row['jam_main']);
                foreach($rowSlots as $rs) {
                    $bookedSlots[] = (int)trim($rs);
                }
            }
            
            $intersect = array_intersect($slots, $bookedSlots);

            if (!empty($intersect)) {
                $db->transRollback();
                return redirect()->back()->withInput()
                    ->with('errors', ['jam_main' => 'Beberapa slot waktu sudah dipesan. Pilih jam lain.']);
            }

            // F-05: booking_code format T4S-XXXXXXXX, slot lock 15 menit
            $bookingCode = 'T4S-' . strtoupper(random_string('alnum', 8));
            $expiresAt   = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            sort($slots);
            $jamMainSorted = implode(',', $slots);

            $this->bookingModel->insert([
                'booking_code'     => $bookingCode,
                'lapangan_id'      => $lapanganId,
                'nama_pemesan'     => $namaPemesan,
                'nomor_wa'         => $nomorWa,
                'tanggal_main'     => $tanggalMain,
                'jam_main'         => $jamMainSorted,
                'total_harga'      => $totalHarga,
                'skema_pembayaran' => $skema,
                'jumlah_dibayar'   => 0,      // diupdate setelah payment callback
                'sisa_tagihan'     => $totalHarga,
                'status'           => 'pending',
                'status_pelunasan' => 'belum_lunas',
                'expires_at'       => $expiresAt,
            ]);

            $db->transComplete();

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[Home::processBooking] Transaction error: {e}', ['e' => $e->getMessage()]);
            return redirect()->back()->withInput()
                ->with('errors', ['general' => 'Terjadi kesalahan. Silakan coba lagi.']);
        }

        if (! $db->transStatus()) {
            return redirect()->back()->withInput()
                ->with('errors', ['general' => 'Gagal menyimpan booking. Silakan coba lagi.']);
        }

        // ── Panggil PaymentKu QRIS API ──────────────────────────────────────
        $paymentKu   = new PaymentKu();
        $callbackUrl = site_url('payment/callback');
        $description = "Booking Lapangan {$lapangan['nama_lapangan']} — {$tanggalMain} (Jam: {$jamMainSorted})";

        $qrisResult = $paymentKu->createQrisTransaction(
            $bookingCode,
            $jumlahDibayar,
            $namaPemesan,
            $nomorWa,
            $callbackUrl
        );

        if ($qrisResult['success']) {
            // Simpan token PaymentKu ke database
            $this->bookingModel->update(
                $this->bookingModel->where('booking_code', $bookingCode)->first()['id'],
                ['payment_token' => $qrisResult['token']]
            );
        }

        // Redirect ke halaman pembayaran QR (bahkan jika QRIS gagal, tampilkan fallback)
        return redirect()->to(site_url('booking/bayar/' . $bookingCode));
    }

    /**
     * Halaman pembayaran QR (redirect setelah form booking berhasil)
     */
    public function payment(string $bookingCode): string
    {
        $booking = $this->bookingModel
            ->where('booking_code', strtoupper(trim($bookingCode)))
            ->first();

        if (! $booking) {
            return redirect()->to(site_url('/'))->with('error', 'Kode booking tidak ditemukan.');
        }

        // Jika sudah success, langsung ke halaman konfirmasi
        if ($booking['status'] === 'success') {
            return redirect()->to(site_url('booking/konfirmasi/' . $bookingCode));
        }

        // Jika expired/failed, balik ke halaman booking
        if (in_array($booking['status'], ['expired', 'failed'])) {
            return redirect()->to(site_url('booking'))
                ->with('errors', ['jam_mulai' => 'Waktu pembayaran habis. Silakan booking ulang.']);
        }

        $lapangan  = $this->lapanganModel->find($booking['lapangan_id']);

        // Hitung nominal yang harus dibayar sekarang
        $jumlahDibayar = ($booking['skema_pembayaran'] === 'dp')
            ? $booking['total_harga'] * 0.5
            : $booking['total_harga'];

        // Coba ambil QR baru dari PaymentKu jika belum ada token
        $qrUrl    = '';
        $qrString = '';
        $payUrl   = '';
        $paymentKu = new PaymentKu();

        if (! empty($booking['payment_token'])) {
            // Token ada — QR sudah dibuat sebelumnya, cek ulang status
            $statusResult = $paymentKu->checkStatus($bookingCode);
            if ($statusResult['status'] === 'success') {
                return redirect()->to(site_url('booking/konfirmasi/' . $bookingCode));
            }
            // Gunakan token yang ada. Ambil pay_url dari checkStatus
            $raw = $statusResult['raw']['data'] ?? [];
            $payUrl = $raw['pay_url'] ?? '';
        } else {
            // Buat transaksi baru
            $callbackUrl = site_url('payment/callback');
            $result = $paymentKu->createQrisTransaction(
                $bookingCode, 
                $jumlahDibayar, 
                $booking['nama_pemesan'], 
                $booking['nomor_wa'], 
                $callbackUrl
            );
            if ($result['success']) {
                $qrUrl    = $result['qr_url'];
                $qrString = $result['qr_string'];
                $payUrl   = $result['pay_url'] ?? '';
                $this->bookingModel->update($booking['id'], [
                    'payment_token' => $result['token'],
                ]);
            } else {
                $errorMessage = 'Gagal memproses pembayaran: ' . ($result['error'] ?? 'Unknown Error');
            }
        }

        helper('format');
        $jamMainFormatted = format_jam_main($booking['jam_main']);

        return view('user/payment', [
            'booking'       => $booking,
            'lapangan'      => $lapangan,
            'jumlah_bayar'  => $jumlahDibayar,
            'qr_url'        => $qrUrl,
            'qr_string'     => $qrString,
            'pay_url'       => $payUrl,
            'jam_main_fmt'  => $jamMainFormatted,
            'error_message' => $errorMessage ?? null,
        ]);
    }

    /**
     * API Polling: cek status pembayaran via PaymentKu
     * GET /api/payment-status?kode=T4S-XXXXXXXX
     */
    public function checkPaymentStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $kode    = strtoupper(trim($this->request->getGet('kode') ?? ''));
        $booking = $this->bookingModel->where('booking_code', $kode)->first();

        if (! $booking) {
            return $this->response->setStatusCode(404)->setJSON(['status' => 'not_found']);
        }

        // Jika sudah success di DB, langsung return
        if ($booking['status'] === 'success') {
            return $this->response->setJSON(['status' => 'success']);
        }

        // Cek ke PaymentKu API
        $paymentKu = new PaymentKu();
        $result    = $paymentKu->checkStatus($kode);

        // Jika PaymentKu konfirmasi success, update DB & kirim WA
        if ($result['status'] === 'success' && $booking['status'] !== 'success') {
            helper('whatsapp');
            $skema         = $booking['skema_pembayaran'];
            $totalHarga    = (float) $booking['total_harga'];
            $jumlahDibayar = ($skema === 'dp') ? $totalHarga * 0.5 : $totalHarga;
            $sisaTagihan   = $totalHarga - $jumlahDibayar;

            $this->bookingModel->update($booking['id'], [
                'status'           => 'success',
                'jumlah_dibayar'   => $jumlahDibayar,
                'sisa_tagihan'     => $sisaTagihan,
                'status_pelunasan' => ($skema === 'full') ? 'lunas' : 'belum_lunas',
            ]);

            $updatedBooking = $this->bookingModel->find($booking['id']);
            $lapangan       = $this->lapanganModel->find($booking['lapangan_id']);
            if ($lapangan) {
                whatsapp_send_ticket($updatedBooking, $lapangan);
            }
        }

        return $this->response->setJSON(['status' => $result['status']]);
    }

    /** Halaman konfirmasi pembayaran (F-10) */
    public function konfirmasi(string $bookingCode): string
    {
        $booking = $this->bookingModel
            ->where('booking_code', strtoupper(trim($bookingCode)))
            ->first();

        if (! $booking) {
            return redirect()->to(site_url('/'))->with('error', 'Kode booking tidak ditemukan.');
        }

        $lapangan = $this->lapanganModel->find($booking['lapangan_id']);
        helper('format');

        return view('user/konfirmasi', [
            'booking'  => $booking,
            'lapangan' => $lapangan,
            'jam_main_fmt' => format_jam_main($booking['jam_main']),
        ]);
    }

    /** Cek status booking (F-11) */
    public function cekStatus(): string
    {
        $kode    = $this->request->getGet('kode');
        $booking  = null;
        $lapangan = null;

        if ($kode) {
            $booking = $this->bookingModel
                ->where('booking_code', strtoupper(trim($kode)))
                ->first();
            if ($booking) {
                $lapangan = $this->lapanganModel->find($booking['lapangan_id']);
            }
        }

        return view('user/cek_status', [
            'kode'     => $kode,
            'booking'  => $booking,
            'lapangan' => $lapangan,
        ]);
    }
}

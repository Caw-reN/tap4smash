<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LapanganModel;
use App\Libraries\PaymentKu;

class BookingController extends BaseController
{
    private BookingModel  $bookingModel;
    private LapanganModel $lapanganModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->lapanganModel = new LapanganModel();
    }

    /** Daftar semua booking dengan filter */
    public function index(): string
    {
        $filters = [
            'tanggal'          => $this->request->getGet('tanggal'),
            'lapangan_id'      => $this->request->getGet('lapangan_id'),
            'status'           => $this->request->getGet('status'),
            'status_pelunasan' => $this->request->getGet('status_pelunasan'),
        ];

        $perPage = 10;
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset  = ($page - 1) * $perPage;
        $total   = $this->bookingModel->countWithFilters($filters);
        $totalPages = (int) ceil($total / $perPage);

        return view('admin/bookings/index', [
            'page_title'  => 'Manajemen Booking',
            'bookings'    => $this->bookingModel->getAllWithFilters($filters, $perPage, $offset),
            'lapangans'   => $this->lapanganModel->findAll(),
            'filters'     => $filters,
            'total'       => $total,
            'perPage'     => $perPage,
            'currentPage' => $page,
            'totalPages'  => $totalPages,
        ]);
    }

    public function create(): string
    {
        return view('admin/bookings/create', [
            'page_title' => 'Tambah Booking Baru',
            'lapangans'  => $this->lapanganModel->getActive(),
        ]);
    }

    public function store(): \CodeIgniter\HTTP\ResponseInterface
    {
        helper(['text', 'whatsapp']);

        $rules = [
            'lapangan_id'  => 'required|is_natural_no_zero',
            'tanggal_main' => 'required|valid_date[Y-m-d]',
            'jam_main'     => 'required',
            'nama_pemesan' => 'required',
            'nomor_wa'     => 'required',
            'pembayaran'   => 'required|in_list[belum_bayar,dp,lunas]',
            'metode'       => 'permit_empty|in_list[cash,qris]',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Input tidak valid. Periksa kembali form.',
                'errors'  => $this->validator->getErrors()
            ]);
        }

        $lapanganId  = (int) $this->request->getPost('lapangan_id');
        $tanggalMain = $this->request->getPost('tanggal_main');
        $jamMain     = $this->request->getPost('jam_main');
        if (!is_array($jamMain)) $jamMain = [$jamMain];
        $namaPemesan = $this->request->getPost('nama_pemesan');
        $nomorWa     = whatsapp_normalize_number($this->request->getPost('nomor_wa'));
        $pembayaran  = $this->request->getPost('pembayaran');
        $metode      = $this->request->getPost('metode') ?? 'cash';

        $lapangan = $this->lapanganModel->find($lapanganId);
        if (! $lapangan) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Lapangan tidak ditemukan.']);
        }

        if (count($jamMain) < 1) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Pilih minimal 1 slot jam.']);
        }

        $durasiJam = count($jamMain);
        $totalHarga = $lapangan['harga_per_jam'] * $durasiJam;

        $db = db_connect();
        $db->transStart();

        try {
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
            
            $intersect = array_intersect($jamMain, $bookedSlots);
            if (!empty($intersect)) {
                $db->transRollback();
                return $this->response->setStatusCode(422)->setJSON(['success' => false, 'message' => 'Slot waktu sudah dipesan orang lain.']);
            }

            $skema = ($pembayaran === 'dp') ? 'dp' : 'full';
            $dibayarSaatIni = 0;
            if ($pembayaran === 'dp') $dibayarSaatIni = $totalHarga / 2;
            if ($pembayaran === 'lunas') $dibayarSaatIni = $totalHarga;

            $bookingCode = 'T4S-' . strtoupper(random_string('alnum', 8));
            sort($jamMain);
            $jamMainSorted = implode(',', $jamMain);

            if ($metode === 'qris' && $pembayaran !== 'belum_bayar') {
                $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                
                $paymentKu = new PaymentKu();
                $result = $paymentKu->createQrisTransaction(
                    $bookingCode,
                    $dibayarSaatIni,
                    $namaPemesan,
                    $nomorWa,
                    site_url('payment/callback')
                );

                if (! $result['success']) {
                    $db->transRollback();
                    return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Gagal membuat QRIS.']);
                }

                $bookingId = $this->bookingModel->insert([
                    'booking_code'     => $bookingCode,
                    'lapangan_id'      => $lapanganId,
                    'nama_pemesan'     => $namaPemesan,
                    'nomor_wa'         => $nomorWa,
                    'tanggal_main'     => $tanggalMain,
                    'jam_main'         => $jamMainSorted,
                    'total_harga'      => $totalHarga,
                    'skema_pembayaran' => $skema,
                    'jumlah_dibayar'   => 0,
                    'sisa_tagihan'     => $totalHarga,
                    'status'           => 'pending',
                    'status_pelunasan' => 'belum_lunas',
                    'payment_token'    => $result['token'],
                    'expires_at'       => $expiresAt,
                ]);
                $db->transComplete();

                return $this->response->setJSON([
                    'success'    => true,
                    'is_qris'    => true,
                    'qr_url'     => $result['qr_url'],
                    'qr_string'  => $result['qr_string'],
                    'booking_id' => $bookingId,
                    'amount'     => $dibayarSaatIni,
                    'message'    => 'Silakan scan QR untuk menyelesaikan pembayaran.',
                ]);
            } else {
                $sisaTagihan = $totalHarga - $dibayarSaatIni;
                $statusPelunasan = ($sisaTagihan <= 0) ? 'lunas' : 'belum_lunas';
                $expiresAt = date('Y-m-d H:i:s', strtotime('+1 years'));

                $this->bookingModel->insert([
                    'booking_code'     => $bookingCode,
                    'lapangan_id'      => $lapanganId,
                    'nama_pemesan'     => $namaPemesan,
                    'nomor_wa'         => $nomorWa,
                    'tanggal_main'     => $tanggalMain,
                    'jam_main'         => $jamMainSorted,
                    'total_harga'      => $totalHarga,
                    'skema_pembayaran' => $skema,
                    'jumlah_dibayar'   => $dibayarSaatIni,
                    'sisa_tagihan'     => $sisaTagihan,
                    'status'           => 'success',
                    'status_pelunasan' => $statusPelunasan,
                    'expires_at'       => $expiresAt,
                ]);
                $db->transComplete();

                session()->setFlashdata('success', 'Booking berhasil ditambahkan.');
                return $this->response->setJSON([
                    'success' => true,
                    'is_qris' => false,
                    'message' => 'Booking berhasil disimpan.'
                ]);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan sistem.']);
        }
    }

    public function qrisNewBookingStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json      = $this->request->getJSON(true);
        $bookingId = (int) ($json['booking_id'] ?? 0);
        $booking   = $this->bookingModel->find($bookingId);

        if (! $booking) return $this->response->setJSON(['success' => false]);
        if ($booking['status'] === 'success') {
            session()->setFlashdata('success', 'Pembayaran QRIS berhasil dikonfirmasi. Booking ditambahkan.');
            return $this->response->setJSON(['success' => true, 'paid' => true]);
        }

        $paymentKu = new \App\Libraries\PaymentKu();
        $checkId = !empty($booking['payment_token']) ? $booking['payment_token'] : $booking['booking_code'];
        $result = $paymentKu->checkStatus($checkId);

        if ($result['status'] === 'success') {
            if ($booking['status'] !== 'success') {
                $skema = $booking['skema_pembayaran'];
                $dibayar = ($skema === 'dp') ? ($booking['total_harga'] / 2) : $booking['total_harga'];
                $sisa = $booking['total_harga'] - $dibayar;
                $pelunasan = ($sisa <= 0) ? 'lunas' : 'belum_lunas';
                
                $this->bookingModel->update($bookingId, [
                    'status' => 'success',
                    'jumlah_dibayar' => $dibayar,
                    'sisa_tagihan' => $sisa,
                    'status_pelunasan' => $pelunasan,
                ]);
            }
            session()->setFlashdata('success', 'Pembayaran QRIS berhasil dikonfirmasi. Booking ditambahkan.');
            return $this->response->setJSON(['success' => true, 'paid' => true, 'message' => 'Pembayaran QRIS berhasil!']);
        }

        return $this->response->setJSON(['success' => true, 'paid' => false]);
    }

    /** Daftar booking yang perlu dilunasi (DP belum lunas) */
    public function pelunasan(): string
    {
        return view('admin/bookings/pelunasan', [
            'page_title' => 'Pelunasan DP',
            'bookings'   => $this->bookingModel->getPendingPelunasan(),
        ]);
    }

    /** Aksi: tandai booking sebagai lunas di kasir */
    public function lunasi(int $id)
    {
        $booking = $this->bookingModel->find($id);

        if (! $booking || $booking['status'] !== 'success') {
            return redirect()->back()->with('error', 'Booking tidak ditemukan atau tidak valid.');
        }

        $this->bookingModel->markAsLunas($id);

        return redirect()->to(site_url('admin/pelunasan'))
            ->with('success', "Booking #{$booking['booking_code']} berhasil dilunasi.");
    }

    /** API: Inisialisasi QRIS untuk Pelunasan */
    public function pelunasanQrisInit(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json      = $this->request->getJSON(true);
        $bookingId = (int) ($json['booking_id'] ?? 0);

        $booking = $this->bookingModel->find($bookingId);

        if (! $booking || $booking['status'] !== 'success') {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
            ]);
        }

        if ($booking['status_pelunasan'] === 'lunas') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Booking ini sudah dilunasi.',
            ]);
        }

        $paymentKu   = new PaymentKu();
        $sisaTagihan = (float) $booking['sisa_tagihan'];
        $orderId     = $booking['booking_code'] . '-LNS';

        $result = $paymentKu->createQrisTransaction(
            $orderId,
            $sisaTagihan,
            $booking['nama_pemesan'],
            $booking['nomor_wa'],
            site_url('payment/callback')
        );

        if (! $result['success']) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal membuat QRIS: ' . ($result['error'] ?? 'Unknown error'),
            ]);
        }

        $this->bookingModel->update($bookingId, [
            'payment_token' => $result['token'],
        ]);

        return $this->response->setJSON([
            'success'      => true,
            'qr_url'       => $result['qr_url'],
            'qr_string'    => $result['qr_string'],
            'pay_url'      => $result['pay_url'],
            'sisa_tagihan' => $sisaTagihan,
        ]);
    }

    /** API: Polling status QRIS Pelunasan */
    public function pelunasanQrisStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json      = $this->request->getJSON(true);
        $bookingId = (int) ($json['booking_id'] ?? 0);
        $booking   = $this->bookingModel->find($bookingId);

        if (! $booking) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Booking tidak ditemukan.']);
        }

        if ($booking['status_pelunasan'] === 'lunas') {
            return $this->response->setJSON([
                'success' => true,
                'paid'    => true,
                'message' => 'Pembayaran QRIS terkonfirmasi. Booking sudah lunas!',
            ]);
        }

        $paymentKu = new PaymentKu();
        $checkId = ! empty($booking['payment_token'])
            ? $booking['payment_token']
            : $booking['booking_code'] . '-LNS';

        $result = $paymentKu->checkStatus($checkId);

        if ($result['status'] === 'success') {
            $this->bookingModel->markAsLunas($bookingId);
            return $this->response->setJSON([
                'success' => true,
                'paid'    => true,
                'message' => 'Pembayaran QRIS terkonfirmasi. Booking sudah lunas!',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'paid' => false]);
    }

    // ─── Check-in QR Scanner ──────────────────────────────────────────────────

    /**
     * Halaman scanner QR Check-in untuk admin.
     * GET /admin/checkin
     */
    public function checkin(): string
    {
        return view('admin/checkin', [
            'page_title' => 'Check-in Scanner',
        ]);
    }

    /**
     * API: Cari booking berdasarkan kode (dari hasil scan QR / input manual).
     * POST /admin/checkin/scan
     * Body JSON: { "booking_code": "T4S-XXXXXXXX" }
     */
    public function scanResult(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json        = $this->request->getJSON(true);
        $bookingCode = strtoupper(trim($json['booking_code'] ?? ''));

        if (empty($bookingCode)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Kode booking tidak boleh kosong.',
            ]);
        }

        $booking = $this->bookingModel
            ->select('bookings.*, lapangans.nama_lapangan')
            ->join('lapangans', 'lapangans.id = bookings.lapangan_id')
            ->where('bookings.booking_code', $bookingCode)
            ->first();

        if (! $booking) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => "Booking dengan kode <strong>{$bookingCode}</strong> tidak ditemukan.",
            ]);
        }

        if ($booking['status'] !== 'success') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Booking ini belum dikonfirmasi (status: ' . $booking['status'] . '). Tidak dapat check-in.',
            ]);
        }

        if ($booking['is_checked_in']) {
            return $this->response->setStatusCode(422)->setJSON([
                'success'    => false,
                'already_in' => true,
                'message'    => 'User sudah check-in pada ' . date('H:i, d M Y', strtotime($booking['checkin_at'])) . '.',
                'booking'    => $this->sanitizeBookingForJson($booking),
            ]);
        }

        $jams   = explode(',', $booking['jam_main']);
        $jamFmt = implode(', ', array_map(fn($j) => sprintf('%02d:00', trim($j)), $jams));

        return $this->response->setJSON([
            'success'       => true,
            'needs_payment' => ($booking['status_pelunasan'] === 'belum_lunas'),
            'sisa_tagihan'  => (float) $booking['sisa_tagihan'],
            'booking'       => $this->sanitizeBookingForJson($booking),
            'jam_fmt'       => $jamFmt,
        ]);
    }

    /**
     * API: Proses check-in.
     * POST /admin/checkin/proses
     * Body JSON: { "booking_id": 1, "method": "cash"|"qris"|null }
     */
    public function doCheckin(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json      = $this->request->getJSON(true);
        $bookingId = (int) ($json['booking_id'] ?? 0);
        $method    = $json['method'] ?? null;

        $booking = $this->bookingModel->find($bookingId);

        if (! $booking || $booking['status'] !== 'success') {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Booking tidak ditemukan.',
            ]);
        }

        if ($booking['is_checked_in']) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Booking ini sudah check-in.',
            ]);
        }

        // ── Jika QRIS: buat transaksi pelunasan baru via PaymentKu ────────────
        if ($method === 'qris') {
            $paymentKu   = new PaymentKu();
            $sisaTagihan = (float) $booking['sisa_tagihan'];
            $orderId     = $booking['booking_code'] . '-LNS';

            $result = $paymentKu->createQrisTransaction(
                $orderId,
                $sisaTagihan,
                $booking['nama_pemesan'],
                $booking['nomor_wa'],
                site_url('payment/callback')
            );

            if (! $result['success']) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat QRIS: ' . ($result['error'] ?? 'Unknown error'),
                ]);
            }

            // Simpan token sementara untuk polling
            $this->bookingModel->update($bookingId, [
                'payment_token' => $result['token'],
            ]);

            return $this->response->setJSON([
                'success'      => true,
                'mode'         => 'qris_pending',
                'qr_url'       => $result['qr_url'],
                'qr_string'    => $result['qr_string'],
                'pay_url'      => $result['pay_url'],
                'sisa_tagihan' => $sisaTagihan,
                'booking_id'   => $bookingId,
                'message'      => 'Silakan scan QR QRIS berikut untuk melunasi sisa tagihan.',
            ]);
        }

        // ── Cash atau sudah lunas: langsung check-in ──────────────────────────
        // Bungkus dalam transaction + re-read row untuk cegah double check-in
        // jika dua request datang bersamaan (misal: admin tekan 2x cepat).
        $db = db_connect();
        $db->transStart();

        $fresh = $db->table('bookings')->where('id', $bookingId)->get()->getRowArray();
        if ($fresh['is_checked_in']) {
            $db->transRollback();
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Booking ini sudah check-in (terdeteksi bersamaan).',
            ]);
        }

        $this->bookingModel->markAsCheckedIn($bookingId, $method);
        $db->transComplete();

        return $this->response->setJSON([
            'success' => true,
            'mode'    => 'done',
            'message' => $method === 'cash'
                ? 'Pelunasan cash berhasil. User berhasil check-in!'
                : 'User berhasil check-in!',
        ]);
    }

    /**
     * API: Poll status QRIS pelunasan saat checkin.
     * POST /admin/checkin/qris-status
     * Body JSON: { "booking_id": 1 }
     */
    public function qrisCheckinStatus(): \CodeIgniter\HTTP\ResponseInterface
    {
        $json      = $this->request->getJSON(true);
        $bookingId = (int) ($json['booking_id'] ?? 0);
        $booking   = $this->bookingModel->find($bookingId);

        if (! $booking) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Booking tidak ditemukan.']);
        }

        // Sudah check-in sebelumnya (termasuk lewat webhook callback)
        if ($booking['is_checked_in']) {
            return $this->response->setJSON([
                'success' => true,
                'paid'    => true,
                'done'    => true,
                'message' => 'Pembayaran QRIS terkonfirmasi. User berhasil check-in!',
            ]);
        }

        $paymentKu = new PaymentKu();

        // Gunakan payment_token (trx_id asli dari PaymentKu) jika tersimpan,
        // fallback ke reference_id (booking_code-LNS) jika token belum ada.
        $checkId = ! empty($booking['payment_token'])
            ? $booking['payment_token']
            : $booking['booking_code'] . '-LNS';

        $result = $paymentKu->checkStatus($checkId);

        log_message('debug', '[qrisCheckinStatus] booking_id={id} checkId={cid} status={s}', [
            'id'  => $bookingId,
            'cid' => $checkId,
            's'   => $result['status'],
        ]);

        if ($result['status'] === 'success') {
            $this->bookingModel->markAsCheckedIn($bookingId, 'qris');
            return $this->response->setJSON([
                'success' => true,
                'paid'    => true,
                'done'    => true,
                'message' => 'Pembayaran QRIS terkonfirmasi. User berhasil check-in!',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'paid' => false, 'done' => false]);
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function sanitizeBookingForJson(array $booking): array
    {
        return [
            'id'               => $booking['id'],
            'booking_code'     => $booking['booking_code'],
            'nama_pemesan'     => $booking['nama_pemesan'],
            'nomor_wa'         => $booking['nomor_wa'],
            'lapangan_id'      => $booking['lapangan_id'],
            'nama_lapangan'    => $booking['nama_lapangan'],
            'tanggal_main'     => $booking['tanggal_main'],
            'jam_main'         => $booking['jam_main'],
            'skema_pembayaran' => $booking['skema_pembayaran'],
            'total_harga'      => (float) $booking['total_harga'],
            'jumlah_dibayar'   => (float) $booking['jumlah_dibayar'],
            'sisa_tagihan'     => (float) $booking['sisa_tagihan'],
            'status'           => $booking['status'],
            'status_pelunasan' => $booking['status_pelunasan'],
            'is_checked_in'    => (bool) $booking['is_checked_in'],
            'checkin_at'       => $booking['checkin_at'],
        ];
    }
}

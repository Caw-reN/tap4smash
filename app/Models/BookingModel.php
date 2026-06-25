<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table         = 'bookings';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'booking_code', 'lapangan_id', 'nama_pemesan', 'nomor_wa',
        'tanggal_main', 'jam_main',
        'total_harga', 'skema_pembayaran', 'jumlah_dibayar', 'sisa_tagihan',
        'status', 'status_pelunasan', 'payment_token', 'expires_at',
        'is_checked_in', 'checkin_at', 'checkin_method',
    ];

    // ─── Cleanup ───────────────────────────────────────────────────────────────

    /**
     * Bebaskan slot pending yang sudah melewati expires_at.
     * Dipanggil di awal setiap render kalender (F-02).
     */
    public function cleanupExpiredSlots(): int
    {
        return $this->db->table($this->table)
            ->where('status', 'pending')
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->update(['status' => 'expired']);
    }

    // ─── Admin Queries ────────────────────────────────────────────────────────

    /**
     * Semua booking dengan join nama lapangan.
     * Mendukung filter: tanggal, lapangan_id, status.
     */
    public function getAllWithFilters(array $filters = []): array
    {
        $builder = $this->db->table('bookings b')
            ->select('b.*, l.nama_lapangan')
            ->join('lapangans l', 'l.id = b.lapangan_id')
            ->orderBy('b.created_at', 'DESC');

        if (! empty($filters['tanggal'])) {
            $builder->where('b.tanggal_main', $filters['tanggal']);
        }
        if (! empty($filters['lapangan_id'])) {
            $builder->where('b.lapangan_id', $filters['lapangan_id']);
        }
        if (! empty($filters['status'])) {
            $builder->where('b.status', $filters['status']);
        }
        if (! empty($filters['status_pelunasan'])) {
            $builder->where('b.status_pelunasan', $filters['status_pelunasan']);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Booking sukses yang belum lunas (untuk halaman pelunasan DP).
     */
    public function getPendingPelunasan(): array
    {
        return $this->db->table('bookings b')
            ->select('b.*, l.nama_lapangan')
            ->join('lapangans l', 'l.id = b.lapangan_id')
            ->where('b.status', 'success')
            ->where('b.status_pelunasan', 'belum_lunas')
            ->orderBy('b.tanggal_main', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Tandai booking sebagai lunas (pelunasan tunai di kasir).
     */
    public function markAsLunas(int $id): bool
    {
        return (bool) $this->update($id, [
            'sisa_tagihan'     => 0,
            'status_pelunasan' => 'lunas',
        ]);
    }

    /**
     * Tandai booking sebagai checked-in (masuk GOR).
     * Jika ada pelunasan, catat metodenya (cash/qris).
     *
     * @param int         $id     ID booking
     * @param string|null $method 'cash'|'qris'|null (null jika sudah lunas sebelum checkin)
     */
    public function markAsCheckedIn(int $id, ?string $method = null): bool
    {
        $data = [
            'is_checked_in' => 1,
            'checkin_at'    => date('Y-m-d H:i:s'),
        ];

        if ($method !== null) {
            $data['checkin_method']   = $method;
            $data['sisa_tagihan']     = 0;
            $data['status_pelunasan'] = 'lunas';
        }

        return (bool) $this->update($id, $data);
    }

    // ─── Dashboard Stats ──────────────────────────────────────────────────────

    public function countTodayBookings(): int
    {
        return $this->where('tanggal_main', date('Y-m-d'))
            ->where('status', 'success')
            ->countAllResults();
    }

    public function sumTodayRevenue(): float
    {
        $result = $this->db->table($this->table)
            ->selectSum('jumlah_dibayar', 'total')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->where('status', 'success')
            ->get()->getRow();

        return (float) ($result->total ?? 0);
    }

    public function countPendingPelunasan(): int
    {
        return $this->where('status', 'success')
            ->where('status_pelunasan', 'belum_lunas')
            ->countAllResults();
    }

    public function countPendingPayment(): int
    {
        return $this->where('status', 'pending')
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->countAllResults();
    }

    /**
     * 5 booking terbaru untuk widget dashboard.
     */
    public function getRecentBookings(int $limit = 5): array
    {
        return $this->db->table('bookings b')
            ->select('b.booking_code, b.nama_pemesan, b.tanggal_main, b.jam_main, b.status, b.skema_pembayaran, l.nama_lapangan')
            ->join('lapangans l', 'l.id = b.lapangan_id')
            ->where('b.status', 'success')
            ->orderBy('b.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}

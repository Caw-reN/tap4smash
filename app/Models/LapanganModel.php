<?php

namespace App\Models;

use CodeIgniter\Model;

class LapanganModel extends Model
{
    protected $table         = 'lapangans';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = ['nama_lapangan', 'jenis_lapangan', 'harga_per_jam', 'is_active'];

    // ─── Queries ──────────────────────────────────────────────────────────────

    /** Semua lapangan aktif (untuk dropdown booking) */
    public function getActive(): array
    {
        return $this->where('is_active', 1)->orderBy('nama_lapangan', 'ASC')->findAll();
    }

    /** Toggle status aktif/maintenance */
    public function toggleStatus(int $id): bool
    {
        $lapangan = $this->find($id);
        if (! $lapangan) return false;

        return (bool) $this->update($id, ['is_active' => $lapangan['is_active'] ? 0 : 1]);
    }
}

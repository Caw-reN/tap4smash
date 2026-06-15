<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJenisLapanganToLapangans extends Migration
{
    public function up()
    {
        // Pengecekan agar tidak error jika kolom sudah ditambahkan secara manual
        if (! $this->db->fieldExists('jenis_lapangan', 'lapangans')) {
            $this->forge->addColumn('lapangans', [
                'jenis_lapangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'nama_lapangan',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('jenis_lapangan', 'lapangans')) {
            $this->forge->dropColumn('lapangans', 'jenis_lapangan');
        }
    }
}

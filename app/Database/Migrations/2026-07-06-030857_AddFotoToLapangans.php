<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToLapangans extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('lapangans', [
            'foto' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'jenis_lapangan',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('lapangans', 'foto');
    }
}

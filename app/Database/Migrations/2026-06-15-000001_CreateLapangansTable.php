<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLapangansTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_lapangan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Contoh: Lapangan A, Lapangan B',
            ],
            'harga_per_jam' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1=Aktif, 0=Maintenance',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('lapangans', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('lapangans', true);
    }
}

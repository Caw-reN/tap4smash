<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBookingsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'booking_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'Format: T4S-XXXXXXXX',
            ],
            'lapangan_id' => [
                'type'     => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'nama_pemesan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'nomor_wa' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'comment'    => 'Format internasional: 628xxxxxxxxxx',
            ],
            'tanggal_main' => [
                'type' => 'DATE',
            ],
            'jam_main' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'comment'    => 'Format: comma separated hours, e.g. 10,12,14',
            ],
            'total_harga' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'skema_pembayaran' => [
                'type'       => 'ENUM',
                'constraint' => ['dp', 'full'],
            ],
            'jumlah_dibayar' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'sisa_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'success', 'expired', 'failed'],
                'default'    => 'pending',
            ],
            'status_pelunasan' => [
                'type'       => 'ENUM',
                'constraint' => ['lunas', 'belum_lunas'],
                'default'    => 'belum_lunas',
            ],
            'payment_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addUniqueKey('booking_code');
        $this->forge->addKey(['lapangan_id', 'tanggal_main'], false, false, 'idx_lapangan_tanggal');
        $this->forge->addKey(['status', 'expires_at'], false, false, 'idx_status_expires');
        $this->forge->addForeignKey('lapangan_id', 'lapangans', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('bookings', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('bookings', true);
    }
}

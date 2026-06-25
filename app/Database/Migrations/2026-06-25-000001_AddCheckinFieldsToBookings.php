<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tambahkan kolom untuk fitur Check-in QR Code.
 *
 * - is_checked_in  : apakah user sudah check-in di GOR
 * - checkin_at     : waktu check-in
 * - checkin_method : metode pelunasan saat checkin (cash / qris / null jika sudah lunas)
 */
class AddCheckinFieldsToBookings extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('bookings', [
            'is_checked_in' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'unsigned'   => true,
                'default'    => 0,
                'after'      => 'status_pelunasan',
            ],
            'checkin_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'is_checked_in',
            ],
            'checkin_method' => [
                'type'       => 'ENUM',
                'constraint' => ['cash', 'qris'],
                'null'       => true,
                'default'    => null,
                'after'      => 'checkin_at',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('bookings', ['is_checked_in', 'checkin_at', 'checkin_method']);
    }
}

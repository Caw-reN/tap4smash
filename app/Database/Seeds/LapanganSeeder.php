<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LapanganSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_lapangan' => 'Lapangan A',
                'harga_per_jam' => 50000,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lapangan' => 'Lapangan B',
                'harga_per_jam' => 50000,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lapangan' => 'Lapangan C',
                'harga_per_jam' => 60000,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_lapangan' => 'Lapangan VIP',
                'harga_per_jam' => 80000,
                'is_active'     => 1,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('lapangans')->insertBatch($data);
    }
}

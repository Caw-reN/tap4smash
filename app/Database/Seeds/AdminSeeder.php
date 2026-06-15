<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * AdminSeeder
 *
 * Membuat akun admin default untuk Tap4Smash.
 * Username: admin | Password: tap4smash2025
 *
 * PENTING: Ganti password setelah pertama kali login di production!
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'username'   => 'admin',
            'password'   => password_hash('tap4smash2025', PASSWORD_BCRYPT),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table('admins')->insert($data);
    }
}

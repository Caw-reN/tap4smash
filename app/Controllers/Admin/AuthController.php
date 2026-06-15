<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

/**
 * AuthController
 *
 * Menangani login dan logout admin Tap4Smash.
 * Session: admin_logged_in (bool), admin_username (string)
 */
class AuthController extends BaseController
{
    private AdminModel $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
    }

    // ─────────────────────────────────────────────────────────────────────────

    /** Tampilkan halaman login */
    public function login()
    {
        // Jika sudah login, langsung ke dashboard
        if (session()->get('admin_logged_in')) {
            return redirect()->to(site_url('admin/dashboard'));
        }

        return view('admin/auth/login', [
            'error' => session()->getFlashdata('error'),
        ]);
    }

    /** Proses form login */
    public function doLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Validasi input
        if (empty($username) || empty($password)) {
            return redirect()->back()
                ->with('error', 'Username dan password wajib diisi.');
        }

        // Cari admin di DB
        $admin = $this->adminModel->findByUsername($username);

        if (! $admin || ! password_verify($password, $admin['password'])) {
            return redirect()->back()
                ->with('error', 'Username atau password salah.');
        }

        // Set session
        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $admin['id'],
            'admin_username'  => $admin['username'],
        ]);

        return redirect()->to(site_url('admin/dashboard'));
    }

    /** Logout dan hancurkan session */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('admin/login'))
            ->with('error', 'Kamu berhasil logout.');
    }
}

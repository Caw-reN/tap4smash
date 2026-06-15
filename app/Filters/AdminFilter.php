<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AdminFilter
 *
 * Memproteksi semua route /admin/* kecuali /admin/login.
 * Diregistrasikan di app/Config/Filters.php.
 */
class AdminFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Lewati filter untuk halaman login agar tidak terjadi redirect loop
        $path = trim($request->getUri()->getPath(), '/');
        if ($path === 'admin/login') {
            return;
        }

        if (! session()->get('admin_logged_in')) {
            return redirect()->to(site_url('admin/login'))
                ->with('error', 'Sesi habis. Silakan login kembali.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}

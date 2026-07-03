<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingController extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();

        $data = [
            'paymentku_api_key'        => $settingModel->getValue('paymentku_api_key', env('paymentku.secretKey', '')),
            'paymentku_webhook_secret' => $settingModel->getValue('paymentku_webhook_secret', env('paymentku.webhookSecret', '')),
            'paymentku_base_url'       => $settingModel->getValue('paymentku_base_url', env('paymentku.baseUrl', 'https://paymenku.com/api/v1')),
            // Lokasi Maps
            'maps_embed_url'           => $settingModel->getValue('maps_embed_url', ''),
            'maps_label'               => $settingModel->getValue('maps_label', 'GOR Tap4Smash'),
            'maps_address'             => $settingModel->getValue('maps_address', ''),
        ];

        return view('admin/settings', $data);
    }

    public function update()
    {
        $settingModel = new SettingModel();

        $apiKey   = $this->request->getPost('paymentku_api_key');
        $whSecret = $this->request->getPost('paymentku_webhook_secret');
        $baseUrl  = $this->request->getPost('paymentku_base_url');

        if ($apiKey !== null) {
            $settingModel->setValue('paymentku_api_key', $apiKey);
        }
        if ($whSecret !== null) {
            $settingModel->setValue('paymentku_webhook_secret', $whSecret);
        }
        if ($baseUrl !== null) {
            $settingModel->setValue('paymentku_base_url', $baseUrl);
        }

        return redirect()->to('admin/settings')->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function updateMaps()
    {
        $settingModel = new SettingModel();

        $embedUrl = trim($this->request->getPost('maps_embed_url') ?? '');
        $label    = trim($this->request->getPost('maps_label') ?? '');
        $address  = trim($this->request->getPost('maps_address') ?? '');

        $settingModel->setValue('maps_embed_url', $embedUrl);
        $settingModel->setValue('maps_label',     $label ?: 'GOR Tap4Smash');
        $settingModel->setValue('maps_address',   $address);

        return redirect()->to('admin/settings')->with('success', 'Pengaturan lokasi berhasil disimpan.');
    }

    public function uploadLogo()
    {
        $file = $this->request->getFile('logo_file');

        // Validasi: wajib ada, harus gambar, max 2MB
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return redirect()->to('admin/settings')->with('error', 'Tidak ada file yang dipilih.');
        }

        $allowed = ['image/png', 'image/jpeg', 'image/webp', 'image/svg+xml'];
        if (! in_array($file->getMimeType(), $allowed)) {
            return redirect()->to('admin/settings')->with('error', 'Format file tidak didukung. Gunakan PNG, JPG, atau WEBP.');
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->to('admin/settings')->with('error', 'Ukuran file terlalu besar. Maksimum 2MB.');
        }

        // Tentukan ekstensi dan simpan ke public/img/logo.<ext>
        $ext      = $file->getClientExtension();
        $destDir  = FCPATH . 'img';
        $destFile = 'logo.' . $ext;

        // Hapus logo lama (semua ekstensi yang mungkin)
        foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $oldExt) {
            $old = $destDir . DIRECTORY_SEPARATOR . 'logo.' . $oldExt;
            if (file_exists($old)) {
                unlink($old);
            }
        }

        if (! is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        $file->move($destDir, $destFile, true);

        return redirect()->to('admin/settings')->with('success', 'Logo berhasil diperbarui!');
    }
}

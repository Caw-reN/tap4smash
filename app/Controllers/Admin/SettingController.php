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
        ];

        return view('admin/settings', $data);
    }

    public function update()
    {
        $settingModel = new SettingModel();

        $apiKey  = $this->request->getPost('paymentku_api_key');
        $whSecret = $this->request->getPost('paymentku_webhook_secret');
        $baseUrl = $this->request->getPost('paymentku_base_url');

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
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LapanganModel;

class LapanganController extends BaseController
{
    private LapanganModel $lapanganModel;

    public function __construct()
    {
        $this->lapanganModel = new LapanganModel();
        helper(['form', 'filesystem']);
    }

    /** Daftar semua lapangan */
    public function index(): string
    {
        return view('admin/lapangans/index', [
            'page_title' => 'Master Lapangan',
            'lapangans'  => $this->lapanganModel->orderBy('id', 'ASC')->findAll(),
            'success'    => session()->getFlashdata('success'),
            'error'      => session()->getFlashdata('error'),
        ]);
    }

    /** Form tambah lapangan */
    public function create(): string
    {
        return view('admin/lapangans/form', [
            'page_title' => 'Tambah Lapangan',
            'lapangan'   => null,
        ]);
    }

    /** Simpan lapangan baru */
    public function store()
    {
        $rules = [
            'nama_lapangan'  => 'required|min_length[2]|max_length[50]',
            'jenis_lapangan' => 'permit_empty|max_length[100]',
            'harga_per_jam'  => 'required|numeric|greater_than[0]',
            'foto'           => 'permit_empty|is_image[foto]|max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/lapangans/form', [
                'page_title' => 'Tambah Lapangan',
                'lapangan'   => null,
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'nama_lapangan'  => $this->request->getPost('nama_lapangan'),
            'jenis_lapangan' => $this->request->getPost('jenis_lapangan'),
            'harga_per_jam'  => $this->request->getPost('harga_per_jam'),
            'is_active'      => 1,
        ];

        // Handle upload foto
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            $namaFile = $foto->getRandomName();
            $foto->move(FCPATH . 'img/lapangans', $namaFile);
            $data['foto'] = $namaFile;
        }

        $this->lapanganModel->insert($data);

        return redirect()->to(site_url('admin/lapangan'))
            ->with('success', 'Lapangan baru berhasil ditambahkan.');
    }

    /** Form edit lapangan */
    public function edit(int $id): string
    {
        $lapangan = $this->lapanganModel->find($id);

        if (! $lapangan) {
            return redirect()->to(site_url('admin/lapangan'))
                ->with('error', 'Lapangan tidak ditemukan.');
        }

        return view('admin/lapangans/form', [
            'page_title' => 'Edit Lapangan',
            'lapangan'   => $lapangan,
        ]);
    }

    /** Update lapangan */
    public function update(int $id)
    {
        $lapangan = $this->lapanganModel->find($id);
        if (! $lapangan) {
            return redirect()->to(site_url('admin/lapangan'))
                ->with('error', 'Lapangan tidak ditemukan.');
        }

        $rules = [
            'nama_lapangan'  => 'required|min_length[2]|max_length[50]',
            'jenis_lapangan' => 'permit_empty|max_length[100]',
            'harga_per_jam'  => 'required|numeric|greater_than[0]',
            'foto'           => 'permit_empty|is_image[foto]|max_size[foto,2048]|mime_in[foto,image/jpg,image/jpeg,image/png,image/webp]',
        ];

        if (! $this->validate($rules)) {
            return view('admin/lapangans/form', [
                'page_title' => 'Edit Lapangan',
                'lapangan'   => $lapangan,
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        $data = [
            'nama_lapangan'  => $this->request->getPost('nama_lapangan'),
            'jenis_lapangan' => $this->request->getPost('jenis_lapangan'),
            'harga_per_jam'  => $this->request->getPost('harga_per_jam'),
        ];

        // Handle upload foto baru
        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && ! $foto->hasMoved()) {
            // Hapus foto lama jika ada
            if (! empty($lapangan['foto'])) {
                $fotoLama = FCPATH . 'img/lapangans/' . $lapangan['foto'];
                if (file_exists($fotoLama)) {
                    unlink($fotoLama);
                }
            }
            $namaFile = $foto->getRandomName();
            $foto->move(FCPATH . 'img/lapangans', $namaFile);
            $data['foto'] = $namaFile;
        }

        // Handle hapus foto (checkbox hapus foto)
        if ($this->request->getPost('hapus_foto') === '1' && empty($foto?->getName())) {
            if (! empty($lapangan['foto'])) {
                $fotoLama = FCPATH . 'img/lapangans/' . $lapangan['foto'];
                if (file_exists($fotoLama)) {
                    unlink($fotoLama);
                }
            }
            $data['foto'] = null;
        }

        $this->lapanganModel->update($id, $data);

        return redirect()->to(site_url('admin/lapangan'))
            ->with('success', 'Data lapangan berhasil diperbarui.');
    }

    /** Toggle status aktif / maintenance */
    public function toggleStatus(int $id)
    {
        $lapangan = $this->lapanganModel->find($id);
        if (! $lapangan) {
            return redirect()->back()->with('error', 'Lapangan tidak ditemukan.');
        }

        $this->lapanganModel->toggleStatus($id);

        $newStatus = $lapangan['is_active'] ? 'Maintenance' : 'Aktif';
        return redirect()->to(site_url('admin/lapangan'))
            ->with('success', "{$lapangan['nama_lapangan']} diubah ke status {$newStatus}.");
    }

    /** Hapus lapangan (hanya jika tidak ada booking aktif) */
    public function delete(int $id)
    {
        $lapangan = $this->lapanganModel->find($id);
        if (! $lapangan) {
            return redirect()->back()->with('error', 'Lapangan tidak ditemukan.');
        }

        // Cek booking aktif via BookingModel agar tidak ada raw query di controller
        $bookingModel   = new \App\Models\BookingModel();
        $activeBookings = $bookingModel->countActiveByLapangan($id);

        if ($activeBookings > 0) {
            return redirect()->to(site_url('admin/lapangan'))
                ->with('error', "Tidak bisa hapus {$lapangan['nama_lapangan']} — masih ada {$activeBookings} booking aktif.");
        }

        // Hapus file foto dari disk jika ada
        if (! empty($lapangan['foto'])) {
            $fotoPath = FCPATH . 'img/lapangans/' . $lapangan['foto'];
            if (file_exists($fotoPath)) {
                unlink($fotoPath);
            }
        }

        $this->lapanganModel->delete($id);

        return redirect()->to(site_url('admin/lapangan'))
            ->with('success', "{$lapangan['nama_lapangan']} berhasil dihapus.");
    }
}

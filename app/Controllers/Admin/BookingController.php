<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LapanganModel;

class BookingController extends BaseController
{
    private BookingModel  $bookingModel;
    private LapanganModel $lapanganModel;

    public function __construct()
    {
        $this->bookingModel  = new BookingModel();
        $this->lapanganModel = new LapanganModel();
    }

    /** Daftar semua booking dengan filter */
    public function index(): string
    {
        $filters = [
            'tanggal'          => $this->request->getGet('tanggal'),
            'lapangan_id'      => $this->request->getGet('lapangan_id'),
            'status'           => $this->request->getGet('status'),
            'status_pelunasan' => $this->request->getGet('status_pelunasan'),
        ];

        return view('admin/bookings/index', [
            'page_title' => 'Manajemen Booking',
            'bookings'   => $this->bookingModel->getAllWithFilters($filters),
            'lapangans'  => $this->lapanganModel->findAll(),
            'filters'    => $filters,
        ]);
    }

    /** Daftar booking yang perlu dilunasi (DP belum lunas) */
    public function pelunasan(): string
    {
        return view('admin/bookings/pelunasan', [
            'page_title' => 'Pelunasan DP',
            'bookings'   => $this->bookingModel->getPendingPelunasan(),
        ]);
    }

    /** Aksi: tandai booking sebagai lunas di kasir */
    public function lunasi(int $id)
    {
        $booking = $this->bookingModel->find($id);

        if (! $booking || $booking['status'] !== 'success') {
            return redirect()->back()->with('error', 'Booking tidak ditemukan atau tidak valid.');
        }

        $this->bookingModel->markAsLunas($id);

        return redirect()->to(site_url('admin/pelunasan'))
            ->with('success', "Booking #{$booking['booking_code']} berhasil dilunasi.");
    }
}

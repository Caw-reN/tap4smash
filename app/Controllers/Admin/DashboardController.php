<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\LapanganModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $bookingModel  = new BookingModel();
        $lapanganModel = new LapanganModel();

        // Cleanup slot expired setiap kali admin membuka dashboard
        $bookingModel->cleanupExpiredSlots();

        $data = [
            'page_title'          => 'Dashboard',
            'total_booking_hari'  => $bookingModel->countTodayBookings(),
            'total_revenue_hari'  => $bookingModel->sumTodayRevenue(),
            'pending_pelunasan'   => $bookingModel->countPendingPelunasan(),
            'pending_payment'     => $bookingModel->countPendingPayment(),
            'recent_bookings'     => $bookingModel->getRecentBookings(5),
            'total_lapangan'      => $lapanganModel->countAll(),
            // Stat tambahan
            'revenue_bulan'       => $bookingModel->sumMonthRevenue(),
            'checkin_hari'        => $bookingModel->countTodayCheckins(),
            'total_booking_bulan' => $bookingModel->countMonthBookings(),
        ];

        return view('admin/dashboard', $data);
    }
}

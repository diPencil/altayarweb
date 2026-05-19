<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Deposit;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminStatsController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        try {
            $totalUsers = User::count();
            $activeUsers = User::where('status', 1)->count();
            $suspendedUsers = User::where('status', 0)->count();

            $totalRevenue = (float) Deposit::where('status', 1)->sum('amount');

            $tourBookingsCount = TourBooking::count();
            $serviceBookingsCount = ServiceBooking::count();
            $totalBookings = $tourBookingsCount + $serviceBookingsCount;

            $tourPending = TourBooking::where('status', 0)->count();
            $servicePending = ServiceBooking::where('status', 0)->count();
            $pendingBookings = $tourPending + $servicePending;

            $tourConfirmed = TourBooking::where('status', 1)->count();
            $serviceConfirmed = ServiceBooking::where('status', 1)->count();
            $confirmedBookings = $tourConfirmed + $serviceConfirmed;

            $totalOrders = Invoice::count();
            $paidOrders = Invoice::where('status', 1)->count();
            $unpaidOrders = Invoice::where('status', '!=', 1)->count();

            return response()->json([
                'users' => [
                    'total' => $totalUsers,
                    'active' => $activeUsers,
                    'suspended' => $suspendedUsers,
                    'percentage_change' => 12.5,
                ],
                'revenue' => [
                    'total' => $totalRevenue,
                    'percentage_change' => 8.2,
                ],
                'bookings' => [
                    'total' => $totalBookings,
                    'pending' => $pendingBookings,
                    'confirmed' => $confirmedBookings,
                    'percentage_change' => 5.4,
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'paid' => $paidOrders,
                    'unpaid' => $unpaidOrders,
                    'percentage_change' => 10.1,
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch admin stats: ' . $e->getMessage()
            ], 500);
        }
    }
}

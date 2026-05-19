<?php

namespace App\Http\Controllers\Api\Mobile\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\TourBooking;
use App\Models\Listing;
use App\Models\SupportTicket;

class EmployeeStatsController extends Controller
{
    public function overview(Request $request): JsonResponse
    {
        $employeeId = $request->user()->id;

        $stats = [
            'assigned_customers' => User::where('agent_id', $employeeId)->count(),
            'total_tour_packages' => \App\Models\TourPackage::where('user_type', 'employee')->where('user_id', $employeeId)->count(),
            'total_listing_offers' => Listing::where('user_type', 'employee')->where('user_id', $employeeId)->whereNotNull('offer_type')->count(),
            'total_support_tickets' => SupportTicket::where('agent_id', $employeeId)->count(),
            'total_open_support_tickets' => SupportTicket::where('agent_id', $employeeId)->where('status', 0)->count(),
            'balance' => $request->user()->balance ?? 0,
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}

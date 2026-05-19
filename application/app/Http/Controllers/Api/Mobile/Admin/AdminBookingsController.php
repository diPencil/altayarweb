<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminBookingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $tourBookings = TourBooking::query()
                ->with(['tour_package', 'user'])
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (TourBooking $booking): array {
                    $status = match ((int) $booking->status) {
                        1 => 'CONFIRMED',
                        2 => 'CANCELLED',
                        default => 'PENDING',
                    };
                    $currency = strtoupper((string) ($booking->tour_package->currency ?? $booking->currency ?? 'EGP')) ?: 'EGP';
                    $titleEn = $booking->tour_package?->title ?? 'Tour Booking';
                    $titleAr = $booking->tour_package?->title_ar ?? $titleEn;
                    
                    $customerName = '';
                    if ($booking->user) {
                        $customerName = trim($booking->user->firstname . ' ' . $booking->user->lastname);
                        if ($customerName === '') {
                            $customerName = $booking->user->username;
                        }
                    }
                    if ($customerName === '') {
                        $customerName = 'Customer';
                    }

                    return [
                        'id' => $booking->id,
                        'booking_number' => $booking->reference_no,
                        'title' => $titleEn,
                        'title_en' => $titleEn,
                        'title_ar' => $titleAr,
                        'booking_type' => 'tour',
                        'status' => $status,
                        'total_amount' => (float) ($booking->price ?? 0),
                        'currency' => $currency,
                        'customer_name' => $customerName,
                        'creator_name' => $customerName,
                        'booking_source' => 'WEBSITE',
                        'created_at' => optional($booking->created_at)->toISOString(),
                    ];
                });

            $serviceBookings = ServiceBooking::query()
                ->with(['user'])
                ->orderByDesc('id')
                ->limit(50)
                ->get()
                ->map(function (ServiceBooking $booking): array {
                    $status = match ((int) $booking->status) {
                        1 => 'CONFIRMED',
                        2 => 'COMPLETED',
                        3 => 'CANCELLED',
                        default => 'PENDING',
                    };
                    $currency = strtoupper((string) ($booking->currency ?? 'EGP')) ?: 'EGP';
                    $title = $booking->title ?: 'Service Booking';
                    
                    $customerName = '';
                    if ($booking->user) {
                        $customerName = trim($booking->user->firstname . ' ' . $booking->user->lastname);
                        if ($customerName === '') {
                            $customerName = $booking->user->username;
                        }
                    }
                    if ($customerName === '') {
                        $customerName = 'Customer';
                    }

                    return [
                        'id' => $booking->id,
                        'booking_number' => $booking->reference_no,
                        'title' => $title,
                        'title_en' => $title,
                        'title_ar' => $title,
                        'booking_type' => (string) ($booking->booking_type ?: 'service'),
                        'status' => $status,
                        'total_amount' => (float) ($booking->amount ?? 0),
                        'currency' => $currency,
                        'customer_name' => $customerName,
                        'creator_name' => $customerName,
                        'booking_source' => 'WEBSITE',
                        'created_at' => optional($booking->created_at)->toISOString(),
                    ];
                });

            $bookings = $tourBookings->concat($serviceBookings)
                ->sortByDesc('created_at')
                ->values()
                ->all();

            return response()->json($bookings);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch admin bookings: ' . $e->getMessage()
            ], 500);
        }
    }
}

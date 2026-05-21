<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use App\Models\TourBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminBookingsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $search = trim((string) $request->input('search', ''));
            $status = strtoupper(trim((string) $request->input('status', '')));
            $type = strtoupper(trim((string) $request->input('type', '')));
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', 50)), 100));

            $bookings = [];

            $tourBookings = TourBooking::with(['user', 'tour_package', 'deposit'])
                ->where('owner_type', 'admin')
                ->where('owner_id', auth('admin')->id())
                ->latest()
                ->get();

            foreach ($tourBookings as $booking) {
                $bookings[] = $this->tourBookingPayload($booking);
            }

            $serviceBookings = ServiceBooking::with(['user', 'admin'])
                ->latest()
                ->get();

            foreach ($serviceBookings as $booking) {
                $bookings[] = $this->serviceBookingPayload($booking);
            }

            $bookings = array_values(array_filter($bookings, function (array $booking) use ($search, $status, $type): bool {
                if ($type !== '' && strtoupper((string) ($booking['booking_kind'] ?? '')) !== $type && strtoupper((string) ($booking['booking_source'] ?? '')) !== $type) {
                    return false;
                }

                if ($status !== '') {
                    $bookingStatus = strtoupper((string) ($booking['status'] ?? ''));
                    $paymentStatus = strtoupper((string) ($booking['payment_status'] ?? ''));

                    if ($status !== $bookingStatus && $status !== $paymentStatus) {
                        return false;
                    }
                }

                if ($search === '') {
                    return true;
                }

                $haystack = implode(' ', array_filter([
                    (string) ($booking['booking_number'] ?? ''),
                    (string) ($booking['customer_name'] ?? ''),
                    (string) ($booking['creator_name'] ?? ''),
                    (string) ($booking['title_en'] ?? ''),
                    (string) ($booking['title_ar'] ?? ''),
                    (string) ($booking['booking_kind'] ?? ''),
                    (string) ($booking['booking_source'] ?? ''),
                ]));

                return stripos($haystack, $search) !== false;
            }));

            usort($bookings, function (array $left, array $right): int {
                return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
            });

            $total = count($bookings);
            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($bookings, $offset, $perPage);

            return response()->json([
                'success' => true,
                'bookings' => array_values($pageItems),
                'meta' => $this->paginationMeta($page, $perPage, $total),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch bookings: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $tourBooking = TourBooking::with(['user', 'tour_package', 'deposit'])
                ->where('owner_type', 'admin')
                ->where('owner_id', auth('admin')->id())
                ->find($id);

            if ($tourBooking) {
                return response()->json([
                    'success' => true,
                    'booking' => $this->tourBookingPayload($tourBooking, true),
                ]);
            }

            $serviceBooking = ServiceBooking::with(['user', 'admin'])->find($id);

            if ($serviceBooking) {
                return response()->json([
                    'success' => true,
                    'booking' => $this->serviceBookingPayload($serviceBooking, true),
                ]);
            }

            return response()->json([
                'detail' => 'Booking not found',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch booking details: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function tourBookingPayload(TourBooking $booking, bool $detail = false): array
    {
        $raw = $booking->toArray();
        $tourPackage = $booking->tour_package;
        $user = $booking->user;
        $deposit = $booking->deposit;

        $payload = [
            'id' => $booking->id,
            'booking_number' => $booking->reference_no ?: ('TB-' . $booking->id),
            'booking_kind' => 'TOUR',
            'booking_type' => $raw['booking_type'] ?? 'tour_booking',
            'booking_source' => strtoupper((string) ($raw['owner_type'] ?? 'website')),
            'title_en' => $tourPackage?->title ?? ($raw['title_en'] ?? $raw['title'] ?? ''),
            'title_ar' => $tourPackage?->title_ar ?? ($raw['title_ar'] ?? ''),
            'customer_name' => $user?->fullname ?? $user?->firstname ?? ($raw['customer_name'] ?? ''),
            'customer_email' => $user?->email,
            'creator_name' => $booking->owner?->name ?? $booking->employee?->name ?? $booking->admin?->name,
            'status' => $this->mapTourStatus((int) ($booking->status ?? 0)),
            'payment_status' => $this->mapDepositStatus($deposit?->status),
            'amount' => (float) ($raw['amount'] ?? $raw['total_amount'] ?? 0),
            'subtotal' => (float) ($raw['subtotal'] ?? $raw['amount'] ?? 0),
            'discount_amount' => (float) ($raw['discount_amount'] ?? 0),
            'currency' => $raw['currency'] ?? 'EGP',
            'guest_count' => (int) ($raw['guest_count'] ?? $raw['guests'] ?? 1),
            'booking_date' => $raw['booking_date'] ?? null,
            'start_date' => $raw['start_date'] ?? $raw['tour_date'] ?? null,
            'end_date' => $raw['end_date'] ?? null,
            'customer_notes' => $raw['notes'] ?? $raw['customer_notes'] ?? null,
            'membership_id' => $raw['membership_id'] ?? null,
            'created_at' => optional($booking->created_at)->toIso8601String(),
        ];

        if ($detail) {
            $payload['user'] = $user ? [
                'id' => (string) $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'phone' => $user->mobile,
            ] : null;
            $payload['tour_package'] = $tourPackage ? $tourPackage->toArray() : null;
            $payload['deposit'] = $deposit ? $deposit->toArray() : null;
            $payload['booking_details'] = $raw;
        }

        return $payload;
    }

    private function serviceBookingPayload(ServiceBooking $booking, bool $detail = false): array
    {
        $raw = $booking->toArray();
        $user = $booking->user;
        $admin = $booking->admin;

        $payload = [
            'id' => $booking->id,
            'booking_number' => $booking->reference_no ?: ('SB-' . $booking->id),
            'booking_kind' => 'SERVICE',
            'booking_type' => $raw['booking_type'] ?? 'service_booking',
            'booking_source' => 'ADMIN',
            'title_en' => $booking->title ?? ($raw['title_en'] ?? ''),
            'title_ar' => $raw['title_ar'] ?? '',
            'customer_name' => $user?->fullname ?? $user?->firstname ?? ($raw['customer_name'] ?? ''),
            'customer_email' => $user?->email,
            'creator_name' => $admin?->name,
            'status' => $this->mapServiceStatus((int) ($booking->status ?? 0)),
            'payment_status' => $this->mapServicePaymentStatus($booking),
            'amount' => (float) ($raw['amount'] ?? 0),
            'subtotal' => (float) ($raw['amount'] ?? 0),
            'discount_amount' => (float) ($raw['discount_amount'] ?? 0),
            'currency' => $raw['currency'] ?? 'EGP',
            'guest_count' => (int) ($raw['guest_count'] ?? $raw['guests'] ?? 1),
            'booking_date' => $raw['booking_date'] ?? null,
            'start_date' => $raw['service_date'] ?? null,
            'end_date' => $raw['service_end_date'] ?? null,
            'customer_notes' => $raw['notes'] ?? $raw['customer_notes'] ?? null,
            'membership_id' => $raw['membership_id'] ?? null,
            'created_at' => optional($booking->created_at)->toIso8601String(),
        ];

        if ($detail) {
            $payload['user'] = $user ? [
                'id' => (string) $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'first_name' => $user->firstname,
                'last_name' => $user->lastname,
                'phone' => $user->mobile,
            ] : null;
            $payload['admin'] = $admin ? [
                'id' => (string) $admin->id,
                'name' => $admin->name,
                'email' => $admin->email,
            ] : null;
            $payload['booking_details'] = $raw;
        }

        return $payload;
    }

    private function mapTourStatus(int $status): string
    {
        return match ($status) {
            1 => 'CONFIRMED',
            2 => 'CANCELLED',
            3 => 'COMPLETED',
            default => 'PENDING',
        };
    }

    private function mapServiceStatus(int $status): string
    {
        return match ($status) {
            1 => 'CONFIRMED',
            2 => 'COMPLETED',
            3 => 'CANCELLED',
            default => 'PENDING',
        };
    }

    private function mapDepositStatus($status): string
    {
        return match ((int) $status) {
            1 => 'PAID',
            3 => 'FAILED',
            2 => 'PENDING',
            default => 'PENDING',
        };
    }

    private function mapServicePaymentStatus(ServiceBooking $booking): string
    {
        if ((float) ($booking->paid_amount ?? 0) >= (float) ($booking->amount ?? 0) && (float) ($booking->amount ?? 0) > 0) {
            return 'PAID';
        }

        if ((float) ($booking->paid_amount ?? 0) > 0) {
            return 'PARTIALLY_PAID';
        }

        return 'PENDING';
    }

    private function paginationMeta(int $page, int $perPage, int $total): array
    {
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => $lastPage,
            'from' => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
            'to' => min($page * $perPage, $total),
        ];
    }
}

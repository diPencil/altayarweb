<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\ServiceBooking;
use App\Models\TourBooking;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingsController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $tourBookings = TourBooking::query()
            ->with(['tour_package', 'deposit'])
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (TourBooking $booking): array {
                return $this->formatTourBooking($booking);
            });

        $serviceBookings = ServiceBooking::query()
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (ServiceBooking $booking): array {
                return $this->formatServiceBooking($booking);
            });

        $bookings = $tourBookings->concat($serviceBookings)
            ->sortByDesc('created_at')
            ->values();

        return response()->json($bookings);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $tourBooking = TourBooking::query()
            ->with(['tour_package', 'deposit'])
            ->where('user_id', $user->id)
            ->find($id);

        if ($tourBooking) {
            return response()->json($this->formatTourBooking($tourBooking));
        }

        $serviceBooking = ServiceBooking::query()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($this->formatServiceBooking($serviceBooking));
    }

    private function formatTourBooking(TourBooking $booking): array
    {
        $depositStatus = optional($booking->deposit)->status;
        $paymentStatus = $this->mapBookingPaymentStatus((int) $booking->status, $depositStatus);
        $status = $this->mapTourStatus((int) $booking->status);
        $currency = strtoupper((string) ($booking->tour_package->currency ?? $booking->currency ?? 'EGP')) ?: 'EGP';
        $titleEn = $booking->tour_package?->title ?? 'Tour Booking';
        $titleAr = $booking->tour_package?->title_ar ?? $titleEn;

        return [
            'id' => $booking->id,
            'booking_number' => $booking->reference_no,
            'title' => $titleEn,
            'title_en' => $titleEn,
            'title_ar' => $titleAr,
            'type' => 'tour',
            'status' => $status,
            'payment_status' => $paymentStatus,
            'total_amount' => (float) ($booking->price ?? 0),
            'currency' => $currency,
            'start_date' => $this->toIsoString($booking->tour_package?->tour_start ?? $booking->created_at),
            'end_date' => $this->toIsoString($booking->tour_package?->tour_end),
            'created_at' => $this->toIsoString($booking->created_at),
        ];
    }

    private function formatServiceBooking(ServiceBooking $booking): array
    {
        $deposit = Deposit::query()
            ->where('service_booking_id', $booking->id)
            ->orderByDesc('id')
            ->first();

        $paymentStatus = $this->mapServicePaymentStatus($booking, $deposit?->status);
        $status = $this->mapServiceStatus((int) ($booking->status ?? 0));
        $currency = strtoupper((string) ($booking->currency ?? 'EGP')) ?: 'EGP';
        $title = $booking->title ?: 'Service Booking';

        return [
            'id' => $booking->id,
            'booking_number' => $booking->reference_no,
            'title' => $title,
            'title_en' => $title,
            'title_ar' => $title,
            'type' => (string) ($booking->booking_type ?: 'service'),
            'status' => $status,
            'payment_status' => $paymentStatus,
            'total_amount' => (float) ($booking->amount ?? 0),
            'currency' => $currency,
            'start_date' => $this->toIsoString($booking->service_date ?? $booking->booking_date ?? $booking->created_at),
            'end_date' => $this->toIsoString($booking->service_end_date),
            'created_at' => $this->toIsoString($booking->booking_date ?? $booking->created_at),
        ];
    }

    private function mapTourStatus(int $status): string
    {
        return match ($status) {
            1 => 'confirmed',
            2 => 'cancelled',
            default => 'pending',
        };
    }

    private function mapServiceStatus(int $status): string
    {
        return match ($status) {
            1 => 'confirmed',
            2 => 'completed',
            3 => 'cancelled',
            default => 'pending',
        };
    }

    private function mapBookingPaymentStatus(int $bookingStatus, $depositStatus): string
    {
        if ((int) $depositStatus === 1) {
            return 'paid';
        }

        if ((int) $depositStatus === 2) {
            return 'pending';
        }

        if ((int) $depositStatus === 3) {
            return 'failed';
        }

        return $bookingStatus === 2 ? 'cancelled' : 'unpaid';
    }

    private function mapServicePaymentStatus(ServiceBooking $booking, $depositStatus): string
    {
        if ((int) $depositStatus === 1) {
            return 'paid';
        }

        if ((int) $depositStatus === 2) {
            return 'pending';
        }

        if ((int) $depositStatus === 3) {
            return 'failed';
        }

        $paidAmount = (float) ($booking->paid_amount ?? 0);
        $totalAmount = (float) ($booking->amount ?? 0);

        if ($paidAmount > 0 && $paidAmount >= $totalAmount) {
            return 'paid';
        }

        return 'unpaid';
    }

    private function toIsoString(mixed $value): ?string
    {
        if ($value instanceof Carbon) {
            return $value->toISOString();
        }

        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse($value)->toISOString();
        } catch (\Throwable) {
            return null;
        }
    }
}

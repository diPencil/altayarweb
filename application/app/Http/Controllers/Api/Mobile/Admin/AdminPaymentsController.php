<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminPaymentsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $search = trim((string) $request->input('search', ''));
            $status = strtoupper(trim((string) $request->input('status', '')));
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', 50)), 100));

            $payments = Deposit::query()
                ->with(['user', 'gateway', 'tour_booking', 'service_booking'])
                ->orderByDesc('id')
                ->get()
                ->map(fn (Deposit $deposit): array => $this->paymentPayload($deposit))
                ->filter(function (array $payment) use ($search, $status): bool {
                    if ($status !== '' && strtoupper((string) ($payment['status'] ?? '')) !== $status) {
                        return false;
                    }

                    if ($search === '') {
                        return true;
                    }

                    $haystack = implode(' ', array_filter([
                        (string) ($payment['transaction_id'] ?? ''),
                        (string) ($payment['payment_method'] ?? ''),
                        (string) ($payment['source'] ?? ''),
                        (string) ($payment['user']['username'] ?? ''),
                        (string) ($payment['user']['email'] ?? ''),
                    ]));

                    return stripos($haystack, $search) !== false;
                })
                ->values()
                ->all();

            $summary = [
                'total_count' => count($payments),
                'total_amount' => 0.0,
                'paid_count' => 0,
                'paid_amount' => 0.0,
                'pending_count' => 0,
                'pending_amount' => 0.0,
                'failed_count' => 0,
                'failed_amount' => 0.0,
            ];

            foreach ($payments as $payment) {
                $amount = (float) ($payment['amount'] ?? 0);
                $summary['total_amount'] += $amount;

                if (($payment['status'] ?? 'PENDING') === 'PAID') {
                    $summary['paid_count']++;
                    $summary['paid_amount'] += $amount;
                } elseif (($payment['status'] ?? 'PENDING') === 'FAILED') {
                    $summary['failed_count']++;
                    $summary['failed_amount'] += $amount;
                } else {
                    $summary['pending_count']++;
                    $summary['pending_amount'] += $amount;
                }
            }

            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($payments, $offset, $perPage);

            return response()->json([
                'success' => true,
                'items' => array_values($pageItems),
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $summary['total_count'],
                    'last_page' => max(1, (int) ceil($summary['total_count'] / $perPage)),
                    'from' => $summary['total_count'] === 0 ? 0 : (($page - 1) * $perPage) + 1,
                    'to' => min($page * $perPage, $summary['total_count']),
                ],
                'summary' => $summary,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch admin payments: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $deposit = Deposit::query()
                ->with(['user', 'gateway', 'tour_booking', 'service_booking'])
                ->find($id);

            if (! $deposit) {
                return response()->json([
                    'detail' => 'Payment not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'payment' => $this->paymentPayload($deposit, true),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch payment details: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function paymentPayload(Deposit $deposit, bool $detail = false): array
    {
        $raw = $deposit->toArray();
        $status = $this->paymentStatus((int) ($deposit->status ?? 0));
        $user = $deposit->user;
        $gateway = $deposit->gateway;
        $sourceModel = $deposit->tour_booking ?: $deposit->service_booking;

        $payload = [
            'id' => $deposit->id,
            'user_id' => (string) $deposit->user_id,
            'user' => $user ? [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname ?: '',
                'email' => $user->email,
                'phone' => $user->mobile,
                'username' => $user->username,
            ] : null,
            'amount' => (float) ($deposit->amount ?? 0),
            'currency' => strtoupper((string) ($deposit->method_currency ?: 'EGP')),
            'status' => $status,
            'payment_method' => $gateway?->name ?: 'Online Payment',
            'payment_type' => 'DEPOSIT',
            'source' => $deposit->tour_booking_id ? 'TOUR_BOOKING' : ($deposit->service_booking_id ? 'SERVICE_BOOKING' : 'WALLET'),
            'transaction_id' => $raw['trx'] ?? $raw['transaction_id'] ?? $raw['reference_no'] ?? null,
            'description' => data_get($raw, 'detail.description') ?? $raw['remarks'] ?? $raw['note'] ?? null,
            'order_id' => $deposit->tour_booking_id ?? $deposit->service_booking_id ?? null,
            'related_booking_type' => $sourceModel ? class_basename($sourceModel) : null,
            'created_at' => optional($deposit->created_at)->toISOString(),
        ];

        if ($detail) {
            $payload['gateway'] = $gateway ? $gateway->toArray() : null;
            $payload['tour_booking'] = $deposit->tour_booking ? $deposit->tour_booking->toArray() : null;
            $payload['service_booking'] = $deposit->service_booking ? $deposit->service_booking->toArray() : null;
            $payload['payment_details'] = $raw;
        }

        return $payload;
    }

    private function paymentStatus(int $status): string
    {
        return match ($status) {
            1 => 'PAID',
            3 => 'FAILED',
            default => 'PENDING',
        };
    }
}

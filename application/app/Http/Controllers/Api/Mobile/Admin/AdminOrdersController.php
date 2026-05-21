<?php

namespace App\Http\Controllers\Api\Mobile\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminOrdersController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $search = trim((string) $request->input('search', ''));
            $status = strtoupper(trim((string) $request->input('status', '')));
            $page = max(1, (int) $request->integer('page', 1));
            $perPage = max(1, min((int) $request->integer('per_page', $request->integer('limit', 50)), 100));

            $invoices = Invoice::query()
                ->with(['user', 'items', 'booking'])
                ->orderByDesc('id')
                ->get()
                ->map(fn (Invoice $invoice): array => $this->orderPayload($invoice))
                ->filter(function (array $order) use ($search, $status): bool {
                    if ($status !== '' && strtoupper((string) ($order['status'] ?? '')) !== $status && strtoupper((string) ($order['payment_status'] ?? '')) !== $status) {
                        return false;
                    }

                    if ($search === '') {
                        return true;
                    }

                    $haystack = implode(' ', array_filter([
                        (string) ($order['order_number'] ?? ''),
                        (string) ($order['customer_name'] ?? ''),
                        (string) ($order['customer_email'] ?? ''),
                        (string) ($order['user']['username'] ?? ''),
                        (string) ($order['booking_reference'] ?? ''),
                    ]));

                    return stripos($haystack, $search) !== false;
                })
                ->values()
                ->all();

            usort($invoices, function (array $left, array $right) {
                return strcmp((string) ($right['created_at'] ?? ''), (string) ($left['created_at'] ?? ''));
            });

            $total = count($invoices);
            $offset = ($page - 1) * $perPage;
            $pageItems = array_slice($invoices, $offset, $perPage);

            return response()->json([
                'success' => true,
                'orders' => array_values($pageItems),
                'meta' => $this->paginationMeta($page, $perPage, $total),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch admin orders: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $invoice = Invoice::query()
                ->with(['user', 'items', 'booking'])
                ->find($id);

            if (! $invoice) {
                return response()->json([
                    'detail' => 'Order not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'order' => $this->orderPayload($invoice, true),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'detail' => 'Failed to fetch order details: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function orderPayload(Invoice $invoice, bool $detail = false): array
    {
        $raw = $invoice->toArray();
        $status = match ((int) $invoice->status) {
            1 => 'PAID',
            2 => 'PENDING',
            3 => 'CANCELLED',
            default => 'PENDING',
        };
        $currency = strtoupper((string) ($invoice->currency ?? 'EGP')) ?: 'EGP';

        $user = $invoice->user;
        $booking = $invoice->booking;

        $payload = [
            'id' => $invoice->id,
            'order_number' => $raw['invoice_number'] ?? $raw['invoice_no'] ?? $raw['reference_no'] ?? ('INV-' . $invoice->id),
            'is_free' => false,
            'status' => $status,
            'payment_status' => $status,
            'total_amount' => (float) ($invoice->total_amount ?? $invoice->amount ?? 0),
            'currency' => $currency,
            'user_id' => (string) $invoice->user_id,
            'customer_name' => $user?->fullname ?? trim((string) (($user?->firstname ?? '') . ' ' . ($user?->lastname ?? ''))),
            'customer_email' => $user?->email,
            'booking_reference' => $booking?->reference_no ?? $booking?->booking_number ?? null,
            'booking_type' => $booking ? class_basename($booking) : null,
            'items_count' => $invoice->items?->count() ?? 0,
            'user' => $user ? [
                'first_name' => $user->firstname,
                'last_name' => $user->lastname ?: '',
                'email' => $user->email,
                'phone' => $user->mobile,
                'username' => $user->username,
            ] : null,
            'created_at' => optional($invoice->created_at)->toISOString(),
        ];

        if ($detail) {
            $payload['booking'] = $booking ? $booking->toArray() : null;
            $payload['items'] = ($invoice->items ?? collect())->map(function ($item): array {
                $rawItem = $item->toArray();

                return [
                    'id' => $item->id,
                    'description' => $rawItem['description'] ?? $rawItem['title'] ?? $rawItem['name'] ?? null,
                    'description_en' => $rawItem['description_en'] ?? $rawItem['title_en'] ?? $rawItem['name_en'] ?? null,
                    'description_ar' => $rawItem['description_ar'] ?? $rawItem['title_ar'] ?? $rawItem['name_ar'] ?? null,
                    'quantity' => (float) ($rawItem['quantity'] ?? 1),
                    'unit_price' => (float) ($rawItem['unit_price'] ?? $rawItem['price'] ?? 0),
                    'total_price' => (float) ($rawItem['total_price'] ?? $rawItem['amount'] ?? 0),
                    'currency' => $rawItem['currency'] ?? $currency,
                    'raw' => $rawItem,
                ];
            })->values()->all();
            $payload['order_details'] = $raw;
        }

        return $payload;
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

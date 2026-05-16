<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        $orders = Invoice::query()
            ->with('items')
            ->where('user_id', $user->id)
            ->orderByDesc('id')
            ->get()
            ->map(function (Invoice $invoice) use ($user): array {
                return $this->formatInvoice($invoice, $user);
            })
            ->values();

        return response()->json($orders);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $invoice = Invoice::query()
            ->with('items')
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return response()->json($this->formatInvoice($invoice, $user));
    }

    private function formatInvoice(Invoice $invoice, $user): array
    {
        $status = $this->mapInvoiceStatus((int) ($invoice->status ?? 0));
        $currency = strtoupper((string) ($invoice->currency ?? $user->currency ?? 'EGP')) ?: 'EGP';

        return [
            'id' => $invoice->id,
            'order_number' => $invoice->invoice_number,
            'invoice_number' => $invoice->invoice_number,
            'title' => 'Invoice',
            'status' => $status,
            'payment_status' => $status,
            'total_amount' => (float) ($invoice->total_amount ?? 0),
            'currency' => $currency,
            'created_at' => $this->toIsoString($invoice->created_at),
            'items' => $invoice->items->map(function (InvoiceItem $item): array {
                $description = $item->item_name ?: 'Item';

                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'description' => $description,
                    'description_en' => $description,
                    'description_ar' => $description,
                    'quantity' => (int) ($item->qty ?? 1),
                    'unit_price' => (float) ($item->unit_price ?? 0),
                    'total_price' => (float) ($item->total ?? 0),
                    'check_in' => optional($item->check_in)->toDateString(),
                    'check_out' => optional($item->check_out)->toDateString(),
                ];
            })->values(),
        ];
    }

    private function mapInvoiceStatus(int $status): string
    {
        return match ($status) {
            1 => 'paid',
            2 => 'partially_paid',
            3 => 'cancelled',
            default => 'pending',
        };
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

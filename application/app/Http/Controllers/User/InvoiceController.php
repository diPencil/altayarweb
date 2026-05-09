<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('My Invoices');
        $invoices = Invoice::where('user_id', auth()->id());
        
        if ($request->search) {
            $search = $request->search;
            $invoices = $invoices->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%$search%")
                  ->orWhereHas('items', function ($item) use ($search) {
                      $item->where('item_name', 'LIKE', "%$search%");
                  });
            });
        }

        $invoices = $invoices->latest()->paginate(getPaginate());
        return view($this->activeTemplate . 'user.invoice.list', compact('pageTitle', 'invoices'));
    }

    public function show($invoice_number)
    {
        $invoice = Invoice::where('user_id', auth()->id())->where('invoice_number', $invoice_number)->with(['user', 'items'])->firstOrFail();
        $pageTitle = __('Invoice') . ' - ' . $invoice->invoice_number;

        // Prepare items for the invoice view
        $items = [];
        
        if ($invoice->items->count() > 0) {
            foreach ($invoice->items as $item) {
                $items[] = [
                    'name' => $item->item_name,
                    'description' => 'Guest Count: ' . ($item->guests ?? 0),
                    'dates' => ($item->check_in ? showDateTime($item->check_in, 'd M, Y') : '') . 
                               ($item->check_out ? ' to ' . showDateTime($item->check_out, 'd M, Y') : ''),
                    'quantity' => $item->qty,
                    'price' => $item->unit_price,
                    'total' => $item->total
                ];
            }
        } else {
            // Fallback for old invoices from previous systems
            $items[] = [
                'name' => 'Service/Booking',
                'description' => 'General service entry.',
                'dates' => showDateTime($invoice->issue_date, 'd M, Y'),
                'quantity' => 1,
                'price' => $invoice->total_amount,
                'total' => $invoice->total_amount
            ];
        }

        return view($this->activeTemplate . 'invoice.show', compact('pageTitle', 'invoice', 'items'));
    }
}

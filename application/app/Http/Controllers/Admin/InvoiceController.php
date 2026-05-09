<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Invoices');
        $invoices = Invoice::with(['user']);

        if ($request->search) {
            $search = $request->search;
            $invoices = $invoices->where(function ($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%$search%")
                  ->orWhereHas('user', function ($user) use ($search) {
                      $user->where('username', 'LIKE', "%$search%")
                           ->orWhere('firstname', 'LIKE', "%$search%")
                           ->orWhere('lastname', 'LIKE', "%$search%");
                  });
            });
        }

        $invoices = $invoices->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.invoice.index', compact('pageTitle', 'invoices'));
    }

    public function create()
    {
        $pageTitle = __('Create New Invoice');
        $users = User::active()->orderBy('id', 'desc')->get();
        return view('admin.invoice.create', compact('pageTitle', 'users'));
    }

    public function getBookings(Request $request)
    {
        $userId = $request->user_id;
        
        // Fetch Tour Bookings
        $tourBookings = TourBooking::where('user_id', $userId)
            ->with('tour_package')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'tour',
                    'reference' => $booking->reference_no,
                    'title' => $booking->tour_package ? $booking->tour_package->name : __('Tour Booking'),
                    'price' => $booking->price,
                    'guests' => ($booking->adult ?? 0) + ($booking->child ?? 0)
                ];
            });

        // Fetch Service Bookings
        $serviceBookings = ServiceBooking::where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function($booking) {
                return [
                    'id' => $booking->id,
                    'type' => 'service',
                    'reference' => $booking->reference_no,
                    'title' => $booking->title,
                    'price' => $booking->amount,
                    'guests' => 0,
                    'check_in' => $booking->service_date ? $booking->service_date->format('Y-m-d') : null,
                    'check_out' => $booking->service_end_date ? $booking->service_end_date->format('Y-m-d') : null,
                ];
            });

        return response()->json([
            'bookings' => $tourBookings->concat($serviceBookings)
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.guests' => 'nullable|integer|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $invoice = new Invoice();
        $invoice->invoice_number = 'INV-' . strtoupper(Str::random(8));
        $invoice->user_id = $request->user_id;
        $invoice->booking_id = 0; 
        $invoice->booking_type = ''; // Use empty string instead of null if column is not nullable
        
        // Initial total calculation
        $totalAmount = 0;
        foreach($request->items as $item) {
            $totalAmount += $item['unit_price'] * $item['qty'];
        }

        $invoice->subtotal = $totalAmount;
        $invoice->total_amount = $totalAmount;
        $invoice->paid_amount = $request->paid_amount ?? 0;
        $invoice->status = ($invoice->paid_amount >= $invoice->total_amount) ? 1 : 0;
        $invoice->issue_date = $request->issue_date;
        $invoice->notes = $request->notes;
        $invoice->save();

        // Save individual items
        foreach($request->items as $itemData) {
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->item_name = $itemData['name'];
            $item->check_in = $itemData['check_in'] ?? null;
            $item->check_out = $itemData['check_out'] ?? null;
            $item->qty = $itemData['qty'];
            $item->guests = $itemData['guests'] ?? 0;
            $item->unit_price = $itemData['unit_price'];
            $item->total = $itemData['unit_price'] * $itemData['qty'];
            $item->save();
        }

        $notify[] = ['success', __('Multi-item invoice created successfully')];
        return to_route('admin.invoice.index')->withNotify($notify);
    }

    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $pageTitle = __('Edit Invoice') . ': ' . $invoice->invoice_number;
        $users = User::active()->orderBy('id', 'desc')->get();
        return view('admin.invoice.edit', compact('pageTitle', 'invoice', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.guests' => 'nullable|integer|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->user_id = $request->user_id;
        $invoice->issue_date = $request->issue_date;
        $invoice->notes = $request->notes;
        $invoice->paid_amount = $request->paid_amount ?? 0;

        // Recalculate total
        $totalAmount = 0;
        foreach($request->items as $item) {
            $totalAmount += $item['unit_price'] * $item['qty'];
        }
        $invoice->subtotal = $totalAmount;
        $invoice->total_amount = $totalAmount;
        $invoice->status = ($invoice->paid_amount >= $invoice->total_amount) ? 1 : 0;
        $invoice->save();

        // Delete old items and save new ones
        $invoice->items()->delete();
        foreach($request->items as $itemData) {
            $item = new InvoiceItem();
            $item->invoice_id = $invoice->id;
            $item->item_name = $itemData['name'];
            $item->check_in = $itemData['check_in'] ?? null;
            $item->check_out = $itemData['check_out'] ?? null;
            $item->qty = $itemData['qty'];
            $item->guests = $itemData['guests'] ?? 0;
            $item->unit_price = $itemData['unit_price'];
            $item->total = $itemData['unit_price'] * $itemData['qty'];
            $item->save();
        }

        $notify[] = ['success', __('Invoice updated successfully')];
        return to_route('admin.invoice.index')->withNotify($notify);
    }

    public function statusUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:invoices,id',
            'status' => 'required|in:0,1,2',
            'paid_amount' => 'nullable|numeric|min:0'
        ]);

        $invoice = Invoice::findOrFail($request->id);
        $invoice->status = $request->status;
        if($request->has('paid_amount')){
            $invoice->paid_amount = $request->paid_amount;
        }
        $invoice->save();

        $notify[] = ['success', __('Invoice status updated successfully')];
        return back()->withNotify($notify);
    }

    public function detail($id)
    {
        $invoice = Invoice::with(['user', 'booking', 'items'])->findOrFail($id);
        $pageTitle = __('Invoice Details') . ' - ' . $invoice->invoice_number;

        // Prepare items for the invoice view
        $items = [];
        if ($invoice->items->count() > 0) {
            foreach ($invoice->items as $item) {
                $items[] = [
                    'name' => $item->item_name,
                    'description' => $item->guests ? $item->guests . ' ' . __('Guests') : '',
                    'dates' => ($item->check_in ? showDateTime($item->check_in, 'd M, Y') : '') . 
                               ($item->check_out ? ' - ' . showDateTime($item->check_out, 'd M, Y') : ''),
                    'quantity' => $item->qty,
                    'price' => $item->unit_price,
                    'total' => $item->total
                ];
            }
        } elseif ($invoice->booking_type == 'App\Models\TourBooking') {
            $items[] = [
                'name' => 'Tour Package: ' . ($invoice->booking->tourPackage->name ?? 'N/A'),
                'description' => 'Tour exploration and travel services.',
                'dates' => showDateTime($invoice->booking->user_proposal_date, 'd M, Y'),
                'quantity' => $invoice->booking->seat ?? 1,
                'price' => $invoice->booking->price ?? $invoice->total_amount,
                'total' => $invoice->total_amount
            ];
        } elseif ($invoice->booking_type == 'custom') {
            $items[] = [
                'name' => $invoice->custom_item_name ?? 'Custom Service',
                'description' => 'Manual service entry.',
                'dates' => ($invoice->custom_check_in ? showDateTime($invoice->custom_check_in, 'd M, Y') : showDateTime($invoice->issue_date, 'd M, Y')) . 
                           ($invoice->custom_check_out ? ' to ' . showDateTime($invoice->custom_check_out, 'd M, Y') : ''),
                'quantity' => $invoice->custom_qty ?? 1,
                'price' => ($invoice->total_amount / ($invoice->custom_qty ?? 1)),
                'total' => $invoice->total_amount
            ];
        } else {
            $items[] = [
                'name' => 'Service: ' . ($invoice->booking->title ?? 'N/A'),
                'description' => 'Service booking and accommodation.',
                'dates' => showDateTime($invoice->booking->service_date ?? $invoice->created_at, 'd M, Y') . ' to ' . showDateTime($invoice->booking->service_end_date ?? $invoice->booking->service_date ?? $invoice->created_at, 'd M, Y'),
                'quantity' => 1,
                'price' => $invoice->total_amount,
                'total' => $invoice->total_amount
            ];
        }

        return view($this->activeTemplate . 'invoice.show', compact('pageTitle', 'invoice', 'items'));
    }
}

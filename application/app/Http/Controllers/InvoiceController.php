<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show($invoiceNumber)
    {
        $invoice = Invoice::where('invoice_number', $invoiceNumber)->with(['user', 'items'])->firstOrFail();
        $pageTitle = 'Invoice - ' . $invoice->invoice_number;
        
        $items = [];

        if ($invoice->items->count() > 0) {
            foreach ($invoice->items as $item) {
                $items[] = [
                    'name' => $item->item_name,
                    'description' => 'Guests: ' . ($item->guests ?? 1),
                    'dates' => ($item->check_in ? showDateTime($item->check_in, 'd M, Y') : '') . 
                               ($item->check_out ? ' to ' . showDateTime($item->check_out, 'd M, Y') : ''),
                    'quantity' => $item->qty,
                    'price' => $item->unit_price,
                    'total' => $item->total
                ];
            }
        } elseif ($invoice->booking_type && $invoice->booking) {
            // Fallback for old legacy bookings if they still exist
            $booking = $invoice->booking;
            if ($invoice->booking_type == TourBooking::class) {
                $items[] = [
                    'name' => $booking->tourPackage->title ?? 'Tour Package',
                    'description' => 'Booking Reference: ' . $booking->reference_no,
                    'dates' => $booking->user_proposal_date ?? 'N/A',
                    'quantity' => $booking->seat ?? 1,
                    'price' => $booking->price,
                    'total' => $booking->price * ($booking->seat ?? 1)
                ];
            } elseif ($invoice->booking_type == ServiceBooking::class) {
                 $items[] = [
                    'name' => $booking->title ?? 'Service Booking',
                    'description' => 'Type: ' . $booking->booking_type,
                    'dates' => ($booking->service_date ? $booking->service_date->format('Y-m-d') : 'N/A') . ($booking->service_end_date ? ' to ' . $booking->service_end_date->format('Y-m-d') : ''),
                    'quantity' => 1,
                    'price' => $booking->amount,
                    'total' => $booking->amount
                ];
            }
        }

        return view('presets.default.invoice.show', compact('invoice', 'pageTitle', 'items'));
    }

    public function download($invoiceNumber)
    {
        // For simplicity, using browser print as "download" for now, or could integrate Barryvdh\DomPDF
        return $this->show($invoiceNumber);
    }
}

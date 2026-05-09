<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Invoice;
use App\Models\User;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use Carbon\Carbon;

class GenerateTestInvoice extends Command
{
    protected $signature = 'app:generate-test-invoice {booking_type} {booking_id}';
    protected $description = 'Generate a test invoice for a specific booking';

    public function handle()
    {
        $type = $this->argument('booking_type');
        $id = $this->argument('booking_id');

        if ($type == 'tour') {
            $booking = TourBooking::find($id);
            $bookingClass = TourBooking::class;
        } else {
            $booking = ServiceBooking::find($id);
            $bookingClass = ServiceBooking::class;
        }

        if (!$booking) {
            $this->error('Booking not found!');
            return;
        }

        $invoice = new Invoice();
        $invoice->invoice_number = 'INV-' . strtoupper(str_random(8));
        $invoice->user_id = $booking->user_id;
        $invoice->booking_id = $booking->id;
        $invoice->booking_type = $bookingClass;
        $invoice->subtotal = ($type == 'tour') ? ($booking->price * ($booking->seat ?? 1)) : $booking->amount;
        $invoice->tax = 0;
        $invoice->discount = $booking->discount ?? 0;
        $invoice->total_amount = $invoice->subtotal - $invoice->discount;
        $invoice->paid_amount = ($booking->status == 1 || $booking->status == 2) ? $invoice->total_amount : 0;
        $invoice->status = ($invoice->paid_amount >= $invoice->total_amount) ? 1 : 0;
        $invoice->issue_date = Carbon::now();
        $invoice->notes = 'Test invoice generated via console.';
        $invoice->save();

        $this->info('Invoice generated: ' . $invoice->invoice_number);
        $this->info('URL: ' . route('invoice.show', $invoice->invoice_number));
    }
}

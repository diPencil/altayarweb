<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportLegacyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:import-legacy 
                            {--file=storage/app/imports/legacy_booking_invoices.csv : The path to the CSV file} 
                            {--dry-run : Whether to run in dry-run mode}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import legacy booking invoices from WordPress CSV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->option('file');
        $dryRun = $this->option('dry-run');
        $unmatchedFile = storage_path('app/imports/legacy_booking_invoices_unmatched.csv');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $this->info($dryRun ? "Starting Dry-Run Invoice Import..." : "Starting Real Invoice Import...");

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        // Clean headers: remove BOM and trim
        $headers = array_map(function($header) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $header));
        }, $headers);
        
        $stats = [
            'total_rows' => 0,
            'invoices_to_create' => 0,
            'items_to_create' => 0,
            'users_matched' => 0,
            'users_not_found' => 0,
            'skipped' => 0,
            'failed' => 0,
            'total_amount' => 0,
            'total_paid' => 0,
        ];

        $unmatchedRows = [];
        $unmatchedHeaders = ['legacy_user_id', 'customer_email', 'customer_name', 'legacy_order_id', 'legacy_order_item_id', 'total_amount', 'paid_amount', 'issue_date', 'notes'];

        $usersByLegacyId = User::whereNotNull('legacy_user_id')->pluck('id', 'legacy_user_id')->toArray();
        $usersByEmail = User::pluck('id', 'email')->toArray();

        while (($row = fgetcsv($handle)) !== false) {
            $stats['total_rows']++;
            $data = array_combine($headers, $row);

            $legacyInvoiceId = trim($data['legacy_invoice_id']);
            $email = trim($data['customer_email']);
            $legacyUserId = trim($data['legacy_user_id']);

            // Idempotency: Check if already imported
            if (Invoice::where('legacy_invoice_id', $legacyInvoiceId)->exists()) {
                $stats['skipped']++;
                continue;
            }

            // User matching
            $userId = $usersByLegacyId[$legacyUserId] ?? $usersByEmail[$email] ?? null;

            if (!$userId) {
                $stats['users_not_found']++;
                $unmatchedRows[] = [
                    'legacy_user_id' => $legacyUserId,
                    'customer_email' => $email,
                    'customer_name' => $data['customer_name'],
                    'legacy_order_id' => $data['legacy_order_id'],
                    'legacy_order_item_id' => $data['legacy_order_item_id'],
                    'total_amount' => $data['total_amount'],
                    'paid_amount' => $data['paid_amount'],
                    'issue_date' => $data['issue_date'],
                    'notes' => $data['notes'],
                ];
                $stats['skipped']++;
                continue;
            }

            $stats['users_matched']++;
            $stats['invoices_to_create']++;
            $stats['items_to_create']++;
            $stats['total_amount'] += (float)$data['total_amount'];
            $stats['total_paid'] += (float)$data['paid_amount'];

            if (!$dryRun) {
                try {
                    DB::beginTransaction();

                    $invoice = new Invoice();
                    $invoice->invoice_number = 'LEGACY-' . $data['legacy_invoice_number'];
                    
                    if (Invoice::where('invoice_number', $invoice->invoice_number)->exists()) {
                        $invoice->invoice_number .= '-' . $legacyInvoiceId;
                    }

                    $invoice->user_id = $userId;
                    $invoice->booking_id = 0;
                    $invoice->booking_type = '';
                    $invoice->subtotal = (float)$data['total_amount'];
                    $invoice->total_amount = (float)$data['total_amount'];
                    $invoice->paid_amount = (float)$data['paid_amount'];
                    
                    $status = 0; // pending
                    if ($data['payment_status'] === 'payment_received' || $data['payment_status'] === 'completed') {
                        $status = 1; // paid
                    }
                    if ((float)$data['paid_amount'] > 0 && (float)$data['paid_amount'] < (float)$data['total_amount']) {
                        $status = 2; // partially_paid
                    }
                    if ($data['invoice_status'] === 'trash' || $data['invoice_status'] === 'cancelled') {
                        $status = 3; // cancelled
                    }
                    $invoice->status = $status;

                    $invoice->issue_date = $data['issue_date'] ? date('Y-m-d', strtotime($data['issue_date'])) : now()->toDateString();
                    $invoice->notes = $data['notes'];
                    
                    $invoice->legacy_invoice_id = $legacyInvoiceId;
                    $invoice->legacy_order_id = trim($data['legacy_order_id']);
                    $invoice->legacy_order_item_id = trim($data['legacy_order_item_id']);
                    $invoice->legacy_booking_obj_id = trim($data['legacy_booking_obj_id']);
                    $invoice->legacy_import = true;
                    $invoice->payment_method = $data['payment_method'];
                    $invoice->currency = $data['currency'];
                    
                    $invoice->save();

                    $item = new InvoiceItem();
                    $item->invoice_id = $invoice->id;
                    $item->item_name = $data['item_name_service'];
                    $item->check_in = !empty($data['check_in']) ? date('Y-m-d', strtotime($data['check_in'])) : null;
                    $item->check_out = !empty($data['check_out']) ? date('Y-m-d', strtotime($data['check_out'])) : null;
                    $item->qty = (int)$data['qty'] ?: 1;
                    $item->guests = (int)$data['guests'];
                    $item->unit_price = (float)$data['item_price'];
                    $item->total = (float)$data['item_price'] * $item->qty;
                    $item->save();

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $stats['failed']++;
                    $this->error("Failed to import invoice {$legacyInvoiceId}: " . $e->getMessage());
                    Log::error("Invoice import error: " . $e->getMessage());
                }
            }
        }

        fclose($handle);

        if (!empty($unmatchedRows)) {
            $unmatchedHandle = fopen($unmatchedFile, 'w');
            fputcsv($unmatchedHandle, $unmatchedHeaders);
            foreach ($unmatchedRows as $unmatchedRow) {
                fputcsv($unmatchedHandle, $unmatchedRow);
            }
            fclose($unmatchedHandle);
            $this->warn("\nGenerated unmatched users report: storage/app/imports/legacy_booking_invoices_unmatched.csv");
        }

        $this->info("\n--- Invoice Import Summary ---");
        $this->line("Total CSV Rows:        {$stats['total_rows']}");
        $this->line("Invoices to Create:    {$stats['invoices_to_create']}");
        $this->line("Items to Create:       {$stats['items_to_create']}");
        $this->line("Users Matched:         {$stats['users_matched']}");
        $this->line("Users Not Found:       {$stats['users_not_found']}");
        $this->line("Skipped/Existing:      {$stats['skipped']}");
        $this->line("Failed Rows:           {$stats['failed']}");
        $this->info("\n--- Financials ---");
        $this->line("Total Invoice Amount:  " . number_format($stats['total_amount'], 2));
        $this->line("Total Paid Amount:     " . number_format($stats['total_paid'], 2));

        if ($dryRun) {
            $this->warn("\nDry-run complete. No changes were made.");
        } else {
            $this->info("\nImport complete.");
        }
    }
}

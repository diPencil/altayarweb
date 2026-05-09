<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ServiceBooking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportLegacyBookings extends Command
{
    protected $signature = 'bookings:import-legacy {--dry-run}';
    protected $description = 'Import legacy bookings from CSV into service_bookings table';

    public function handle()
    {
        $file = storage_path('app/imports/legacy_bookings_for_import.csv');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return;
        }

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->info("=== DRY RUN MODE ===");
        }

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);
        
        // Remove BOM if present
        if ($headers && isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
        }

        $summary = [
            'total_rows' => 0,
            'users_matched' => 0,
            'users_not_found' => 0,
            'bookings_to_create' => 0,
            'skipped_existing' => 0,
            'failed' => 0,
            'total_amount' => 0,
            'total_paid' => 0,
            'types' => [],
            'statuses' => [],
        ];

        $unmatched = [];

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($headers, $row);
            $summary['total_rows']++;

            $legacyBookingId = trim($data['legacy_booking_id'] ?? '');
            $legacyOrderId = trim($data['legacy_order_id'] ?? '');
            $legacyOrderItemId = trim($data['legacy_order_item_id'] ?? '');
            
            $legacyUserId = trim($data['legacy_user_id'] ?? '');
            $email = trim($data['customer_email'] ?? '');

            // Match user
            $user = null;
            if (!empty($legacyUserId)) {
                $user = User::where('legacy_user_id', $legacyUserId)->first();
            }
            if (!$user && !empty($email)) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $summary['users_not_found']++;
                $unmatched[] = [
                    'legacy_booking_id' => $legacyBookingId,
                    'customer_email' => $email,
                    'customer_name' => $data['customer_name'] ?? '',
                    'booking_type' => $data['booking_type'] ?? '',
                    'amount' => $data['amount'] ?? 0,
                ];
                continue;
            }

            $summary['users_matched']++;

            // Check idempotency
            $exists = ServiceBooking::where(function($q) use ($legacyBookingId, $legacyOrderId, $legacyOrderItemId) {
                if (!empty($legacyBookingId)) {
                    $q->where('legacy_booking_id', $legacyBookingId);
                } elseif (!empty($legacyOrderId) && !empty($legacyOrderItemId)) {
                    $q->where('legacy_order_id', $legacyOrderId)
                      ->where('legacy_order_item_id', $legacyOrderItemId);
                }
            })->exists();

            if ($exists) {
                $summary['skipped_existing']++;
                continue;
            }

            $summary['bookings_to_create']++;
            
            $amount = (float)($data['amount'] ?? 0);
            $paidAmount = (float)($data['paid_amount'] ?? 0);
            $summary['total_amount'] += $amount;
            $summary['total_paid'] += $paidAmount;

            $type = $this->mapType($data['booking_type'] ?? '');
            $status = $this->mapStatus($data['status'] ?? '');

            $summary['types'][$type] = ($summary['types'][$type] ?? 0) + 1;
            $summary['statuses'][$status] = ($summary['statuses'][$status] ?? 0) + 1;

            if (!$dryRun) {
                try {
                    ServiceBooking::create([
                        'user_id' => $user->id,
                        'created_by_admin_id' => null, // System import
                        'booking_type' => $type,
                        'title' => $data['title_service_name'] ?? 'Legacy Booking',
                        'reference_no' => $data['reference_no'] ?? null,
                        'booking_date' => $data['booking_date'] ?: null,
                        'service_date' => $data['service_date'] ?: null,
                        'service_end_date' => $data['end_date'] ?: null,
                        'amount' => $amount,
                        'status' => $status,
                        'notes' => $data['notes'] ?? null,
                        'legacy_booking_id' => $legacyBookingId,
                        'legacy_order_id' => $legacyOrderId,
                        'legacy_order_item_id' => $legacyOrderItemId,
                        'legacy_booking_obj_id' => $data['legacy_booking_obj_id'] ?? null,
                        'paid_amount' => $paidAmount,
                        'qty' => (int)($data['qty'] ?? 1),
                        'guests' => (int)($data['guests'] ?? 1),
                        'old_payment_status' => $data['old_payment_status'] ?? null,
                        'old_order_status' => $data['old_order_status'] ?? null,
                        'legacy_import' => true,
                        'legacy_source' => $data['legacy_source'] ?? 'WordPress BABE',
                    ]);
                } catch (\Exception $e) {
                    $this->error("Failed to import booking {$legacyBookingId}: " . $e->getMessage());
                    $summary['failed']++;
                }
            }
        }

        fclose($handle);

        $this->info("Import Summary:");
        $this->line("- Total Rows: " . $summary['total_rows']);
        $this->line("- Users Matched: " . $summary['users_matched']);
        $this->line("- Users Not Found: " . $summary['users_not_found']);
        $this->line("- Bookings to Create: " . $summary['bookings_to_create']);
        $this->line("- Skipped Existing: " . $summary['skipped_existing']);
        $this->line("- Failed Rows: " . $summary['failed']);
        $this->line("- Total Amount: " . number_format($summary['total_amount'], 2));
        $this->line("- Total Paid Amount: " . number_format($summary['total_paid'], 2));

        $this->info("\nCount by Booking Type:");
        foreach ($summary['types'] as $type => $count) {
            $this->line("  - {$type}: {$count}");
        }

        $this->info("\nCount by Status:");
        foreach ($summary['statuses'] as $status => $count) {
            $this->line("  - Status {$status}: {$count}");
        }

        if (!empty($unmatched)) {
            $unmatchedFile = storage_path('app/imports/legacy_bookings_unmatched.csv');
            $unmatchedHandle = fopen($unmatchedFile, 'w');
            fputcsv($unmatchedHandle, array_keys($unmatched[0]));
            foreach ($unmatched as $u) {
                fputcsv($unmatchedHandle, $u);
            }
            fclose($unmatchedHandle);
            $this->warn("\nUnmatched bookings logged to: $unmatchedFile");
        }
    }

    private function mapType($type)
    {
        $map = [
            'Trip / Tour' => 'tour',
            'Flight' => 'flight',
            'Stay / Accommodation' => 'stay',
            'Hotel' => 'hotel',
            'Transport / Transfer' => 'transportation',
            'Discount Coupon' => 'coupon',
            'Restaurant' => 'restaurant',
            'Cafe' => 'cafe',
        ];
        return $map[$type] ?? 'tour';
    }

    private function mapStatus($status)
    {
        $map = [
            'Pending' => 0,
            'Approved' => 1,
            'Confirmed' => 1,
            'Completed' => 2,
            'Canceled' => 3,
            'Cancelled' => 3,
            'Rejected' => 3,
        ];
        return $map[$status] ?? 0;
    }
}

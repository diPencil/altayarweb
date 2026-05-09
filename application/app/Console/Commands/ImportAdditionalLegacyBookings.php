<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAdditionalLegacyBookings extends Command
{
    protected $signature = 'bookings:import-additional-legacy {--dry-run}';

    protected $description = 'Import additional legacy service bookings from the employee Excel CSV';

    public function handle(): int
    {
        $file = storage_path('app/imports/additional_legacy_bookings_for_import.csv');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $unmatchedFile = storage_path('app/imports/additional_legacy_bookings_unmatched.csv');

        $handle = fopen($file, 'r');
        if ($handle === false) {
            $this->error("Unable to open file: {$file}");

            return self::FAILURE;
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers) || empty($headers)) {
            fclose($handle);
            $this->error('CSV header row is missing or unreadable.');

            return self::FAILURE;
        }

        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
        $headers = array_map(static fn ($header) => trim((string) $header), $headers);

        $summary = [
            'total_rows' => 0,
            'users_matched' => 0,
            'users_not_found' => 0,
            'bookings_to_create' => 0,
            'skipped_existing' => 0,
            'failed_rows' => 0,
            'total_amount' => 0.0,
            'total_paid_amount' => 0.0,
            'total_legacy_benefit_value' => 0.0,
            'by_type' => [],
            'rows_with_review_flags' => 0,
        ];

        $unmatchedRows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $summary['total_rows']++;

            if (count($row) !== count($headers)) {
                $summary['failed_rows']++;
                continue;
            }

            $data = array_combine($headers, $row);

            if (! is_array($data)) {
                $summary['failed_rows']++;
                continue;
            }

            $legacyBookingId = trim((string) ($data['legacy_booking_id'] ?? ''));
            $referenceNo = trim((string) ($data['reference_no'] ?? ''));
            $username = trim((string) ($data['customer_username'] ?? ''));

            $user = $username !== '' ? User::where('username', $username)->first() : null;

            if (! $user) {
                $summary['users_not_found']++;
                $unmatchedRows[] = $this->buildUnmatchedRow($data, 'user_not_found');
                continue;
            }

            $summary['users_matched']++;

            if ($this->bookingExists($legacyBookingId, $referenceNo)) {
                $summary['skipped_existing']++;
                continue;
            }

            $bookingType = $this->mapBookingType((string) ($data['booking_type'] ?? ''));
            $status = $this->mapStatus((string) ($data['status'] ?? ''));
            $amount = $this->parseMoney($data['amount'] ?? null);
            $paidAmount = $this->parseMoney($data['paid_amount'] ?? null);
            $legacyBenefitValue = $this->parseMoney($data['legacy_benefit_value'] ?? null);
            $rawTotalAmount = $this->parseRawTotalAmount($data['raw_total_amount'] ?? null);
            $reviewFlags = trim((string) ($data['review_flags'] ?? ''));

            $summary['bookings_to_create']++;
            $summary['total_amount'] += $amount;
            $summary['total_paid_amount'] += $paidAmount;
            $summary['total_legacy_benefit_value'] += $legacyBenefitValue;
            $summary['by_type'][$bookingType] = ($summary['by_type'][$bookingType] ?? 0) + 1;

            if ($reviewFlags !== '') {
                $summary['rows_with_review_flags']++;
            }

            if ($dryRun) {
                continue;
            }

            try {
                DB::transaction(function () use ($user, $data, $bookingType, $status, $amount, $paidAmount, $legacyBenefitValue, $rawTotalAmount, $reviewFlags) {
                    ServiceBooking::create([
                        'user_id' => $user->id,
                        'created_by_admin_id' => null,
                        'booking_type' => $bookingType,
                        'title' => trim((string) ($data['title_service_name'] ?? 'Legacy Booking')),
                        'reference_no' => $data['reference_no'] ?? null,
                        'booking_date' => $this->parseDate($data['booking_date'] ?? null),
                        'service_date' => $this->parseDate($data['service_date'] ?? null),
                        'service_end_date' => $this->parseDate($data['end_date'] ?? null),
                        'amount' => $amount,
                        'status' => $status,
                        'notes' => $data['notes'] ?? null,
                        'legacy_booking_id' => trim((string) ($data['legacy_booking_id'] ?? '')) ?: null,
                        'legacy_order_id' => trim((string) ($data['legacy_order_id'] ?? '')) ?: null,
                        'legacy_order_item_id' => trim((string) ($data['legacy_order_item_id'] ?? '')) ?: null,
                        'legacy_booking_obj_id' => trim((string) ($data['legacy_booking_obj_id'] ?? '')) ?: null,
                        'paid_amount' => $paidAmount,
                        'qty' => max(1, (int) ($data['qty'] ?? 1)),
                        'guests' => max(1, (int) ($data['guests'] ?? 1)),
                        'old_payment_status' => trim((string) ($data['old_payment_status'] ?? '')) ?: null,
                        'old_order_status' => trim((string) ($data['old_order_status'] ?? '')) ?: null,
                        'raw_total_amount' => $rawTotalAmount,
                        'legacy_benefit_value' => $legacyBenefitValue,
                        'review_flags' => $reviewFlags ?: null,
                        'source_excel_row' => isset($data['source_excel_row']) && $data['source_excel_row'] !== '' ? (int) $data['source_excel_row'] : null,
                        'legacy_import' => true,
                        'legacy_source' => 'additional_employee_excel_bookings',
                    ]);
                });
            } catch (\Throwable $throwable) {
                $summary['failed_rows']++;
                $this->error("Failed to import legacy booking {$legacyBookingId}: {$throwable->getMessage()}");
            }
        }

        fclose($handle);

        $this->writeUnmatchedReport($unmatchedFile, $unmatchedRows);

        $this->line('Import Summary:');
        $this->line('- Total CSV rows: ' . $summary['total_rows']);
        $this->line('- Users matched: ' . $summary['users_matched']);
        $this->line('- Users not found: ' . $summary['users_not_found']);
        $this->line('- Bookings to create: ' . $summary['bookings_to_create']);
        $this->line('- Skipped/existing: ' . $summary['skipped_existing']);
        $this->line('- Failed rows: ' . $summary['failed_rows']);
        $this->line('- Total amount: ' . number_format($summary['total_amount'], 2));
        $this->line('- Total paid amount: ' . number_format($summary['total_paid_amount'], 2));
        $this->line('- Total legacy_benefit_value: ' . number_format($summary['total_legacy_benefit_value'], 2));
        $this->line('- Rows with review_flags: ' . $summary['rows_with_review_flags']);

        $this->line('Count by booking_type:');
        foreach ($summary['by_type'] as $type => $count) {
            $this->line("- {$type}: {$count}");
        }

        $this->line('Unmatched report path: ' . $unmatchedFile);

        if ($dryRun) {
            $this->warn('Dry run complete. No database changes were made.');
        } else {
            $this->info('Import complete.');
        }

        return self::SUCCESS;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if (trim((string) $value) !== '') {
                return false;
            }
        }

        return true;
    }

    private function bookingExists(string $legacyBookingId, string $referenceNo): bool
    {
        return ServiceBooking::query()
            ->where(function ($query) use ($legacyBookingId, $referenceNo) {
                if ($legacyBookingId !== '') {
                    $query->where('legacy_booking_id', $legacyBookingId);
                }

                if ($referenceNo !== '') {
                    $method = $legacyBookingId !== '' ? 'orWhere' : 'where';
                    $query->{$method}('reference_no', $referenceNo);
                }
            })
            ->exists();
    }

    private function mapBookingType(string $type): string
    {
        $normalized = trim(Str::lower($type));

        return match ($normalized) {
            'trip / tour', 'trip', 'tour' => 'tour',
            'flight' => 'flight',
            'stay / accommodation', 'stay', 'accommodation', 'hotel' => 'stay',
            'transport / transfer', 'transport', 'transfer' => 'transportation',
            'discount coupon', 'coupon' => 'coupon',
            'restaurant' => 'restaurant',
            'cafe', 'coffee shop' => 'cafe',
            default => 'tour',
        };
    }

    private function mapStatus(string $status): int
    {
        $normalized = trim(Str::lower($status));

        return match ($normalized) {
            'approved', 'confirmed' => 1,
            'completed' => 2,
            'canceled', 'cancelled', 'rejected' => 3,
            default => 0,
        };
    }

    private function parseMoney(mixed $value): float
    {
        if ($value === null) {
            return 0.0;
        }

        $cleaned = preg_replace('/[^0-9.\-]/', '', (string) $value);

        return $cleaned === '' || $cleaned === '-' ? 0.0 : (float) $cleaned;
    }

    private function parseRawTotalAmount(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function parseDate(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : null;
    }

    private function buildUnmatchedRow(array $data, string $reason): array
    {
        return [
            'source_excel_row' => $data['source_excel_row'] ?? '',
            'legacy_booking_id' => $data['legacy_booking_id'] ?? '',
            'legacy_order_id' => $data['legacy_order_id'] ?? '',
            'legacy_order_item_id' => $data['legacy_order_item_id'] ?? '',
            'customer_username' => $data['customer_username'] ?? '',
            'reference_no' => $data['reference_no'] ?? '',
            'booking_type' => $data['booking_type'] ?? '',
            'title_service_name' => $data['title_service_name'] ?? '',
            'amount' => $data['amount'] ?? '',
            'paid_amount' => $data['paid_amount'] ?? '',
            'raw_total_amount' => $data['raw_total_amount'] ?? '',
            'legacy_benefit_value' => $data['legacy_benefit_value'] ?? '',
            'review_flags' => $data['review_flags'] ?? '',
            'notes' => $data['notes'] ?? '',
            'reason' => $reason,
        ];
    }

    private function writeUnmatchedReport(string $file, array $rows): void
    {
        $handle = fopen($file, 'w');

        if ($handle === false) {
            $this->warn('Unable to write unmatched report.');

            return;
        }

        $headers = [
            'source_excel_row',
            'legacy_booking_id',
            'legacy_order_id',
            'legacy_order_item_id',
            'customer_username',
            'reference_no',
            'booking_type',
            'title_service_name',
            'amount',
            'paid_amount',
            'raw_total_amount',
            'legacy_benefit_value',
            'review_flags',
            'notes',
            'reason',
        ];

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            fputcsv($handle, array_map(static fn ($header) => $row[$header] ?? '', $headers));
        }

        fclose($handle);
    }
}
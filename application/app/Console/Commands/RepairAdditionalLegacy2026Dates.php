<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RepairAdditionalLegacy2026Dates extends Command
{
    protected $signature = 'bookings:repair-additional-legacy-2026-dates {--dry-run}';

    protected $description = 'Repair additional legacy booking dates whose year was incorrectly stored as 2026';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('=== DRY RUN MODE ===');
        }

        $bookings = ServiceBooking::query()
            ->with('user')
            ->where('legacy_import', true)
            ->where('legacy_source', 'additional_employee_excel_bookings')
            ->orderBy('id')
            ->get();

        $stats = [
            'total_found' => $bookings->count(),
            'rows_with_jan_jun_2026' => 0,
            'rows_with_jul_dec_2026' => 0,
            'rows_before_exclusion' => 0,
            'rows_excluded_manual_review' => 0,
            'fields_to_repair' => 0,
            'rows_repaired' => 0,
            'fields_repaired' => 0,
            'failed' => 0,
            'skipped' => 0,
            'samples' => [],
            'unchanged_samples' => [],
        ];

        foreach ($bookings as $booking) {
            $before = $this->captureDates($booking);
            $after = $before;
            $changedFields = [];
            $janJunFields = [];
            $isManualReviewExclusion = in_array((string) $booking->reference_no, ['LEGACY-XLS2-0211', 'LEGACY-XLS2-0212'], true);
            $rowHasJulDec2026 = false;

            foreach (['booking_date', 'service_date', 'service_end_date'] as $field) {
                if (! $before[$field] instanceof Carbon) {
                    continue;
                }

                $year = (int) $before[$field]->year;
                $month = (int) $before[$field]->month;

                if ($year === 2026 && $month <= 6) {
                    $janJunFields[] = $field;
                }

                if ($year === 2026 && $month >= 7) {
                    $rowHasJulDec2026 = true;

                    if ($isManualReviewExclusion) {
                        continue;
                    }

                    $stats['fields_to_repair']++;
                    $stats['fields_repaired']++;
                    $changedFields[] = $field;
                    $after[$field] = $before[$field]->copy()->year(2025);
                }
            }

            if ($rowHasJulDec2026) {
                $stats['rows_before_exclusion']++;
            }

            if ($isManualReviewExclusion) {
                $stats['rows_excluded_manual_review']++;
            }

            if (empty($changedFields)) {
                if (! empty($janJunFields)) {
                    $stats['rows_with_jan_jun_2026']++;

                    if (count($stats['unchanged_samples']) < 10) {
                        $stats['unchanged_samples'][] = $this->buildSample($booking, $before, $after, $janJunFields, 'kept_june_or_earlier_2026');
                    }
                }

                $stats['skipped']++;
                continue;
            }

            $stats['rows_with_jul_dec_2026']++;
            $stats['rows_repaired']++;

            if (count($stats['samples']) < 15) {
                $stats['samples'][] = $this->buildSample($booking, $before, $after, $changedFields, 'repair_jul_dec_2026');
            }

            if (! $dryRun) {
                try {
                    $booking->timestamps = false;
                    foreach ($changedFields as $field) {
                        $booking->{$field} = $after[$field]->toDateString();
                    }
                    $booking->save();
                } catch (\Throwable $throwable) {
                    $stats['failed']++;
                    $stats['rows_repaired']--;
                    foreach ($changedFields as $field) {
                        $stats['fields_repaired']--;
                    }
                    $this->error('Failed to repair ' . $booking->reference_no . ': ' . $throwable->getMessage());
                }
            }
        }

        $this->line('Repair Summary:');
        $this->line('- Total additional legacy bookings found: ' . $stats['total_found']);
        $this->line('- Candidate rows before exclusion: ' . $stats['rows_before_exclusion']);
        $this->line('- Excluded manual-review rows: ' . $stats['rows_excluded_manual_review']);
        $this->line('- Rows with 2026 dates from Jan-Jun that will be kept unchanged: ' . $stats['rows_with_jan_jun_2026']);
        $this->line('- Rows with 2026 dates from Jul-Dec that are candidates for repair: ' . $stats['rows_with_jul_dec_2026']);
        $this->line('- Total date fields to repair: ' . $stats['fields_to_repair']);
        $this->line('- Rows repaired: ' . $stats['rows_repaired']);
        $this->line('- Fields repaired: ' . $stats['fields_repaired']);
        $this->line('- Failed rows/errors: ' . $stats['failed']);
        $this->line('- Skipped rows: ' . $stats['skipped']);
        $this->line('June 2026 bookings and any Jan-Jun 2026 dates will NOT be changed.');
        $this->line('LEGACY-XLS2-0211 and LEGACY-XLS2-0212 are excluded from automatic repair and remain manual review.');
        $this->line('Only booking_date, service_date, and service_end_date with year 2026 and month 7-12 will be changed.');
        $this->line('No financial data or user data will be modified.');

        if (! empty($stats['samples'])) {
            $this->info('Sample rows (Jul-Dec 2026 candidates, before/after):');

            foreach ($stats['samples'] as $sample) {
                $this->line(sprintf(
                    '- %s | row %s | %s | before: booking_date=%s service_date=%s end_date=%s | after: booking_date=%s service_date=%s end_date=%s | changed: %s | reason: %s',
                    $sample['reference_no'],
                    $sample['source_excel_row'],
                    $sample['title'],
                    $sample['before_booking_date'],
                    $sample['before_service_date'],
                    $sample['before_end_date'],
                    $sample['after_booking_date'],
                    $sample['after_service_date'],
                    $sample['after_end_date'],
                    $sample['changed_fields'],
                    $sample['reason']
                ));
            }
        }

        if (! empty($stats['unchanged_samples'])) {
            $this->info('Sample rows kept unchanged (Jan-Jun 2026):');

            foreach ($stats['unchanged_samples'] as $sample) {
                $this->line(sprintf(
                    '- %s | row %s | %s | booking_date=%s | service_date=%s | end_date=%s | unchanged fields: %s | reason: %s',
                    $sample['reference_no'],
                    $sample['source_excel_row'],
                    $sample['title'],
                    $sample['before_booking_date'],
                    $sample['before_service_date'],
                    $sample['before_end_date'],
                    $sample['changed_fields'],
                    $sample['reason']
                ));
            }
        }

        if ($dryRun) {
            $this->warn('Dry run complete. No database changes were made.');
        } else {
            $this->info('Repair complete.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array{booking_date: ?Carbon, service_date: ?Carbon, service_end_date: ?Carbon}
     */
    private function captureDates(ServiceBooking $booking): array
    {
        return [
            'booking_date' => $this->toCarbon($booking->booking_date),
            'service_date' => $this->toCarbon($booking->service_date),
            'service_end_date' => $this->toCarbon($booking->service_end_date),
        ];
    }

    private function toCarbon(mixed $value): ?Carbon
    {
        return $value instanceof Carbon ? $value->copy() : null;
    }

    /**
     * @param array{booking_date: ?Carbon, service_date: ?Carbon, service_end_date: ?Carbon} $before
     * @param array{booking_date: ?Carbon, service_date: ?Carbon, service_end_date: ?Carbon} $after
     * @param array<int, string> $changedFields
     */
    private function buildSample(ServiceBooking $booking, array $before, array $after, array $changedFields, string $reason): array
    {
        return [
            'reference_no' => (string) $booking->reference_no,
            'source_excel_row' => (string) ($booking->source_excel_row ?? ''),
            'title' => (string) $booking->title,
            'before_booking_date' => $before['booking_date']?->toDateString() ?? '',
            'before_service_date' => $before['service_date']?->toDateString() ?? '',
            'before_end_date' => $before['service_end_date']?->toDateString() ?? '',
            'after_booking_date' => $after['booking_date']?->toDateString() ?? '',
            'after_service_date' => $after['service_date']?->toDateString() ?? '',
            'after_end_date' => $after['service_end_date']?->toDateString() ?? '',
            'changed_fields' => implode(', ', $changedFields),
            'reason' => $reason,
        ];
    }
}
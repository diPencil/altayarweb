<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RepairAdditionalLegacyBookingDates extends Command
{
    protected $signature = 'bookings:repair-additional-legacy-dates {--dry-run}';

    protected $description = 'Repair end dates for imported additional legacy service bookings with missing-year date parsing';

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
            'end_before_service' => 0,
            'year_defaulted' => 0,
            'safe_repair' => 0,
            'manual_review' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'samples' => [],
        ];

        foreach ($bookings as $booking) {
            $serviceDate = $booking->service_date instanceof Carbon ? $booking->service_date->copy() : null;
            $endDate = $booking->service_end_date instanceof Carbon ? $booking->service_end_date->copy() : null;
            $reviewFlags = $this->normalizeText($booking->review_flags);
            $notes = $this->normalizeText($booking->notes);

            $yearDefaulted = $this->containsMarker($reviewFlags, 'date_year_defaulted_to_2025')
                || $this->containsMarker($notes, 'parsed_default_year_2025');

            $bothDatesFromExcelSerials = preg_match('/parse methods:\s*excel_serial\/excel_serial/i', (string) $booking->notes) === 1;

            $endBeforeService = $serviceDate !== null
                && $endDate !== null
                && $endDate->lessThan($serviceDate);

            if ($endBeforeService) {
                $stats['end_before_service']++;
            }

            if ($yearDefaulted) {
                $stats['year_defaulted']++;
            }

            $shouldRepair = $endBeforeService && $yearDefaulted && ! $bothDatesFromExcelSerials && $serviceDate !== null && $endDate !== null;

            if ($shouldRepair) {
                $correctedEndDate = $endDate->copy()->year((int) $serviceDate->year);

                if (count($stats['samples']) < 10) {
                    $stats['samples'][] = $this->buildSample($booking, $serviceDate, $endDate, $correctedEndDate, 'safe_repair');
                }

                $stats['safe_repair']++;

                if (! $dryRun) {
                    try {
                        $booking->timestamps = false;
                        $booking->service_end_date = $correctedEndDate->toDateString();
                        $booking->save();
                        $stats['updated']++;
                    } catch (\Throwable $throwable) {
                        $stats['failed']++;
                        $this->error('Failed to repair ' . $booking->reference_no . ': ' . $throwable->getMessage());
                    }
                }

                continue;
            }

            if ($endBeforeService || $yearDefaulted) {
                $stats['manual_review']++;

                if (count($stats['samples']) < 10) {
                    $stats['samples'][] = $this->buildSample($booking, $serviceDate, $endDate, null, $bothDatesFromExcelSerials ? 'manual_review_excel_serial' : 'manual_review');
                }
            } else {
                $stats['skipped']++;
            }
        }

        $this->line('Repair Summary:');
        $this->line('- Total additional legacy bookings found: ' . $stats['total_found']);
        $this->line('- Bookings with end_date before service_date: ' . $stats['end_before_service']);
        $this->line('- Bookings with date_year_defaulted_to_2025: ' . $stats['year_defaulted']);
        $this->line('- Bookings that can be safely repaired: ' . $stats['safe_repair']);
        $this->line('- Bookings requiring manual review: ' . $stats['manual_review']);
        $this->line('- Updated rows: ' . $stats['updated']);
        $this->line('- Skipped rows: ' . $stats['skipped']);
        $this->line('- Failed rows: ' . $stats['failed']);

        $this->line('No amounts, status values, users, or financial data will be changed by this command.');

        if (! empty($stats['samples'])) {
            $this->info('Sample rows (before/after):');

            foreach ($stats['samples'] as $sample) {
                $afterText = $sample['after_end_date'] ?? 'manual review';
                $this->line(sprintf(
                    '- %s | Service Date: %s | End Date: %s | After: %s | Reason: %s',
                    $sample['reference_no'],
                    $sample['service_date'],
                    $sample['before_end_date'],
                    $afterText,
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

    private function normalizeText(mixed $value): string
    {
        return mb_strtolower(trim((string) $value));
    }

    private function containsMarker(string $haystack, string $needle): bool
    {
        return $haystack !== '' && str_contains($haystack, $needle);
    }

    private function buildSample(ServiceBooking $booking, ?Carbon $serviceDate, ?Carbon $endDate, ?Carbon $afterEndDate, string $reason): array
    {
        return [
            'reference_no' => (string) $booking->reference_no,
            'service_date' => $serviceDate?->toDateString() ?? 'missing',
            'before_end_date' => $endDate?->toDateString() ?? 'missing',
            'after_end_date' => $afterEndDate?->toDateString(),
            'reason' => $reason,
        ];
    }
}
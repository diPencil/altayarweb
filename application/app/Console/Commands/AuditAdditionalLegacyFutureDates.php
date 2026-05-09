<?php

namespace App\Console\Commands;

use App\Models\ServiceBooking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AuditAdditionalLegacyFutureDates extends Command
{
    protected $signature = 'bookings:audit-additional-legacy-future-dates {--dry-run}';

    protected $description = 'Audit future-dated additional legacy service bookings against the original CSV';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $file = storage_path('app/imports/additional_legacy_bookings_for_import.csv');
        $reportPath = storage_path('app/imports/additional_legacy_future_dates_review.csv');

        if (! file_exists($file)) {
            $this->error("File not found: {$file}");

            return self::FAILURE;
        }

        if ($dryRun) {
            $this->info('=== DRY RUN MODE ===');
        }

        $csvRows = $this->loadCsvRows($file);
        $bookings = ServiceBooking::query()
            ->where('legacy_import', true)
            ->where('legacy_source', 'additional_employee_excel_bookings')
            ->orderBy('id')
            ->get();

        $today = now()->startOfDay();

        $stats = [
            'total_found' => $bookings->count(),
            'future_booking_date' => 0,
            'future_service_date' => 0,
            'future_end_date' => 0,
            'date_year_defaulted' => 0,
            'affected_rows' => 0,
            'samples' => [],
            'report_rows' => [],
            'suggested_action_counts' => [],
        ];

        foreach ($bookings as $booking) {
            $hasFutureBookingDate = $this->isFutureDate($booking->booking_date, $today);
            $hasFutureServiceDate = $this->isFutureDate($booking->service_date, $today);
            $hasFutureEndDate = $this->isFutureDate($booking->service_end_date, $today);

            if (! $hasFutureBookingDate && ! $hasFutureServiceDate && ! $hasFutureEndDate && ! $this->hasYearDefaultedFlag($booking)) {
                continue;
            }

            $stats['affected_rows']++;

            if ($hasFutureBookingDate) {
                $stats['future_booking_date']++;
            }

            if ($hasFutureServiceDate) {
                $stats['future_service_date']++;
            }

            if ($hasFutureEndDate) {
                $stats['future_end_date']++;
            }

            if ($this->hasYearDefaultedFlag($booking)) {
                $stats['date_year_defaulted']++;
            }

            $csvRow = $this->findCsvRow($csvRows, $booking);

            if (count($stats['samples']) < 10) {
                $sample = $this->buildSample($booking, $csvRow, [
                    'future_booking_date' => $hasFutureBookingDate,
                    'future_service_date' => $hasFutureServiceDate,
                    'future_end_date' => $hasFutureEndDate,
                ]);

                $stats['samples'][] = $sample;
            }

            $reportRow = $this->buildReportRow($booking, $csvRow, [
                'future_booking_date' => $hasFutureBookingDate,
                'future_service_date' => $hasFutureServiceDate,
                'future_end_date' => $hasFutureEndDate,
            ]);

            $stats['report_rows'][] = $reportRow;
            $stats['suggested_action_counts'][$reportRow['suggested_action']] = ($stats['suggested_action_counts'][$reportRow['suggested_action']] ?? 0) + 1;
        }

        $this->writeReport($reportPath, $stats['report_rows']);

        $this->line('Audit Summary:');
        $this->line('- Total additional legacy bookings: ' . $stats['total_found']);
        $this->line('- Bookings with future booking_date: ' . $stats['future_booking_date']);
        $this->line('- Bookings with future service_date: ' . $stats['future_service_date']);
        $this->line('- Bookings with future end_date: ' . $stats['future_end_date']);
        $this->line('- Bookings with date_year_defaulted flags: ' . $stats['date_year_defaulted']);
        $this->line('- Bookings requiring review: ' . $stats['affected_rows']);
        $this->line('- Report rows exported: ' . count($stats['report_rows']));
        $this->line('No booking data was modified.');

        $this->line('Count by suggested_action:');
        foreach ($stats['suggested_action_counts'] as $action => $count) {
            $this->line("- {$action}: {$count}");
        }

        $this->line('Report path: ' . $reportPath);

        if (! empty($stats['samples'])) {
            $this->info('Sample rows (current values vs original CSV):');

            foreach ($stats['samples'] as $sample) {
                $this->line(sprintf(
                    '- %s | row %s | %s | booking_date: %s | service_date: %s | end_date: %s | CSV booking_date: %s | CSV service_date: %s | CSV end_date: %s | raw dates: %s | review_flags: %s | suggested action: %s',
                    $sample['reference_no'],
                    $sample['source_excel_row'],
                    $sample['title'],
                    $sample['booking_date'],
                    $sample['service_date'],
                    $sample['end_date'],
                    $sample['csv_booking_date'],
                    $sample['csv_service_date'],
                    $sample['csv_end_date'],
                    $sample['raw_dates'],
                    $sample['review_flags'],
                    $sample['suggested_action']
                ));
            }
        }

        if ($dryRun) {
            $this->warn('Dry run complete. No database changes were made.');
        }

        return self::SUCCESS;
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function loadCsvRows(string $file): array
    {
        $handle = fopen($file, 'r');

        if ($handle === false) {
            return [];
        }

        $headers = fgetcsv($handle);
        if (! is_array($headers)) {
            fclose($handle);

            return [];
        }

        $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]);
        $headers = array_map(static fn ($header) => trim((string) $header), $headers);

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($headers)) {
                continue;
            }

            $data = array_combine($headers, $row);

            if (! is_array($data)) {
                continue;
            }

            $sourceExcelRow = trim((string) ($data['source_excel_row'] ?? ''));
            $referenceNo = trim((string) ($data['reference_no'] ?? ''));

            if ($sourceExcelRow !== '') {
                $rows['row:' . $sourceExcelRow] = $data;
            }

            if ($referenceNo !== '') {
                $rows['ref:' . $referenceNo] = $data;
            }
        }

        fclose($handle);

        return $rows;
    }

    /**
     * @param array<string, array<string, string>> $csvRows
     */
    private function findCsvRow(array $csvRows, ServiceBooking $booking): ?array
    {
        $sourceExcelRow = $booking->source_excel_row ? (string) $booking->source_excel_row : '';
        $referenceNo = trim((string) $booking->reference_no);

        if ($sourceExcelRow !== '' && isset($csvRows['row:' . $sourceExcelRow])) {
            return $csvRows['row:' . $sourceExcelRow];
        }

        if ($referenceNo !== '' && isset($csvRows['ref:' . $referenceNo])) {
            return $csvRows['ref:' . $referenceNo];
        }

        return null;
    }

    private function isFutureDate(mixed $value, Carbon $today): bool
    {
        return $value instanceof Carbon && $value->copy()->startOfDay()->gt($today);
    }

    private function hasYearDefaultedFlag(ServiceBooking $booking): bool
    {
        $reviewFlags = mb_strtolower((string) ($booking->review_flags ?? ''));
        $notes = mb_strtolower((string) ($booking->notes ?? ''));

        return str_contains($reviewFlags, 'date_year_defaulted_to_2025')
            || str_contains($notes, 'parsed_default_year_2025');
    }

    /**
     * @param array<string, string>|null $csvRow
     * @param array{future_booking_date: bool, future_service_date: bool, future_end_date: bool} $flags
     */
    private function buildSample(ServiceBooking $booking, ?array $csvRow, array $flags): array
    {
        $rawDates = $this->extractRawDates((string) ($booking->notes ?? ''));
        $yearDefaulted = $this->hasYearDefaultedFlag($booking);

        return [
            'reference_no' => (string) $booking->reference_no,
            'source_excel_row' => (string) ($booking->source_excel_row ?? ''),
            'title' => (string) $booking->title,
            'booking_date' => optional($booking->booking_date)->toDateString() ?? '',
            'service_date' => optional($booking->service_date)->toDateString() ?? '',
            'end_date' => optional($booking->service_end_date)->toDateString() ?? '',
            'csv_booking_date' => (string) ($csvRow['booking_date'] ?? ''),
            'csv_service_date' => (string) ($csvRow['service_date'] ?? ''),
            'csv_end_date' => (string) ($csvRow['end_date'] ?? ''),
            'raw_dates' => $rawDates !== '' ? $rawDates : 'n/a',
            'review_flags' => (string) ($booking->review_flags ?? ''),
            'suggested_action' => $this->suggestAction($flags, $yearDefaulted, (string) ($csvRow['notes'] ?? ''), (string) ($booking->notes ?? '')),
        ];
    }

    /**
     * @param array<string, string>|null $csvRow
     * @param array{future_booking_date: bool, future_service_date: bool, future_end_date: bool} $flags
     * @return array<string, string>
     */
    private function buildReportRow(ServiceBooking $booking, ?array $csvRow, array $flags): array
    {
        $sample = $this->buildSample($booking, $csvRow, $flags);

        return [
            'id' => (string) $booking->id,
            'user_id' => (string) $booking->user_id,
            'client_username' => (string) optional($booking->user)->username,
            'reference_no' => $sample['reference_no'],
            'source_excel_row' => $sample['source_excel_row'],
            'title' => $sample['title'],
            'booking_type' => (string) $booking->booking_type,
            'current_booking_date' => $sample['booking_date'],
            'current_service_date' => $sample['service_date'],
            'current_service_end_date' => $sample['end_date'],
            'original_csv_booking_date' => $sample['csv_booking_date'],
            'original_csv_service_date' => $sample['csv_service_date'],
            'original_csv_end_date' => $sample['csv_end_date'],
            'raw_total_amount' => (string) ($booking->raw_total_amount ?? ''),
            'amount' => (string) $booking->amount,
            'paid_amount' => (string) $booking->paid_amount,
            'review_flags' => (string) ($booking->review_flags ?? ''),
            'notes' => (string) ($booking->notes ?? ''),
            'parse_method' => $this->extractParseMethod((string) ($csvRow['notes'] ?? ''), (string) ($booking->notes ?? '')),
            'suggested_action' => $sample['suggested_action'],
            'reason' => $this->buildReason($flags, $booking, $csvRow),
        ];
    }

    /**
     * @param array<int, array<string, string>> $rows
     */
    private function writeReport(string $file, array $rows): void
    {
        $handle = fopen($file, 'w');

        if ($handle === false) {
            $this->warn('Unable to write review report: ' . $file);

            return;
        }

        $headers = [
            'id',
            'user_id',
            'client_username',
            'reference_no',
            'source_excel_row',
            'title',
            'booking_type',
            'current_booking_date',
            'current_service_date',
            'current_service_end_date',
            'original_csv_booking_date',
            'original_csv_service_date',
            'original_csv_end_date',
            'raw_total_amount',
            'amount',
            'paid_amount',
            'review_flags',
            'notes',
            'parse_method',
            'suggested_action',
            'reason',
        ];

        fputcsv($handle, $headers);

        foreach ($rows as $row) {
            $line = [];

            foreach ($headers as $header) {
                $line[] = $row[$header] ?? '';
            }

            fputcsv($handle, $line);
        }

        fclose($handle);
    }

    /**
     * @param array{future_booking_date: bool, future_service_date: bool, future_end_date: bool} $flags
     */
    private function suggestAction(array $flags, bool $yearDefaulted, string $csvNotes, string $bookingNotes): string
    {
        $notes = mb_strtolower($csvNotes . ' ' . $bookingNotes);

        if (str_contains($notes, 'excel_serial/excel_serial')) {
            return 'manual_review_serial_dates';
        }

        if ($yearDefaulted) {
            if ($flags['future_end_date'] && ! $flags['future_service_date']) {
                return 'review_end_date_year_parsing';
            }

            if ($flags['future_service_date'] || $flags['future_booking_date']) {
                return 'review_future_year_fallback';
            }

            return 'review_year_defaulting';
        }

        if ($flags['future_service_date'] || $flags['future_end_date'] || $flags['future_booking_date']) {
            return 'manual_review_future_dates';
        }

        return 'no_action';
    }

    private function extractParseMethod(string $csvNotes, string $bookingNotes): string
    {
        $notes = $csvNotes . ' ' . $bookingNotes;

        if (preg_match('/Parse methods:\s*([^\.]+)\./i', $notes, $matches) === 1) {
            return trim((string) $matches[1]);
        }

        return '';
    }

    /**
     * @param array{future_booking_date: bool, future_service_date: bool, future_end_date: bool} $flags
     */
    private function buildReason(array $flags, ServiceBooking $booking, ?array $csvRow): string
    {
        $reasons = [];

        if ($flags['future_booking_date']) {
            $reasons[] = 'booking_date_is_future';
        }

        if ($flags['future_service_date']) {
            $reasons[] = 'service_date_is_future';
        }

        if ($flags['future_end_date']) {
            $reasons[] = 'service_end_date_is_future';
        }

        if ($this->hasYearDefaultedFlag($booking)) {
            $reasons[] = 'date_year_defaulted_flag_present';
        }

        $csvNotes = mb_strtolower((string) ($csvRow['notes'] ?? ''));
        $bookingNotes = mb_strtolower((string) ($booking->notes ?? ''));

        if (str_contains($csvNotes . ' ' . $bookingNotes, 'excel_serial/excel_serial')) {
            $reasons[] = 'serial_dates_need_manual_review';
        }

        return implode('; ', array_values(array_unique($reasons)));
    }

    private function extractRawDates(string $notes): string
    {
        if ($notes === '') {
            return '';
        }

        if (preg_match("/Raw dates:\s*from='([^']*)',\s*to='([^']*)'/i", $notes, $matches) === 1) {
            return "from='{$matches[1]}', to='{$matches[2]}'";
        }

        return '';
    }
}
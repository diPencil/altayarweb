<?php

namespace App\Console\Commands;

use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\PaymentGatewayLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ReconcileFawaterkDeposits extends Command
{
    protected $signature = 'fawaterk:reconcile-pending
        {--deposit_id= : Reconcile one deposit id}
        {--trx= : Reconcile one deposit trx}
        {--days=30 : Only include deposits created within this many days}
        {--limit=100 : Maximum deposits to inspect}
        {--dry-run : Report intended changes without mutating deposits}';

    protected $description = 'Safely reconcile pending Fawaterk deposits against Fawaterk invoice status.';

    protected array $summary = [
        'processed' => 0,
        'ambiguous_invoice_match' => 0,
        'pending' => 0,
        'paid' => 0,
        'failed' => 0,
        'verification_failed' => 0,
        'skipped' => 0,
    ];

    protected array $seenInvoices = [];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $deposits = $this->pendingFawaterkDeposits();

        if ($deposits->isEmpty()) {
            $this->info('No pending Fawaterk deposits found for the given filters.');
            $this->printSummary();
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn('Dry run enabled: no deposit statuses will be changed.');
        }

        foreach ($deposits as $deposit) {
            $this->reconcileDeposit($deposit, $dryRun);
        }

        $this->printSummary();
        return self::SUCCESS;
    }

    protected function pendingFawaterkDeposits()
    {
        $query = Deposit::with('gateway')
            ->where('method_code', 115)
            ->where('status', 2)
            ->whereNotNull('btc_wallet')
            ->where('btc_wallet', '<>', '')
            ->orderBy('id');

        if ($this->option('deposit_id')) {
            $query->where('id', $this->option('deposit_id'));
        }

        if ($this->option('trx')) {
            $query->where('trx', $this->option('trx'));
        }

        $days = (int) $this->option('days');
        if ($days > 0) {
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $limit = max(1, (int) $this->option('limit'));
        return $query->limit($limit)->get();
    }

    protected function reconcileDeposit(Deposit $deposit, bool $dryRun): void
    {
        $this->summary['processed']++;
        $invoiceId = (string) $deposit->btc_wallet;

        if (isset($this->seenInvoices[$invoiceId])) {
            $this->summary['skipped']++;
            $this->line("SKIP deposit {$deposit->id}: invoice {$invoiceId} already handled in this run.");
            return;
        }

        $activeMatches = $this->activeMatchesForInvoice($invoiceId);
        if ($activeMatches->count() > 1) {
            $this->seenInvoices[$invoiceId] = true;
            $candidates = $this->describeCandidates($activeMatches);
            $message = 'Multiple active deposits match invoice: ' . json_encode($candidates);
            $this->writeReconciliationLog($invoiceId, null, 'ambiguous_invoice_match', $message);
            $this->summary['ambiguous_invoice_match']++;
            $this->warn("AMBIGUOUS invoice {$invoiceId}: {$message}");
            return;
        }

        $gatewayResponse = $this->fetchInvoiceData($deposit, $invoiceId);
        if (!($gatewayResponse['ok'] ?? false)) {
            $message = $gatewayResponse['message'] ?? 'Fawaterk invoice verification failed.';
            $this->writeReconciliationLog($invoiceId, $deposit, 'verification_failed', $message, $gatewayResponse['payload'] ?? null);
            $this->summary['verification_failed']++;
            $this->error("VERIFY FAILED deposit {$deposit->id} invoice {$invoiceId}: {$message}");
            return;
        }

        $payload = $gatewayResponse['payload'];
        $decision = $this->gatewayDecision($deposit, $payload);

        if ($decision['decision'] === 'paid') {
            $this->handlePaidDecision($deposit, $invoiceId, $payload, $decision['message'], $dryRun);
            return;
        }

        if ($decision['decision'] === 'failed') {
            $this->handleFailedDecision($deposit, $invoiceId, $payload, $decision['message'], $dryRun);
            return;
        }

        $this->writeReconciliationLog($invoiceId, $deposit, 'pending', $decision['message'], $payload);
        $this->summary['pending']++;
        $this->line("PENDING deposit {$deposit->id} invoice {$invoiceId}: {$decision['message']}");
    }

    protected function activeMatchesForInvoice(string $invoiceId)
    {
        return Deposit::query()
            ->where('method_code', 115)
            ->whereIn('status', [0, 2])
            ->where(function ($query) use ($invoiceId) {
                $query->where('btc_wallet', $invoiceId)
                    ->orWhere('detail->gateway_invoice_id', $invoiceId)
                    ->orWhere('detail->gateway_invoice_id', (string) $invoiceId);
            })
            ->orderBy('id')
            ->get();
    }

    protected function fetchInvoiceData(Deposit $deposit, string $invoiceId): array
    {
        $gatewayCurrency = $deposit->gatewayCurrency();
        $gatewayConfig = json_decode((string) ($gatewayCurrency->gateway_parameter ?? '{}'));
        $apiKey = data_get($gatewayConfig, 'api_key.value') ?: data_get($gatewayConfig, 'api_key');

        if (!$apiKey) {
            return [
                'ok' => false,
                'message' => 'Missing Fawaterk API key for deposit gateway currency.',
            ];
        }

        try {
            $response = Http::acceptJson()->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(30)->get('https://app.fawaterk.com/api/v2/getInvoiceData/' . $invoiceId);

            $payload = $response->json();

            if (!$response->successful()) {
                return [
                    'ok' => false,
                    'message' => 'Fawaterk API returned HTTP ' . $response->status(),
                    'payload' => $payload,
                ];
            }

            return [
                'ok' => true,
                'payload' => $payload,
            ];
        } catch (Throwable $throwable) {
            return [
                'ok' => false,
                'message' => $throwable->getMessage(),
            ];
        }
    }

    protected function gatewayDecision(Deposit $deposit, array $payload): array
    {
        if ($this->gatewayConfirmsPaid($deposit, $payload)) {
            return [
                'decision' => 'paid',
                'message' => 'Gateway verified paid invoice.',
            ];
        }

        $failureReason = $this->gatewayFailureReason($payload);
        if ($failureReason) {
            return [
                'decision' => 'failed',
                'message' => 'Gateway reported failed/rejected/expired payment: ' . $failureReason,
            ];
        }

        return [
            'decision' => 'pending',
            'message' => 'Gateway invoice is still pending or unpaid.',
        ];
    }

    protected function gatewayConfirmsPaid(Deposit $deposit, array $payload): bool
    {
        $paid = (int) data_get($payload, 'data.paid') === 1
            || in_array(Str::lower((string) data_get($payload, 'data.status')), ['paid', 'success', 'succeeded', 'completed', 'complete', 'captured'], true);

        if (!$paid) {
            return false;
        }

        $currency = data_get($payload, 'data.currency');
        if ($currency && Str::upper((string) $currency) !== Str::upper((string) $deposit->method_currency)) {
            return false;
        }

        $total = data_get($payload, 'data.total');
        if ($total !== null && abs((float) $total - (float) $deposit->final_amo) > 0.01) {
            return false;
        }

        return true;
    }

    protected function gatewayFailureReason(array $payload): ?string
    {
        $values = [
            data_get($payload, 'errorMessage'),
            data_get($payload, 'response.gatewayCode'),
            data_get($payload, 'response.gatewayMessage'),
            data_get($payload, 'message'),
            data_get($payload, 'status'),
            data_get($payload, 'invoice_status'),
            data_get($payload, 'payment_status'),
            data_get($payload, 'data.status'),
            data_get($payload, 'data.invoice_status'),
            data_get($payload, 'data.payment_status'),
        ];

        $keywords = [
            'failed',
            'fail',
            'rejected',
            'reject',
            'declined',
            'decline',
            'cancelled',
            'canceled',
            'expired',
            'void',
            'error',
            'unsuccessful',
        ];

        foreach ($values as $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $normalized = Str::lower((string) $value);
            foreach ($keywords as $keyword) {
                if (Str::contains($normalized, $keyword)) {
                    return (string) $value;
                }
            }
        }

        return null;
    }

    protected function handlePaidDecision(Deposit $deposit, string $invoiceId, array $payload, string $message, bool $dryRun): void
    {
        $freshDeposit = Deposit::find($deposit->id);
        if (!$freshDeposit || (int) $freshDeposit->status !== 2) {
            $this->summary['skipped']++;
            $this->line("SKIP deposit {$deposit->id}: status changed before paid mutation.");
            return;
        }

        $statusBefore = (int) $freshDeposit->status;
        if (!$dryRun) {
            PaymentController::userDataUpdate($freshDeposit);
            $freshDeposit->refresh();
        }

        $this->writeReconciliationLog($invoiceId, $freshDeposit, 'paid', ($dryRun ? '[dry-run] ' : '') . $message, $payload, $statusBefore);
        $this->summary['paid']++;
        $this->info(($dryRun ? 'DRY-RUN PAID' : 'PAID') . " deposit {$deposit->id} invoice {$invoiceId}: {$message}");
    }

    protected function handleFailedDecision(Deposit $deposit, string $invoiceId, array $payload, string $message, bool $dryRun): void
    {
        $freshDeposit = Deposit::find($deposit->id);
        if (!$freshDeposit || (int) $freshDeposit->status !== 2) {
            $this->summary['skipped']++;
            $this->line("SKIP deposit {$deposit->id}: status changed before failed mutation.");
            return;
        }

        $statusBefore = (int) $freshDeposit->status;
        if (!$dryRun) {
            PaymentController::markDepositFailed($freshDeposit, $message);
            $freshDeposit->refresh();
        }

        $this->writeReconciliationLog($invoiceId, $freshDeposit, 'failed', ($dryRun ? '[dry-run] ' : '') . $message, $payload, $statusBefore);
        $this->summary['failed']++;
        $this->warn(($dryRun ? 'DRY-RUN FAILED' : 'FAILED') . " deposit {$deposit->id} invoice {$invoiceId}: {$message}");
    }

    protected function writeReconciliationLog(string $invoiceId, ?Deposit $deposit, string $decision, string $message, ?array $payload = null, ?int $statusBefore = null): void
    {
        try {
            PaymentGatewayLog::create([
                'gateway' => 'Fawaterk',
                'event_type' => 'reconciliation',
                'invoice_id' => $invoiceId,
                'deposit_id' => $deposit?->id,
                'trx' => $deposit?->trx,
                'local_status_before' => $statusBefore ?? $deposit?->getOriginal('status'),
                'local_status_after' => $deposit?->status,
                'decision' => $decision,
                'message' => $message,
                'payload' => $payload,
            ]);
        } catch (Throwable $throwable) {
            Log::warning('Unable to write Fawaterk reconciliation audit log.', [
                'error' => $throwable->getMessage(),
                'invoice_id' => $invoiceId,
                'deposit_id' => $deposit?->id,
                'trx' => $deposit?->trx,
                'decision' => $decision,
                'message' => $message,
            ]);
        }
    }

    protected function describeCandidates($deposits): array
    {
        return $deposits->map(function (Deposit $deposit) {
            return [
                'id' => $deposit->id,
                'trx' => $deposit->trx,
                'status' => $deposit->status,
            ];
        })->values()->all();
    }

    protected function printSummary(): void
    {
        $this->newLine();
        $this->info('Summary');
        foreach ($this->summary as $key => $value) {
            $this->line($key . ': ' . $value);
        }
    }
}

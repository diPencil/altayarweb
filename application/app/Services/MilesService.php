<?php

namespace App\Services;

use App\Exceptions\IdempotencyConflictException;
use App\Models\User;
use App\Models\UserMilesTransaction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class MilesService
{
    /**
     * Maximum single transaction and balance limit to prevent integer overflow.
     */
    public const MAX_MILES_LIMIT = 2000000000;

    /**
     * Grant miles to a user (Credit: direction = +1).
     *
     * @throws InvalidArgumentException|IdempotencyConflictException
     */
    public function grantMiles(
        User $user,
        int $amount,
        string $source,
        string $remark,
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?int $adminId = null,
        ?string $idempotencyKey = null,
        array $meta = [],
        ?int $agentId = null
    ): UserMilesTransaction {
        $this->validateAmount($amount);

        if ($idempotencyKey) {
            $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $this->resolveIdempotentMatch($existing, [
                    'user_id' => $user->id,
                    'type' => 'credit',
                    'direction' => 1,
                    'miles' => $amount,
                    'source' => $source,
                    'reference_id' => $referenceId,
                    'remark' => $remark,
                    'admin_id' => $adminId,
                    'agent_id' => $agentId,
                ]);
            }
        }

        try {
            return DB::transaction(function () use (
                $user, $amount, $source, $remark, $referenceId, $referenceType, $adminId, $idempotencyKey, $meta, $agentId
            ) {
                // Consistent lock order: User row locked first
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

                // Re-check idempotency inside lock in case of concurrent thread
                if ($idempotencyKey) {
                    $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                    if ($existing) {
                        return $this->resolveIdempotentMatch($existing, [
                            'user_id' => $lockedUser->id,
                            'type' => 'credit',
                            'direction' => 1,
                            'miles' => $amount,
                            'source' => $source,
                            'reference_id' => $referenceId,
                            'remark' => $remark,
                            'admin_id' => $adminId,
                            'agent_id' => $agentId,
                        ]);
                    }
                }

                $currentBalance = $this->calculateAvailableBalance($lockedUser->id);

                if (($currentBalance + $amount) > self::MAX_MILES_LIMIT) {
                    throw new InvalidArgumentException(__('Resulting miles balance would exceed the maximum allowable limit.'));
                }

                $newBalance = $currentBalance + $amount;

                return UserMilesTransaction::create([
                    'user_id' => $lockedUser->id,
                    'trx' => getTrx(),
                    'type' => 'credit',
                    'direction' => 1,
                    'miles' => $amount,
                    'balance_after' => $newBalance,
                    'source' => $source ?: 'manual',
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'remark' => $remark,
                    'admin_id' => $adminId,
                    'agent_id' => $agentId,
                    'is_reversal' => false,
                    'reversal_of_id' => null,
                    'idempotency_key' => $idempotencyKey,
                    'meta' => $meta,
                ]);
            });
        } catch (QueryException $e) {
            if ($idempotencyKey && $this->isDuplicateKeyException($e)) {
                $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $this->resolveIdempotentMatch($existing, [
                        'user_id' => $user->id,
                        'type' => 'credit',
                        'direction' => 1,
                        'miles' => $amount,
                        'source' => $source,
                        'reference_id' => $referenceId,
                    ]);
                }
            }
            throw $e;
        }
    }

    /**
     * Deduct miles from a user (Debit: direction = -1).
     *
     * @throws InvalidArgumentException|IdempotencyConflictException
     */
    public function deductMiles(
        User $user,
        int $amount,
        string $source,
        string $remark,
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?int $adminId = null,
        ?string $idempotencyKey = null,
        array $meta = [],
        ?int $agentId = null
    ): UserMilesTransaction {
        $this->validateAmount($amount);

        if (empty(trim($remark))) {
            throw new InvalidArgumentException(__('A reason is mandatory for mileage deductions.'));
        }

        if ($idempotencyKey) {
            $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $this->resolveIdempotentMatch($existing, [
                    'user_id' => $user->id,
                    'type' => 'debit',
                    'direction' => -1,
                    'miles' => $amount,
                    'source' => $source,
                    'reference_id' => $referenceId,
                    'remark' => $remark,
                    'admin_id' => $adminId,
                    'agent_id' => $agentId,
                ]);
            }
        }

        try {
            return DB::transaction(function () use (
                $user, $amount, $source, $remark, $referenceId, $referenceType, $adminId, $idempotencyKey, $meta, $agentId
            ) {
                // Consistent lock order: User row locked first
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

                if ($idempotencyKey) {
                    $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                    if ($existing) {
                        return $this->resolveIdempotentMatch($existing, [
                            'user_id' => $lockedUser->id,
                            'type' => 'debit',
                            'direction' => -1,
                            'miles' => $amount,
                            'source' => $source,
                            'reference_id' => $referenceId,
                            'remark' => $remark,
                            'admin_id' => $adminId,
                            'agent_id' => $agentId,
                        ]);
                    }
                }

                $currentBalance = $this->calculateAvailableBalance($lockedUser->id);

                if ($amount > $currentBalance) {
                    throw new InvalidArgumentException(__('Insufficient miles balance. Current balance is :balance miles.', ['balance' => $currentBalance]));
                }

                $newBalance = $currentBalance - $amount;

                return UserMilesTransaction::create([
                    'user_id' => $lockedUser->id,
                    'trx' => getTrx(),
                    'type' => 'debit',
                    'direction' => -1,
                    'miles' => $amount,
                    'balance_after' => $newBalance,
                    'source' => $source ?: 'manual',
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'remark' => $remark,
                    'admin_id' => $adminId,
                    'agent_id' => $agentId,
                    'is_reversal' => false,
                    'reversal_of_id' => null,
                    'idempotency_key' => $idempotencyKey,
                    'meta' => $meta,
                ]);
            });
        } catch (QueryException $e) {
            if ($idempotencyKey && $this->isDuplicateKeyException($e)) {
                $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $this->resolveIdempotentMatch($existing, [
                        'user_id' => $user->id,
                        'type' => 'debit',
                        'direction' => -1,
                        'miles' => $amount,
                        'source' => $source,
                        'reference_id' => $referenceId,
                    ]);
                }
            }
            throw $e;
        }
    }

    /**
     * Reverse an existing transaction safely with strict accounting symmetry.
     *
     * @throws InvalidArgumentException|IdempotencyConflictException
     */
    public function reverseTransaction(
        int $transactionId,
        string $reason,
        ?int $adminId = null,
        ?string $idempotencyKey = null,
        ?int $agentId = null
    ): UserMilesTransaction {
        if (empty(trim($reason))) {
            throw new InvalidArgumentException(__('A reversal reason is mandatory.'));
        }

        if ($idempotencyKey) {
            $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                $targetInitial = UserMilesTransaction::findOrFail($transactionId);
                return $this->resolveIdempotentMatch($existing, [
                    'user_id' => $targetInitial->user_id,
                    'source' => 'reversal',
                    'reversal_of_id' => $transactionId,
                    'admin_id' => $adminId,
                ]);
            }
        }

        try {
            return DB::transaction(function () use ($transactionId, $reason, $adminId, $idempotencyKey, $agentId) {
                // 1. Initial read to identify the user
                $targetInitial = UserMilesTransaction::findOrFail($transactionId);

                // 2. Strict hierarchical lock ordering: Lock USER first, then lock TRANSACTION
                $lockedUser = User::where('id', $targetInitial->user_id)->lockForUpdate()->firstOrFail();
                $target = UserMilesTransaction::where('id', $transactionId)->lockForUpdate()->firstOrFail();

                // Validation checks
                if ($target->is_reversed) {
                    throw new InvalidArgumentException(__('This transaction has already been reversed.'));
                }

                if ($target->is_reversal) {
                    throw new InvalidArgumentException(__('A reversal transaction cannot be reversed.'));
                }

                // Double check if any reversal already references this transaction
                $alreadyHasReversal = UserMilesTransaction::where('reversal_of_id', $target->id)->exists();
                if ($alreadyHasReversal) {
                    throw new InvalidArgumentException(__('A reversal record already exists for this transaction.'));
                }

                // Check idempotency inside lock
                if ($idempotencyKey) {
                    $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                    if ($existing) {
                        return $this->resolveIdempotentMatch($existing, [
                            'user_id' => $lockedUser->id,
                            'source' => 'reversal',
                            'reversal_of_id' => $target->id,
                            'admin_id' => $adminId,
                        ]);
                    }
                }

                $currentBalance = $this->calculateAvailableBalance($lockedUser->id);

                // Accounting symmetry:
                // Original Credit (+1) => Reversal is Debit (-1) (clawback)
                // Original Debit (-1)  => Reversal is Credit (+1) (restoration)
                $counterDirection = -1 * (int) $target->direction;
                $counterType = $counterDirection === 1 ? 'credit' : 'debit';
                $miles = (int) $target->miles;

                if ($counterDirection === -1 && ($currentBalance - $miles) < 0) {
                    throw new InvalidArgumentException(__(
                        'Cannot reverse this credit. Current user balance (:balance) is insufficient to absorb the deduction of :amount miles.',
                        ['balance' => $currentBalance, 'amount' => $miles]
                    ));
                }

                $newBalance = $currentBalance + ($counterDirection * $miles);

                if ($newBalance > self::MAX_MILES_LIMIT) {
                    throw new InvalidArgumentException(__('Resulting miles balance would exceed the maximum allowable limit.'));
                }

                // Mark original as reversed
                $target->is_reversed = true;
                $target->reversed_at = now();
                $target->save();

                // Create the counter-entry
                return UserMilesTransaction::create([
                    'user_id' => $lockedUser->id,
                    'trx' => getTrx(),
                    'type' => $counterType,
                    'direction' => $counterDirection,
                    'miles' => $miles,
                    'balance_after' => $newBalance,
                    'source' => 'reversal',
                    'reference_type' => UserMilesTransaction::class,
                    'reference_id' => $target->trx,
                    'remark' => 'Reversal of ' . $target->trx . ': ' . $reason,
                    'admin_id' => $adminId,
                    'agent_id' => $agentId,
                    'is_reversal' => true,
                    'reversal_of_id' => $target->id,
                    'idempotency_key' => $idempotencyKey,
                    'meta' => [
                        'original_trx' => $target->trx,
                        'original_type' => $target->type,
                        'original_direction' => $target->direction,
                        'reversal_reason' => $reason,
                    ],
                ]);
            });
        } catch (QueryException $e) {
            if ($idempotencyKey && $this->isDuplicateKeyException($e)) {
                $existing = UserMilesTransaction::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $this->resolveIdempotentMatch($existing, [
                        'source' => 'reversal',
                        'reversal_of_id' => $transactionId,
                    ]);
                }
            }
            throw $e;
        }
    }

    /**
     * Calculate current available balance directly from the authoritative ledger.
     */
    public function calculateAvailableBalance(int $userId): int
    {
        $credits = (int) UserMilesTransaction::where('user_id', $userId)
            ->where('direction', 1)
            ->sum('miles');

        $debits = (int) UserMilesTransaction::where('user_id', $userId)
            ->where('direction', -1)
            ->sum('miles');

        return max(0, $credits - $debits);
    }

    /**
     * Compute comprehensive customer metrics for the dedicated Miles page.
     */
    public function getUserMetrics(int $userId): array
    {
        $records = UserMilesTransaction::where('user_id', $userId)->get();

        $balance = 0;
        $totalEarned = 0;
        $totalDeducted = 0;

        foreach ($records as $row) {
            $balance += ($row->direction * (int) $row->miles);

            // Gross earnings: Non-reversal credits
            if ($row->type === 'credit' && ! $row->is_reversal) {
                $totalEarned += (int) $row->miles;
            }
            // Gross deductions: Non-reversal debits
            if ($row->type === 'debit' && ! $row->is_reversal) {
                $totalDeducted += (int) $row->miles;
            }
        }

        return [
            'available_miles' => max(0, $balance),
            'total_earned' => $totalEarned,
            'total_deducted' => $totalDeducted,
        ];
    }

    /**
     * Compute system-wide mileage metrics for the Admin Management dashboard.
     */
    public function getSystemMetrics(): array
    {
        $totalCredits = (int) UserMilesTransaction::where('direction', 1)->sum('miles');
        $totalDebits = (int) UserMilesTransaction::where('direction', -1)->sum('miles');

        $totalGranted = (int) UserMilesTransaction::where('type', 'credit')
            ->where('is_reversal', false)
            ->sum('miles');

        $totalDeducted = (int) UserMilesTransaction::where('type', 'debit')
            ->where('is_reversal', false)
            ->sum('miles');

        return [
            'total_available' => max(0, $totalCredits - $totalDebits),
            'total_granted' => $totalGranted,
            'total_deducted' => $totalDeducted,
        ];
    }

    /**
     * Validate positive integer range.
     */
    protected function validateAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException(__('Miles amount must be a positive integer greater than zero.'));
        }

        if ($amount > self::MAX_MILES_LIMIT) {
            throw new InvalidArgumentException(__('Miles amount exceeds the maximum single transaction limit.'));
        }
    }

    /**
     * Verify whether an existing transaction with the same idempotency key matches the intended payload.
     *
     * @throws IdempotencyConflictException
     */
    protected function resolveIdempotentMatch(UserMilesTransaction $existing, array $expectedPayload): UserMilesTransaction
    {
        foreach ($expectedPayload as $key => $value) {
            if ($key === 'reference_id') {
                if ((string)$existing->reference_id !== (string)$value) {
                    throw new IdempotencyConflictException(__('Idempotency key already used for a different payload (reference mismatch).'));
                }
                continue;
            }

            if ($key === 'remark') {
                if (trim((string)$existing->remark) !== trim((string)$value)) {
                    throw new IdempotencyConflictException(__('Idempotency key already used for a different payload (remark mismatch).'));
                }
                continue;
            }

            if ($key === 'admin_id') {
                if ((string)$existing->admin_id !== (string)$value) {
                    throw new IdempotencyConflictException(__('Idempotency key already used for a different actor (admin mismatch).'));
                }
                continue;
            }

            if ($key === 'agent_id') {
                if ((string)$existing->agent_id !== (string)$value) {
                    throw new IdempotencyConflictException(__('Idempotency key already used for a different actor (agent mismatch).'));
                }
                continue;
            }

            if ($existing->{$key} != $value) {
                throw new IdempotencyConflictException(__('Idempotency key already used for a different payload (:field mismatch).', ['field' => $key]));
            }
        }

        return $existing;
    }

    /**
     * Check if a QueryException corresponds to a unique constraint violation.
     */
    protected function isDuplicateKeyException(QueryException $e): bool
    {
        $errorCode = $e->getCode();
        $sqlState = $e->errorInfo[0] ?? null;

        return $errorCode == 23000 || $sqlState === '23000';
    }
}

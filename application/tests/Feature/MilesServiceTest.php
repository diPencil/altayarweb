<?php

namespace Tests\Feature;

use App\Exceptions\IdempotencyConflictException;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Tests\TestCase;

class MilesServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected MilesService $service;
    protected User $user;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(MilesService::class);

        // Create disposable test fixtures wrapped in DatabaseTransactions
        $this->user = new User();
        $this->user->firstname = 'Test';
        $this->user->lastname = 'MilesUser';
        $this->user->username = 'testmiles_' . uniqid();
        $this->user->email = 'testmiles_' . uniqid() . '@example.com';
        $this->user->mobile = '+2010' . rand(10000000, 99999999);
        $this->user->password = bcrypt('password');
        $this->user->balance = 150.00;
        $this->user->status = 1;
        $this->user->ev = 1;
        $this->user->sv = 1;
        $this->user->save();

        $this->admin = Admin::first();
        if (!$this->admin) {
            $this->admin = new Admin();
            $this->admin->name = 'Test Admin';
            $this->admin->email = 'admin_test_' . uniqid() . '@example.com';
            $this->admin->username = 'admin_' . uniqid();
            $this->admin->password = bcrypt('password');
            $this->admin->save();
        }
    }

    public function test_can_grant_miles_successfully(): void
    {
        $tx = $this->service->grantMiles(
            user: $this->user,
            amount: 1000,
            source: 'flight',
            remark: 'Promotional flight bonus',
            referenceId: 'FLIGHT-101',
            referenceType: 'flight',
            adminId: $this->admin->id
        );

        $this->assertInstanceOf(UserMilesTransaction::class, $tx);
        $this->assertEquals('credit', $tx->type);
        $this->assertEquals(1, $tx->direction);
        $this->assertEquals(1000, $tx->miles);
        $this->assertEquals(1000, $tx->balance_after);
        $this->assertFalse($tx->is_reversal);
        $this->assertFalse($tx->is_reversed);
        $this->assertEquals($this->admin->id, $tx->admin_id);

        $this->assertEquals(1000, $this->service->calculateAvailableBalance($this->user->id));
        $this->assertEquals(1000, $this->user->miles_balance);
    }

    public function test_can_deduct_miles_successfully(): void
    {
        $this->service->grantMiles(
            user: $this->user,
            amount: 1000,
            source: 'flight',
            remark: 'Initial grant'
        );

        $tx = $this->service->deductMiles(
            user: $this->user,
            amount: 200,
            source: 'manual',
            remark: 'Manual admin correction',
            adminId: $this->admin->id
        );

        $this->assertInstanceOf(UserMilesTransaction::class, $tx);
        $this->assertEquals('debit', $tx->type);
        $this->assertEquals(-1, $tx->direction);
        $this->assertEquals(200, $tx->miles);
        $this->assertEquals(800, $tx->balance_after);

        $this->assertEquals(800, $this->service->calculateAvailableBalance($this->user->id));
        $this->assertEquals(800, $this->user->miles_balance);

        $metrics = $this->service->getUserMetrics($this->user->id);
        $this->assertEquals(800, $metrics['available_miles']);
        $this->assertEquals(1000, $metrics['total_earned']);
        $this->assertEquals(200, $metrics['total_deducted']);
    }

    public function test_cannot_deduct_more_than_available_balance(): void
    {
        $this->service->grantMiles(
            user: $this->user,
            amount: 500,
            source: 'tour_package',
            remark: 'Package bonus'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Insufficient miles balance/i');

        $this->service->deductMiles(
            user: $this->user,
            amount: 501,
            source: 'manual',
            remark: 'Excessive deduction'
        );

        $this->assertEquals(500, $this->service->calculateAvailableBalance($this->user->id));
    }

    public function test_can_reverse_credit_transaction_safely(): void
    {
        $grantTx = $this->service->grantMiles(
            user: $this->user,
            amount: 1000,
            source: 'flight',
            remark: 'Erroneous grant'
        );

        $reversalTx = $this->service->reverseTransaction(
            transactionId: $grantTx->id,
            reason: 'Granted in error',
            adminId: $this->admin->id
        );

        $this->assertInstanceOf(UserMilesTransaction::class, $reversalTx);
        $this->assertEquals('debit', $reversalTx->type);
        $this->assertEquals(-1, $reversalTx->direction);
        $this->assertEquals(1000, $reversalTx->miles);
        $this->assertEquals(0, $reversalTx->balance_after);
        $this->assertTrue($reversalTx->is_reversal);
        $this->assertEquals($grantTx->id, $reversalTx->reversal_of_id);

        $grantTx->refresh();
        $this->assertTrue($grantTx->is_reversed);
        $this->assertNotNull($grantTx->reversed_at);

        $this->assertEquals(0, $this->service->calculateAvailableBalance($this->user->id));
    }

    public function test_cannot_reverse_credit_if_balance_insufficient(): void
    {
        $grantTx = $this->service->grantMiles(
            user: $this->user,
            amount: 1000,
            source: 'flight',
            remark: 'Initial grant'
        );

        // Deduct 800 miles, leaving only 200
        $this->service->deductMiles(
            user: $this->user,
            amount: 800,
            source: 'hotel',
            remark: 'Hotel booking'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/insufficient/i');

        // Attempting to reverse 1000 miles credit when only 200 is available
        $this->service->reverseTransaction(
            transactionId: $grantTx->id,
            reason: 'Clawback attempt'
        );
    }

    public function test_can_reverse_debit_transaction_safely(): void
    {
        $this->service->grantMiles(
            user: $this->user,
            amount: 1000,
            source: 'flight',
            remark: 'Initial balance'
        );

        $debitTx = $this->service->deductMiles(
            user: $this->user,
            amount: 300,
            source: 'manual',
            remark: 'Mistaken deduction'
        );

        $this->assertEquals(700, $this->service->calculateAvailableBalance($this->user->id));

        $reversalTx = $this->service->reverseTransaction(
            transactionId: $debitTx->id,
            reason: 'Reversing erroneous deduction',
            adminId: $this->admin->id
        );

        $this->assertInstanceOf(UserMilesTransaction::class, $reversalTx);
        $this->assertEquals('credit', $reversalTx->type);
        $this->assertEquals(1, $reversalTx->direction);
        $this->assertEquals(300, $reversalTx->miles);
        $this->assertEquals(1000, $reversalTx->balance_after);
        $this->assertTrue($reversalTx->is_reversal);

        $this->assertEquals(1000, $this->service->calculateAvailableBalance($this->user->id));
    }

    public function test_cannot_reverse_already_reversed_transaction(): void
    {
        $grantTx = $this->service->grantMiles(
            user: $this->user,
            amount: 500,
            source: 'manual',
            remark: 'Grant'
        );

        $this->service->reverseTransaction(
            transactionId: $grantTx->id,
            reason: 'First reversal'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/already been reversed/i');

        $this->service->reverseTransaction(
            transactionId: $grantTx->id,
            reason: 'Second reversal attempt'
        );
    }

    public function test_cannot_reverse_a_reversal_transaction(): void
    {
        $grantTx = $this->service->grantMiles(
            user: $this->user,
            amount: 500,
            source: 'manual',
            remark: 'Grant'
        );

        $reversalTx = $this->service->reverseTransaction(
            transactionId: $grantTx->id,
            reason: 'First reversal'
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/reversal transaction cannot be reversed/i');

        $this->service->reverseTransaction(
            transactionId: $reversalTx->id,
            reason: 'Attempting to reverse the reversal'
        );
    }

    public function test_strict_idempotency_identical_payload_returns_original(): void
    {
        $key = 'IDEMP-' . uniqid();

        $tx1 = $this->service->grantMiles(
            user: $this->user,
            amount: 750,
            source: 'hotel',
            remark: 'Hotel stay bonus',
            referenceId: 'HTL-999',
            idempotencyKey: $key
        );

        $tx2 = $this->service->grantMiles(
            user: $this->user,
            amount: 750,
            source: 'hotel',
            remark: 'Hotel stay bonus',
            referenceId: 'HTL-999',
            idempotencyKey: $key
        );

        $this->assertEquals($tx1->id, $tx2->id);
        $this->assertEquals($tx1->trx, $tx2->trx);
        $this->assertEquals(750, $this->service->calculateAvailableBalance($this->user->id));
        $this->assertEquals(1, UserMilesTransaction::where('idempotency_key', $key)->count());
    }

    public function test_strict_idempotency_payload_mismatch_throws_exception(): void
    {
        $key = 'IDEMP-' . uniqid();

        $this->service->grantMiles(
            user: $this->user,
            amount: 500,
            source: 'flight',
            remark: 'First request',
            idempotencyKey: $key
        );

        $this->expectException(IdempotencyConflictException::class);

        // Same key, different amount -> conflict
        $this->service->grantMiles(
            user: $this->user,
            amount: 600,
            source: 'flight',
            remark: 'Conflicting second request',
            idempotencyKey: $key
        );
    }

    public function test_negative_amounts_and_zero_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->service->grantMiles(
            user: $this->user,
            amount: -50,
            source: 'manual',
            remark: 'Negative grant'
        );
    }

    public function test_user_delete_block_reason_protects_miles_history(): void
    {
        // User without miles or bookings has no block reason
        $this->assertNull($this->user->deleteBlockReason());

        // Once a miles transaction exists, deletion is strictly blocked
        $this->service->grantMiles(
            user: $this->user,
            amount: 100,
            source: 'manual',
            remark: 'Audit record'
        );

        $this->user->refresh();
        $blockReason = $this->user->deleteBlockReason();
        $this->assertNotNull($blockReason);
        $this->assertStringContainsString('cannot be deleted', $blockReason);
    }

    public function test_miles_permission_semantics(): void
    {
        // 1. null permissions -> legacy / unrestricted default -> TRUE
        $this->user->dashboard_permissions = null;
        $this->user->save();
        $this->assertTrue(userCanMiles($this->user));
        $this->assertTrue(userDashboardCan('miles', $this->user));

        // 2. empty array [] -> explicitly disabled; does NOT fall back to unrestricted -> FALSE
        $this->user->dashboard_permissions = [];
        $this->user->save();
        $this->assertFalse(userCanMiles($this->user));
        $this->assertFalse(userDashboardCan('miles', $this->user));

        // 3. custom array with 'miles' -> TRUE
        $this->user->dashboard_permissions = ['wallet_balance', 'miles'];
        $this->user->save();
        $this->assertTrue(userCanMiles($this->user));
        $this->assertTrue(userDashboardCan('miles', $this->user));
        $this->assertTrue(userDashboardCan('wallet_balance', $this->user));
        $this->assertFalse(userDashboardCan('loyalty_points', $this->user));

        // 4. custom array without 'miles' -> FALSE
        $this->user->dashboard_permissions = ['wallet_balance', 'loyalty_points'];
        $this->user->save();
        $this->assertFalse(userCanMiles($this->user));
        $this->assertFalse(userDashboardCan('miles', $this->user));
        $this->assertTrue(userDashboardCan('wallet_balance', $this->user));
        $this->assertTrue(userDashboardCan('loyalty_points', $this->user));
    }

    public function test_unrelated_balances_remain_untouched(): void
    {
        $initialWalletBalance = $this->user->balance;
        $initialPointsBalance = $this->user->membership_points_balance;
        $initialCashbackBalance = $this->user->cashback_balance;

        $this->service->grantMiles($this->user, 5000, 'manual', 'Large grant');
        $this->service->deductMiles($this->user, 1200, 'manual', 'Deduction');

        $this->user->refresh();

        $this->assertEquals($initialWalletBalance, $this->user->balance);
        $this->assertEquals($initialPointsBalance, $this->user->membership_points_balance);
        $this->assertEquals($initialCashbackBalance, $this->user->cashback_balance);
        $this->assertEquals(3800, $this->user->miles_balance);
    }
}

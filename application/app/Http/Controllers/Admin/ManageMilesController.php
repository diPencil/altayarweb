<?php

namespace App\Http\Controllers\Admin;

use App\Exceptions\IdempotencyConflictException;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMilesTransaction;
use App\Services\MilesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ManageMilesController extends Controller
{
    public function __construct(protected MilesService $milesService)
    {
    }

    /**
     * Display the main Miles Management page.
     */
    public function index(Request $request)
    {
        if (!adminCanMiles('view')) {
            $notify[] = ['error', __('Unauthorized access. You do not have permission to view Miles Management.')];
            $redirect = auth('employee')->check() ? to_route('employee.home') : to_route('admin.dashboard');
            return $redirect->withNotify($notify);
        }

        $pageTitle = __('Miles Management');

        // Aggregate system metrics via central authoritative service
        $metrics = $this->milesService->getSystemMetrics();
        $totalAvailable = $metrics['total_available'];
        $totalGranted = $metrics['total_granted'];
        $totalDeducted = $metrics['total_deducted'];

        // Build filtered transaction query
        $query = UserMilesTransaction::with(['user', 'admin', 'employee', 'reversalOf'])->orderByDesc('id');

        // Search by username, email, trx, or reference_id
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('trx', 'like', "%{$search}%")
                    ->orWhere('reference_id', 'like', "%{$search}%")
                    ->orWhere('remark', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('username', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by operation type (credit or debit)
        if ($request->filled('type') && in_array($request->type, ['credit', 'debit'], true)) {
            $query->where('type', $request->type);
        }

        // Filter by source category
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Filter by date range (e.g., "10/01/2026 - 10/22/2026" or single date)
        if ($request->filled('date')) {
            $date = explode('-', $request->date);
            $startDate = trim($date[0]);
            $endDate = isset($date[1]) ? trim($date[1]) : null;

            try {
                if ($endDate) {
                    $start = Carbon::parse($startDate)->startOfDay();
                    $end = Carbon::parse($endDate)->endOfDay();
                    $query->whereBetween('created_at', [$start, $end]);
                } else {
                    $query->whereDate('created_at', Carbon::parse($startDate));
                }
            } catch (\Throwable $e) {
                // If date parsing fails, skip date filter safely
            }
        }

        $transactions = $query->paginate(getPaginate());

        // Users for the modal dropdown
        $users = User::orderBy('username')->get(['id', 'username', 'email']);

        // Distinct sources for filter dropdown
        $sources = UserMilesTransaction::distinct('source')
            ->whereNotNull('source')
            ->pluck('source');

        return view('admin.membership.miles.index', compact(
            'pageTitle',
            'transactions',
            'users',
            'sources',
            'totalAvailable',
            'totalGranted',
            'totalDeducted'
        ));
    }

    /**
     * Grant or Deduct miles for a user.
     */
    public function operate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'act' => 'required|in:grant,deduct',
            'amount' => 'required|integer|min:1|max:10000000',
            'source' => 'nullable|string|max:50',
            'reference_id' => 'nullable|string|max:100',
            'remark' => 'required|string|max:255',
            'idempotency_key' => 'nullable|string|max:100',
        ]);

        $action = $request->act === 'grant' ? 'grant' : 'deduct';

        if (!adminCanMiles($action)) {
            $notify[] = ['error', __('Unauthorized access. You do not have permission to :action miles.', ['action' => $action])];
            return back()->withNotify($notify);
        }

        $adminId = null;
        $agentId = null;
        if (auth('admin')->check()) {
            $adminId = auth('admin')->id();
        } elseif (auth('employee')->check()) {
            $agentId = auth('employee')->id();
        } elseif (function_exists('employee') && employee()) {
            $agentId = employee()->id;
        }
        $source = $request->source ?: 'admin';
        $idempotencyKey = $request->idempotency_key ?: (string) Str::uuid();

        $targetUser = User::findOrFail($request->user_id);

        try {
            if ($request->act === 'grant') {
                $trx = $this->milesService->grantMiles(
                    user: $targetUser,
                    amount: (int) $request->amount,
                    source: $source,
                    remark: $request->remark,
                    referenceId: $request->reference_id,
                    adminId: $adminId,
                    idempotencyKey: $idempotencyKey,
                    meta: [],
                    agentId: $agentId
                );

                $notify[] = ['success', __('Successfully granted :amount miles to user.', ['amount' => number_format($request->amount)])];
            } else {
                $trx = $this->milesService->deductMiles(
                    user: $targetUser,
                    amount: (int) $request->amount,
                    source: $source,
                    remark: $request->remark,
                    referenceId: $request->reference_id,
                    adminId: $adminId,
                    idempotencyKey: $idempotencyKey,
                    meta: [],
                    agentId: $agentId
                );

                $notify[] = ['success', __('Successfully deducted :amount miles from user.', ['amount' => number_format($request->amount)])];
            }
        } catch (IdempotencyConflictException $e) {
            $notify[] = ['error', __('Idempotency conflict: A transaction with this submission key exists with different attributes.')];
            return back()->withNotify($notify);
        } catch (InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        } catch (\Throwable $e) {
            Log::error('Miles operation failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $notify[] = ['error', __('An unexpected error occurred while processing miles. Please try again.')];
            return back()->withNotify($notify);
        }

        return back()->withNotify($notify);
    }

    /**
     * Reverse an existing miles transaction.
     */
    public function reverse(Request $request, $id)
    {
        if (!adminCanMiles('reverse')) {
            $notify[] = ['error', __('Unauthorized access. You do not have permission to reverse miles transactions.')];
            return back()->withNotify($notify);
        }

        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $adminId = null;
        $agentId = null;
        if (auth('admin')->check()) {
            $adminId = auth('admin')->id();
        } elseif (auth('employee')->check()) {
            $agentId = auth('employee')->id();
        } elseif (function_exists('employee') && employee()) {
            $agentId = employee()->id;
        }

        try {
            $reversal = $this->milesService->reverseTransaction(
                transactionId: (int) $id,
                reason: $request->reason,
                adminId: $adminId,
                idempotencyKey: null,
                agentId: $agentId
            );

            $notify[] = ['success', __('Transaction :trx has been successfully reversed.', ['trx' => $reversal->trx])];
        } catch (InvalidArgumentException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        } catch (\Throwable $e) {
            Log::error('Miles reversal failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            $notify[] = ['error', __('An unexpected error occurred while reversing the transaction.')];
            return back()->withNotify($notify);
        }

        return back()->withNotify($notify);
    }
}

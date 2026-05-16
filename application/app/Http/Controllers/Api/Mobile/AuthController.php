<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $identifier = trim((string) $request->input('identifier'));
        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($field, $identifier)->first();

        if (! $user || ! Hash::check((string) $request->input('password'), (string) $user->password)) {
            return response()->json(['detail' => 'Invalid credentials'], 401);
        }

        if ((int) ($user->status ?? 1) !== 1) {
            return response()->json(['detail' => 'Account is not active'], 403);
        }

        return response()->json($this->buildAuthPayload($user));
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'min:3', 'max:40', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'first_name' => ['required', 'string', 'max:40'],
            'last_name' => ['required', 'string', 'max:40'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'language' => ['nullable', 'string', 'max:10'],
            'referral_code' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $user = new User();
        $user->firstname = trim((string) $request->input('first_name'));
        $user->lastname = trim((string) $request->input('last_name'));
        $user->email = strtolower(trim((string) $request->input('email')));
        $user->password = Hash::make((string) $request->input('password'));
        $user->username = trim((string) $request->input('username'));
        $user->mobile = $request->filled('phone') ? trim((string) $request->input('phone')) : null;
        $user->gender = $request->filled('gender') ? trim((string) $request->input('gender')) : null;
        $user->status = 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->tv = 1;
        $user->ts = 0;

        $referralCode = trim((string) $request->input('referral_code'));
        if ($referralCode !== '') {
            $refUser = User::where('username', $referralCode)->first();
            if ($refUser) {
                $user->ref_by = $refUser->id;
            }
        }

        if ($request->filled('country')) {
            $user->address = (object) [
                'address' => '',
                'state' => '',
                'zip' => '',
                'country' => $request->input('country'),
                'city' => '',
            ];
        }

        $user->save();

        return response()->json($this->buildAuthPayload($user), 201);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['detail' => 'Unauthenticated'], 401);
        }

        return response()->json($this->formatUser($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user instanceof User) {
            $user->tokens()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'refresh_token' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['detail' => $validator->errors()->first()], 422);
        }

        $token = PersonalAccessToken::findToken((string) $request->input('refresh_token'));
        if (! $token || ! $token->tokenable instanceof User) {
            return response()->json(['detail' => 'Invalid refresh token'], 401);
        }

        $user = $token->tokenable;
        $accessToken = $user->createToken('mobile_access_token')->plainTextToken;

        return response()->json([
            'access_token' => $accessToken,
        ]);
    }

    private function buildAuthPayload(User $user): array
    {
        $accessToken = $user->createToken('mobile_access_token')->plainTextToken;
        $refreshToken = $user->createToken('mobile_refresh_token')->plainTextToken;

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 31536000,
            'user' => $this->formatUser($user),
        ];
    }

    private function formatUser(User $user): array
    {
        $membership = $user->currentMembership()->with('plan')->first();
        $planNameEn = $membership->plan->name ?? $membership->plan_name_en ?? $membership->display_name ?? null;
        $planNameAr = $membership->plan->name_ar ?? $membership->plan_name_ar ?? null;
        $membershipCode = $membership->member_code ?? $membership->membership_id_display ?? $user->membership_id_display ?? null;
        $validFrom = optional($membership->start_date)->toDateString();
        $validUntil = optional($membership->end_date)->toDateString();
        $cashbackBalance = isset($user->cashback_balance) ? (float) $user->cashback_balance : 0;
        $currencyText = strtoupper((string) (gs()->cur_text ?? 'USD')) ?: 'USD';
        $currencySymbol = (string) (gs()->cur_sym ?? '$');

        return [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'first_name' => $user->firstname ?: trim((string) ($user->name ?? '')),
            'last_name' => $user->lastname ?: '',
            'phone' => $user->mobile ?: null,
            'role' => 'CUSTOMER',
            'language' => $user->language ?? 'en',
            'avatar' => $user->avatar ?? $user->image ?? null,
            'email_verified' => (int) ($user->ev ?? 0) === 1,
            'phone_verified' => (int) ($user->sv ?? 0) === 1,
            'membership_id_display' => $membershipCode,
            'club_gifts' => $cashbackBalance,
            'currency' => $currencyText,
            'currency_symbol' => $currencySymbol,
            'membership' => $membership ? [
                'membership_number' => $membership->membership_number ?? null,
                'membership_id_display' => $membershipCode,
                'member_code' => $membershipCode,
                'plan_name' => $planNameEn,
                'display_name' => $planNameEn,
                'plan_name_ar' => $planNameAr,
                'plan_name_en' => $planNameEn,
                'tier_code' => $membership->tier_code ?? null,
                'valid_from' => $validFrom,
                'starts_at' => optional($membership->start_date)->toISOString(),
                'valid_until' => $validUntil,
                'expiry_date' => $validUntil,
                'status' => $membership->status ?? null,
                'points_balance' => $membership->points_balance ?? null,
                'cashback_balance' => $cashbackBalance,
                'club_gifts' => $cashbackBalance,
                'currency' => $currencyText,
                'currency_symbol' => $currencySymbol,
                'is_lifetime' => (bool) ($membership->is_lifetime ?? false),
            ] : null,
            'gender' => $user->gender ?? null,
            'country' => is_object($user->address ?? null) ? ($user->address->country ?? null) : null,
            'wallet_balance' => isset($user->balance) ? (float) $user->balance : 0,
            'cashback_balance' => $cashbackBalance,
            'points' => [
                'current_balance' => (int) ($user->membership_points_balance ?? 0),
                'total_earned' => (int) $user->membershipPointTransactions()->where('type', 'earned')->sum('points'),
            ],
            'created_at' => optional($user->created_at)->toISOString(),
        ];
    }
}
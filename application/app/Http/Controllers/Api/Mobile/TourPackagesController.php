<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\TourBooking;
use App\Models\TourPackage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TourPackagesController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $tourPackage = TourPackage::with(['category', 'TourPackagePrimaryImage', 'tour_package_images'])
            ->where('id', $id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $this->normalizeTourPackage($tourPackage),
        ]);
    }

    public function book(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'seat' => ['required', 'integer', 'min:1'],
            'user_proposal_date' => ['nullable', 'date'],
            'save_card' => ['nullable', 'boolean'],
            'use_cashback' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $tourPackage = TourPackage::query()->findOrFail($id);
        $user = $request->user();
        $seat = (int) $request->input('seat');
        $saveCard = $request->boolean('save_card');
        $useCashback = $request->boolean('use_cashback');
        $userProposalDate = null;

        if ((int) $tourPackage->flexible_date === 1 && filled($request->input('user_proposal_date'))) {
            try {
                $userProposalDate = Carbon::parse((string) $request->input('user_proposal_date'));
            } catch (\Throwable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid proposed date supplied',
                    'errors' => [
                        'user_proposal_date' => ['The proposed date is invalid.'],
                    ],
                ], 422);
            }

            if ($userProposalDate->lt(now())) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid proposed date supplied',
                    'errors' => [
                        'user_proposal_date' => ['The proposed date must be today or a future date.'],
                    ],
                ], 422);
            }
        }

        if ($tourPackage->tour_end && Carbon::parse($tourPackage->tour_end)->lt(now())) {
            return response()->json([
                'success' => false,
                'message' => 'Tour package is expired',
                'errors' => [
                    'tour_package_id' => ['Tour package is expired.'],
                ],
            ], 422);
        }

        if ((int) $tourPackage->person_capability <= (int) $tourPackage->booking_person) {
            return response()->json([
                'success' => false,
                'message' => 'Seats are not available for this tour package',
                'errors' => [
                    'seat' => ['Seats are not available for this tour package.'],
                ],
            ], 422);
        }

        if ((int) $tourPackage->person_capability < ((int) $tourPackage->booking_person + $seat)) {
            $availableSeats = max(0, (int) $tourPackage->person_capability - (int) $tourPackage->booking_person);

            return response()->json([
                'success' => false,
                'message' => 'Seats are not available for this tour package',
                'errors' => [
                    'seat' => ["Seats are not available for this tour package. Available seats: {$availableSeats}"],
                ],
            ], 422);
        }

        $bookingAmount = (float) showTourPackageCalculateDiscount(((float) $tourPackage->price * $seat), (float) $tourPackage->discount);
        $cashbackUsed = 0.0;

        if ($useCashback) {
            $cashbackUsed = min((float) ($user->cashback_balance ?? 0), $bookingAmount);
        }

        $amountToPay = max(0, $bookingAmount - $cashbackUsed);

        $gatewayCurrency = $this->resolveTourBookingGateway($request);
        if (! $gatewayCurrency) {
            return response()->json([
                'success' => false,
                'message' => 'No active online payment gateway is available',
                'errors' => [
                    'payment' => ['No active online payment gateway is available.'],
                ],
            ], 422);
        }

        if ($gatewayCurrency->min_amount > $amountToPay || $gatewayCurrency->max_amount < $amountToPay) {
            return response()->json([
                'success' => false,
                'message' => 'Please follow deposit limit',
                'errors' => [
                    'amount' => ['Please follow deposit limit.'],
                ],
            ], 422);
        }

        try {
            $result = DB::transaction(function () use (
                $user,
                $tourPackage,
                $seat,
                $userProposalDate,
                $bookingAmount,
                $cashbackUsed,
                $amountToPay,
                $gatewayCurrency,
                $saveCard,
                $useCashback
            ): array {
                $tourBooking = new TourBooking();
                $tourBooking->user_id = $user->id;
                $tourBooking->owner_id = $tourPackage->user_id;
                $tourBooking->owner_type = $tourPackage->user_type;
                $tourBooking->price = $bookingAmount;
                $tourBooking->discount = $tourPackage->discount;
                $tourBooking->cashback_used = $cashbackUsed;
                $tourBooking->tour_package_id = $tourPackage->id;
                $tourBooking->user_proposal_date = $userProposalDate ?? $tourPackage->tour_start;
                $tourBooking->seat = $seat;
                $tourBooking->status = 0;
                $tourBooking->save();

                $charge = (float) $gatewayCurrency->fixed_charge + ($amountToPay * (float) $gatewayCurrency->percent_charge / 100);
                $payable = $amountToPay + $charge;
                $finalAmo = $payable * (float) $gatewayCurrency->rate;

                $deposit = new Deposit();
                $deposit->user_id = $user->id;
                $deposit->tour_booking_id = $tourBooking->id;
                $deposit->method_code = $gatewayCurrency->method_code;
                $deposit->method_currency = strtoupper((string) $gatewayCurrency->currency);
                $deposit->amount = $amountToPay;
                $deposit->charge = $charge;
                $deposit->rate = $gatewayCurrency->rate;
                $deposit->final_amo = $finalAmo;
                $deposit->btc_amo = 0;
                $deposit->btc_wallet = '';
                $deposit->trx = getTrx();
                $deposit->try = 0;
                $deposit->status = 0;
                $deposit->detail = (object) [
                    'payment_flow' => 'tour_booking',
                    'source' => 'tour_package',
                    'save_card' => $saveCard,
                    'use_cashback' => $useCashback,
                    'tour_package_id' => $tourPackage->id,
                    'seat' => $seat,
                    'user_proposal_date' => optional($userProposalDate)->toDateTimeString() ?? optional($tourPackage->tour_start)->toDateTimeString(),
                ];
                $deposit->save();

                return [
                    'tour_booking' => $tourBooking,
                    'deposit' => $deposit,
                ];
            });
        } catch (\Throwable $throwable) {
            report($throwable);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create tour package booking',
                'errors' => [
                    'booking' => ['Failed to create tour package booking.'],
                ],
            ], 500);
        }

        /** @var TourBooking $tourBooking */
        $tourBooking = $result['tour_booking'];
        /** @var Deposit $deposit */
        $deposit = $result['deposit'];
        $paymentUrl = route('deposit.app.confirm', ['hash' => encrypt($deposit->id)]);

        return response()->json([
            'success' => true,
            'booking_id' => (string) $tourBooking->id,
            'payment_id' => (string) $deposit->id,
            'payment_url' => $paymentUrl,
            'redirect_url' => $paymentUrl,
            'amount' => (float) $deposit->final_amo,
            'booking_amount' => (float) $tourBooking->price,
            'currency' => strtoupper((string) $deposit->method_currency),
            'booking_currency' => $tourPackage->displayCurrencyCode(),
            'status' => 'PENDING',
            'message' => 'Tour package booking created successfully',
        ], 201);
    }

    private function resolveTourBookingGateway(Request $request): ?GatewayCurrency
    {
        $methodCode = $request->input('method_code');
        $currency = $request->input('currency');

        $query = GatewayCurrency::query()
            ->whereHas('method', function ($gate) {
                $gate->where('status', 1);
            })
            ->with('method')
            ->where('method_code', '<', 1000)
            ->whereRaw('LOWER(name) != ?', ['cash'])
            ->orderBy('method_code');

        if (filled($methodCode)) {
            $query->where('method_code', (int) $methodCode);
        }

        if (filled($currency)) {
            $query->whereRaw('UPPER(currency) = ?', [strtoupper((string) $currency)]);
        }

        return $query->first();
    }

    private function normalizeTourPackage(TourPackage $tourPackage): array
    {
        $category = $tourPackage->category ? $this->normalizeCategory($tourPackage->category) : null;
        $gallery = $this->normalizeGallery($tourPackage);
        $primaryImage = $gallery[0]['image_url'] ?? $this->normalizeImageUrl($tourPackage, optional($tourPackage->TourPackagePrimaryImage)->image);
        $descriptionRaw = (string) ($tourPackage->displayDescription() ?: $tourPackage->description ?: '');
        $descriptionPlain = $this->stripHtml($descriptionRaw);
        $destinationOverview = $this->normalizeObject($tourPackage->destination_overview);
        $location = [
            'latitude' => $this->toNullableString($tourPackage->latitude ?? null),
            'longitude' => $this->toNullableString($tourPackage->longitude ?? null),
            'city' => $this->toNullableString($tourPackage->city ?? null),
            'state' => $this->toNullableString($tourPackage->state ?? null),
            'country' => $this->toNullableString($tourPackage->country ?? null),
            'address' => $this->toNullableString($tourPackage->displayAddress() ?: $tourPackage->address ?: null),
            'zip_code' => $this->toNullableString($tourPackage->zip_code ?? null),
        ];

        $destination = array_filter([
            'city' => $location['city'],
            'state' => $location['state'],
            'country' => $location['country'],
            'address' => $location['address'],
            'cities_covered' => $this->toNullableString($tourPackage->displayCitiesCovered() ?: $tourPackage->cities_covered ?: null),
            'departure_from' => $this->toNullableString($tourPackage->displayDepartureFrom() ?: null),
            'arrival' => $this->toNullableString($tourPackage->displayArrival() ?: null),
            'transportation' => $this->toNullableString($tourPackage->displayTransportation() ?: null),
            'accommodation' => $this->toNullableString($tourPackage->displayAccommodation() ?: null),
            'tour_type' => $this->toNullableString($tourPackage->displayTourType() ?: null),
            'package_label' => $this->toNullableString($tourPackage->displayPackageLabel() ?: null),
            'overview' => $destinationOverview,
        ], static fn ($value) => $value !== null && $value !== '' && $value !== []);

        $features = $this->normalizeFeatureRows($tourPackage->localizedFeatures());
        $highlights = $this->normalizeTextList($tourPackage->localizedHighlights());
        $includes = $this->normalizeTextList($tourPackage->localizedIncludes());
        $excludes = $this->normalizeTextList($tourPackage->localizedExcludes());
        $itinerary = $this->normalizeItinerary($tourPackage->localizedItineraryDays(), $tourPackage);
        $icon = $this->resolveIcon($tourPackage, $features);

        return [
            'id' => (string) $tourPackage->id,
            'title' => $this->displayText($tourPackage->displayTitle(), $tourPackage->title),
            'title_en' => (string) ($tourPackage->getRawOriginal('title') ?? ''),
            'title_ar' => (string) ($tourPackage->getRawOriginal('title_ar') ?? ''),
            'description' => $descriptionPlain,
            'description_raw' => $descriptionRaw,
            'description_plain' => $descriptionPlain,
            'description_en' => (string) ($tourPackage->getRawOriginal('description') ?? ''),
            'description_ar' => (string) ($tourPackage->getRawOriginal('description_ar') ?? ''),
            'image_url' => $primaryImage,
            'gallery' => $gallery,
            'icon' => $icon,
            'icon_class' => $icon,
            'icons' => $this->normalizeObject($tourPackage->icons),
            'price' => (float) ($tourPackage->price_from ?: $tourPackage->price ?: 0),
            'original_price' => (float) ($tourPackage->price_to ?: $tourPackage->price ?: $tourPackage->price_from ?: 0),
            'discounted_price' => (float) ($tourPackage->price_from ?: $tourPackage->price ?: 0),
            'currency' => $tourPackage->displayCurrencyCode(),
            'duration_days' => $this->durationDays($tourPackage->day_nights),
            'duration_nights' => $this->durationNights($tourPackage->day_nights),
            'category' => $category,
            'destination' => $destination,
            'location' => $location,
            'highlights' => $highlights,
            'features' => $features,
            'includes' => $includes,
            'excludes' => $excludes,
            'itinerary' => $itinerary,
            'status' => $this->normalizeStatus($tourPackage->status),
            'is_featured' => false,
            'raw' => [
                'price_note' => $this->toNullableString($tourPackage->price_note ?? null),
                'day_nights' => $this->toNullableString($tourPackage->day_nights ?? null),
                'tour_start' => $this->toNullableString($tourPackage->tour_start ?? null),
                'tour_end' => $this->toNullableString($tourPackage->tour_end ?? null),
                'flexible_date' => $tourPackage->flexible_date,
            ],
        ];
    }

    private function normalizeGallery(TourPackage $tourPackage): array
    {
        return $tourPackage->tour_package_images
            ?->values()
            ?->map(function ($image, $index) use ($tourPackage) {
                $filename = (string) ($image->image ?? '');

                return [
                    'id' => (string) ($image->id ?? $index),
                    'image' => $filename,
                    'image_url' => $this->normalizeImageUrl($tourPackage, $filename),
                    'is_primary' => $index === 0,
                ];
            })
            ?->filter(fn ($item) => ! empty($item['image_url']))
            ?->values()
            ?->all() ?? [];
    }

    private function normalizeCategory($category): array
    {
        return [
            'id' => (string) ($category->id ?? ''),
            'name' => (string) ($category->name ?? $category->name_en ?? $category->getRawOriginal('name') ?? ''),
            'name_en' => (string) ($category->getRawOriginal('name') ?? $category->name ?? ''),
            'name_ar' => (string) ($category->name_ar ?? $category->name ?? ''),
            'slug' => (string) ($category->slug ?? $this->slugify((string) ($category->getRawOriginal('name') ?? $category->name ?? ''))),
            'icon' => $this->toNullableString($category->icon ?? null),
            'icon_class' => $this->toNullableString($category->icon_class ?? null),
        ];
    }

    private function normalizeFeatureRows(array $rows): array
    {
        return collect($rows)
            ->map(function ($item) {
                $item = (object) $item;

                return [
                    'icon' => $this->toNullableString($item->icon ?? null),
                    'feature' => $this->toNullableString($item->feature ?? null),
                ];
            })
            ->filter(fn ($item) => filled($item['icon']) || filled($item['feature']))
            ->values()
            ->all();
    }

    private function normalizeTextList(array $items): array
    {
        return collect($items)
            ->map(function ($item) {
                if (is_array($item)) {
                    return $this->toNullableString($item['title'] ?? $item['feature'] ?? $item['description'] ?? $item['text'] ?? $item['value'] ?? null);
                }

                if (is_object($item)) {
                    return $this->toNullableString($item->title ?? $item->feature ?? $item->description ?? $item->text ?? $item->value ?? null);
                }

                return $this->toNullableString($item);
            })
            ->filter(fn ($value) => filled($value))
            ->values()
            ->all();
    }

    private function normalizeItinerary(array $days, TourPackage $tourPackage): array
    {
        return collect($days)
            ->map(function ($item) use ($tourPackage) {
                $day = (array) $item;
                $image = $this->normalizeImageUrl($tourPackage, $day['image'] ?? null);

                return [
                    'day_number' => $this->toNullableString($day['day_number'] ?? null),
                    'title' => $this->displayText($day['title'] ?? $day['title_en'] ?? null, $day['title_ar'] ?? null),
                    'description' => $this->stripHtml($day['description'] ?? $day['description_en'] ?? null),
                    'description_raw' => $this->toNullableString($day['description'] ?? null),
                    'image' => $day['image'] ?? null,
                    'image_url' => $image,
                ];
            })
            ->filter(fn ($item) => filled($item['day_number']) || filled($item['title']) || filled($item['description']) || filled($item['image_url']))
            ->values()
            ->all();
    }

    private function resolveIcon(TourPackage $tourPackage, array $features): string
    {
        $iconFromFeature = collect($features)->pluck('icon')->first(fn ($value) => filled($value));

        if (filled($iconFromFeature)) {
            return (string) $iconFromFeature;
        }

        $icons = $this->normalizeObject($tourPackage->icons);
        $firstIcon = $icons[0] ?? null;

        if (is_array($firstIcon)) {
            $firstIcon = $firstIcon['icon'] ?? $firstIcon['icon_class'] ?? $firstIcon['value'] ?? null;
        } elseif (is_object($firstIcon)) {
            $firstIcon = $firstIcon->icon ?? $firstIcon->icon_class ?? $firstIcon->value ?? null;
        }

        if (filled($firstIcon)) {
            return (string) $firstIcon;
        }

        $categoryName = (string) ($tourPackage->category?->getRawOriginal('name') ?? $tourPackage->category?->name ?? '');
        return $this->slugify($categoryName) ?: 'pricetag-outline';
    }

    private function normalizeImageUrl(TourPackage $tourPackage, ?string $image): ?string
    {
        $image = trim((string) $image);

        if ($image === '') {
            return null;
        }

        return $tourPackage->itineraryImageUrl($image);
    }

    private function normalizeObject(mixed $value): array
    {
        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->toArray();
        }

        if ($value instanceof \stdClass) {
            $value = (array) $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
            return [];
        }

        return is_array($value) ? $value : [];
    }

    private function stripHtml(?string $value): string
    {
        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        $text = preg_replace('/<br\s*\/?>/i', "\n", $value) ?? $value;
        $text = preg_replace('/<\/p\s*>/i', "\n", $text) ?? $text;
        $text = strip_tags($text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/', ' ', $text) ?? $text);
    }

    private function displayText(?string $primary, ?string $fallback = null): string
    {
        $primary = $this->toNullableString($primary);
        if (filled($primary)) {
            return $primary;
        }

        return $this->toNullableString($fallback) ?? '';
    }

    private function toNullableString(mixed $value): ?string
    {
        if (is_array($value) || is_object($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function durationDays(?string $dayNights): ?int
    {
        if (! filled($dayNights)) {
            return null;
        }

        if (preg_match('/(\d+)\s*day/i', $dayNights, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }

    private function durationNights(?string $dayNights): ?int
    {
        if (! filled($dayNights)) {
            return null;
        }

        if (preg_match('/(\d+)\s*night/i', $dayNights, $matches)) {
            return (int) $matches[1];
        }

        $days = $this->durationDays($dayNights);

        return $days !== null ? max($days - 1, 0) : null;
    }

    private function normalizeStatus(mixed $status): string
    {
        return match ((string) $status) {
            '0' => 'PENDING',
            '1' => 'ACTIVE',
            '2' => 'RUNNING',
            '3' => 'EXPIRED',
            '4' => 'CANCELLED',
            default => 'ACTIVE',
        };
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;

        return trim($value, '-') ?: '';
    }
}
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TourBooking;
use App\Models\ServiceBooking;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ServiceBookingController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Tour Package Bookings');
        $users = User::orderBy('username')->get(['id', 'username', 'firstname', 'lastname']);
        $search = trim((string) $request->input('search', ''));
        $filter = $this->normalizeFilter($request->input('filter', 'all'));
        $sort = $this->normalizeSort($request->input('sort', 'dates'));
        $direction = $this->normalizeDirection($request->input('direction', 'desc'));

        $tourBookingsQuery = TourBooking::with(['user', 'tour_package'])->latest('id');
        $serviceBookingsQuery = ServiceBooking::with('user')->latest('id');

        if ($search !== '') {
            $this->applyTourBookingSearch($tourBookingsQuery, $search);
            $this->applyServiceBookingSearch($serviceBookingsQuery, $search);
        }

        $bookings = $this->buildCombinedBookings($tourBookingsQuery->get(), $serviceBookingsQuery->get());
        $bookings = $this->applyFilter($bookings, $filter);
        $bookings = $this->sortBookings($bookings, $sort, $direction);

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = getPaginate();
        $currentItems = $bookings->slice(($page - 1) * $perPage, $perPage)->values();

        $bookings = new LengthAwarePaginator($currentItems, $bookings->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('admin.service_booking.index', compact('pageTitle', 'bookings', 'users', 'search', 'filter', 'sort', 'direction'));
    }

    protected function buildCombinedBookings(Collection $tourBookings, Collection $serviceBookings): Collection
    {
        $tourRows = $tourBookings->map(function ($booking) {
            return (object) [
                'id' => $booking->id,
                'source' => 'tour',
                'legacy_import' => false,
                'legacy_source' => null,
                'user' => $booking->user,
                'booking_type' => 'tour',
                'display_type' => __('Tour Package'),
                'title' => $booking->tour_package?->title ?? __('Tour Booking'),
                'reference_no' => $booking->reference_no,
                'booking_date' => $booking->created_at,
                'service_date' => $booking->tour_package?->tour_start,
                'service_end_date' => null,
                'date_primary' => $booking->tour_package?->tour_start ?? $booking->created_at,
                'amount' => $booking->price,
                'status' => $booking->status,
                'notes' => null,
                'review_flags' => null,
                'status_html' => $booking->statusBadge($booking->status),
                'detail_url' => route('admin.tour.package.booking.details', $booking->id),
                'sort_date' => $booking->created_at,
                'is_additional_legacy' => false,
                'is_future_dated' => $this->hasFutureDate($booking->created_at, $booking->tour_package?->tour_start, null),
                'legacy_review_state' => null,
            ];
        });

        $serviceRows = $serviceBookings->map(function ($booking) {
            return (object) [
                'id' => $booking->id,
                'source' => 'service',
                'legacy_import' => (bool) $booking->legacy_import,
                'legacy_source' => $booking->legacy_source,
                'user' => $booking->user,
                'booking_type' => $booking->booking_type,
                'display_type' => $this->resolveServiceBookingTypeLabel((string) $booking->booking_type),
                'title' => $booking->title,
                'reference_no' => $booking->reference_no,
                'booking_date' => $booking->booking_date ?? $booking->created_at,
                'service_date' => $booking->service_date,
                'service_end_date' => $booking->service_end_date,
                'date_primary' => $booking->booking_date ?? $booking->created_at,
                'amount' => $booking->amount,
                'status' => $booking->status,
                'notes' => $booking->notes,
                'review_flags' => $booking->review_flags,
                'status_html' => $booking->statusBadge(),
                'detail_url' => null,
                'sort_date' => $booking->booking_date ?? $booking->created_at,
                'is_additional_legacy' => (bool) ($booking->legacy_import && $booking->legacy_source === 'additional_employee_excel_bookings'),
                'is_future_dated' => $this->hasFutureDate($booking->booking_date ?? $booking->created_at, $booking->service_date, $booking->service_end_date),
                'legacy_review_state' => $this->resolveLegacyReviewState($booking),
            ];
        });

        return $tourRows->toBase()->concat($serviceRows->toBase())->values();
    }

    protected function applyTourBookingSearch($query, string $search): void
    {
        $term = mb_strtolower($search);

        $query->where(function ($bookingQuery) use ($term) {
            $bookingQuery->whereHas('user', function ($userQuery) use ($term) {
                $userQuery->whereRaw('LOWER(username) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(firstname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
            })
            ->orWhereHas('tour_package', function ($packageQuery) use ($term) {
                $packageQuery->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"]);
            })
            ->orWhereRaw('LOWER(reference_no) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(CAST(price AS CHAR)) LIKE ?', ["%{$term}%"])
            ->orWhere(function ($statusQuery) use ($term) {
                $this->applyStatusSearch($statusQuery, $term, 'status');
            });
        });
    }

    protected function applyServiceBookingSearch($query, string $search): void
    {
        $term = mb_strtolower($search);

        $query->where(function ($bookingQuery) use ($term) {
            $bookingQuery->whereHas('user', function ($userQuery) use ($term) {
                $userQuery->whereRaw('LOWER(username) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(firstname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(lastname) LIKE ?', ["%{$term}%"])
                    ->orWhereRaw('LOWER(email) LIKE ?', ["%{$term}%"]);
            })
            ->orWhereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(reference_no) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(booking_type) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(CAST(amount AS CHAR)) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(COALESCE(legacy_booking_id, "")) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(COALESCE(legacy_order_id, "")) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(COALESCE(legacy_order_item_id, "")) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(COALESCE(legacy_booking_obj_id, "")) LIKE ?', ["%{$term}%"])
            ->orWhereRaw('LOWER(COALESCE(legacy_source, "")) LIKE ?', ["%{$term}%"])
            ->orWhere(function ($statusQuery) use ($term) {
                $this->applyStatusSearch($statusQuery, $term, 'status');
            });
        });
    }

    protected function applyStatusSearch($query, string $term, string $column): void
    {
        $statusTerms = [
            'pending' => [0],
            'approved' => [1],
            'confirmed' => [1],
            'completed' => [2],
            'canceled' => [3],
            'cancelled' => [3],
        ];

        $matchedStatuses = [];

        foreach ($statusTerms as $needle => $values) {
            if (str_contains($term, $needle)) {
                $matchedStatuses = array_merge($matchedStatuses, $values);
            }
        }

        if (empty($matchedStatuses)) {
            $query->whereRaw('1 = 0');

            return;
        }

        $query->whereIn($column, array_values(array_unique($matchedStatuses)));
    }

    protected function normalizeSort(?string $sort): string
    {
        return in_array($sort, ['client', 'type', 'title', 'reference', 'dates', 'amount', 'status'], true) ? $sort : 'dates';
    }

    protected function normalizeDirection(?string $direction): string
    {
        return $direction === 'asc' ? 'asc' : 'desc';
    }

    protected function normalizeFilter(?string $filter): string
    {
        return in_array($filter, ['all', 'additional_legacy', 'future_dated', 'manual_review_excluded', 'repaired_legacy'], true)
            ? $filter
            : 'all';
    }

    protected function applyFilter(Collection $bookings, string $filter): Collection
    {
        $filtered = match ($filter) {
            'additional_legacy' => $bookings->filter(fn ($booking) => $booking->is_additional_legacy),
            'future_dated' => $bookings->filter(fn ($booking) => $booking->is_additional_legacy && $booking->is_future_dated),
            'manual_review_excluded' => $bookings->filter(fn ($booking) => in_array((string) $booking->reference_no, ['LEGACY-XLS2-0211', 'LEGACY-XLS2-0212'], true)),
            'repaired_legacy' => $bookings->filter(fn ($booking) => in_array((string) $booking->reference_no, ['LEGACY-XLS2-0020', 'LEGACY-XLS2-0033', 'LEGACY-XLS2-0061', 'LEGACY-XLS2-0115', 'LEGACY-XLS2-0281', 'LEGACY-XLS2-0304', 'LEGACY-XLS2-0311', 'LEGACY-XLS2-0312', 'LEGACY-XLS2-0313', 'LEGACY-XLS2-0314'], true)),
            default => $bookings,
        };

        return $filtered->values();
    }

    protected function sortBookings(Collection $bookings, string $sort, string $direction): Collection
    {
        $descending = $direction === 'desc';

        return $bookings->sortBy(function ($booking) use ($sort) {
            return match ($sort) {
                'client' => mb_strtolower((string) ($booking->user?->username ?? '')),
                'type' => mb_strtolower((string) ($booking->display_type ?? $booking->booking_type ?? '')),
                'title' => mb_strtolower((string) ($booking->title ?? '')),
                'reference' => mb_strtolower((string) ($booking->reference_no ?? '')),
                'amount' => (float) ($booking->amount ?? 0),
                'status' => (int) ($booking->status ?? 0),
                default => $booking->sort_date ? strtotime((string) $booking->sort_date) : 0,
            };
        }, SORT_REGULAR, $descending)->values();
    }

    protected function resolveServiceBookingTypeLabel(string $bookingType): string
    {
        return match ($bookingType) {
            'tour' => __('Tour Package'),
            'flight' => __('Flight'),
            'stay', 'hotel' => __('Stay / Accommodation'),
            'transportation' => __('Transportation'),
            'coupon' => __('Discount Coupon'),
            'restaurant' => __('Restaurant'),
            'cafe' => __('Cafe'),
            default => __(str_replace('_', ' ', ucfirst($bookingType))),
        };
    }

    protected function hasFutureDate(mixed ...$dates): bool
    {
        $today = now()->startOfDay();

        foreach ($dates as $date) {
            if ($date && method_exists($date, 'copy') && $date->copy()->startOfDay()->gt($today)) {
                return true;
            }
        }

        return false;
    }

    protected function resolveLegacyReviewState(object $booking): ?string
    {
        if (! ($booking->legacy_import && $booking->legacy_source === 'additional_employee_excel_bookings')) {
            return null;
        }

        $referenceNo = (string) ($booking->reference_no ?? '');

        if (in_array($referenceNo, ['LEGACY-XLS2-0211', 'LEGACY-XLS2-0212'], true)) {
            return 'manual_review_excluded';
        }

        if (in_array($referenceNo, ['LEGACY-XLS2-0020', 'LEGACY-XLS2-0033', 'LEGACY-XLS2-0061', 'LEGACY-XLS2-0115', 'LEGACY-XLS2-0281', 'LEGACY-XLS2-0304', 'LEGACY-XLS2-0311', 'LEGACY-XLS2-0312', 'LEGACY-XLS2-0313', 'LEGACY-XLS2-0314'], true)) {
            return 'repaired_legacy';
        }

        return 'additional_legacy';
    }

    public function create()
    {
        $pageTitle = __('Add Tour Package Booking');
        $users = User::orderBy('username')->get(['id', 'username', 'firstname', 'lastname', 'balance']);

        return view('admin.service_booking.create', compact('pageTitle', 'users'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_type' => 'required|in:tour,flight,stay,hotel,transportation,coupon,restaurant,cafe',
            'title' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:120',
            'booking_date' => 'nullable|date',
            'service_date' => 'nullable|date',
            'service_end_date' => 'nullable|date|after_or_equal:service_date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2,3',
            'notes' => 'nullable|string',
        ]);

        $booking = ServiceBooking::findOrFail($id);
        $booking->update($request->only([
            'user_id',
            'booking_type',
            'title',
            'reference_no',
            'booking_date',
            'service_date',
            'service_end_date',
            'amount',
            'status',
            'notes',
        ]));

        $notify[] = ['success', __('Booking updated successfully')];
        return back()->withNotify($notify);
    }

    public function destroy($id)
    {
        ServiceBooking::findOrFail($id)->delete();

        $notify[] = ['success', __('Booking deleted successfully')];
        return back()->withNotify($notify);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_type' => 'required|in:tour,flight,stay,hotel,transportation,coupon,restaurant,cafe',
            'title' => 'required|string|max:255',
            'reference_no' => 'nullable|string|max:120',
            'booking_date' => 'nullable|date',
            'service_date' => 'nullable|date',
            'service_end_date' => 'nullable|date|after_or_equal:service_date',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:0,1,2,3',
            'notes' => 'nullable|string',
        ]);

        $booking = ServiceBooking::create([
            'user_id' => $request->user_id,
            'created_by_admin_id' => auth('admin')->id(),
            'booking_type' => $request->booking_type,
            'title' => $request->title,
            'reference_no' => $request->reference_no,
            'booking_date' => $request->booking_date,
            'service_date' => $request->service_date,
            'service_end_date' => $request->service_end_date,
            'amount' => $request->amount,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        if ($request->status == 1 && $request->has('deduct_wallet')) {
            $user = User::find($request->user_id);
            if ($user && $user->balance >= $request->amount) {
                $user->balance -= $request->amount;
                $user->save();

                $transaction = new \App\Models\Transaction();
                $transaction->user_id = $user->id;
                $transaction->amount = $request->amount;
                $transaction->post_balance = $user->balance;
                $transaction->charge = 0;
                $transaction->trx_type = '-';
                $transaction->remark = 'booking_payment';
                $transaction->details = 'Payment for booking: ' . $request->title;
                $transaction->trx = getTrx();
                $transaction->save();
            }
        }

        $notify[] = ['success', __('Booking added successfully')];
        return back()->withNotify($notify);
    }

    public function hotelList()
    {
        $pageTitle = __('Hotel Booking Requests');
        $bookings = ServiceBooking::with('user')->where('booking_type', 'hotel')->latest()->paginate(getPaginate());
        return view('admin.service_booking.list', compact('pageTitle', 'bookings'));
    }

    public function flightList()
    {
        $pageTitle = __('Flight Booking Requests');
        $bookings = ServiceBooking::with('user')->where('booking_type', 'flight')->latest()->paginate(getPaginate());
        return view('admin.service_booking.list', compact('pageTitle', 'bookings'));
    }

    public function transportList()
    {
        $pageTitle = __('Transportation Booking Requests');
        $bookings = ServiceBooking::with('user')->where('booking_type', 'transportation')->latest()->paginate(getPaginate());
        return view('admin.service_booking.list', compact('pageTitle', 'bookings'));
    }

    public function statusUpdate(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3', // 0: Pending, 1: Approved, 2: Completed, 3: Rejected
        ]);

        $booking = ServiceBooking::findOrFail($id);
        $booking->status = $request->status;
        $booking->save();

        $notify[] = ['success', __('Booking status updated successfully')];
        return back()->withNotify($notify);
    }
}

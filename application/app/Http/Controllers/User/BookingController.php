<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\TourBooking;
use App\Models\TourPackage;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Pagination\LengthAwarePaginator;

class BookingController extends Controller
{
    public function allBookings()
    {
        $pageTitle = 'Booking List';
        $user = auth()->user();
        $search = request()->search;

        $tourBookings = TourBooking::with('tour_package')
            ->where('user_id', $user->id)
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'id' => $booking->id,
                    'kind_label' => 'Tour',
                    'booking_type' => 'tour',
                    'reference_no' => $booking->reference_no,
                    'title' => $booking->tour_package?->title ?? __('Tour Booking'),
                    'booking_date' => $booking->created_at,
                    'service_date' => $booking->tour_package?->tour_start,
                    'end_date' => null,
                    'amount' => $booking->price,
                    'status' => $booking->status,
                    'status_html' => $booking->statusBadge($booking->status),
                    'details_url' => route('user.tour.package.booking.details', $booking->id),
                    'legacy_import' => false,
                ];
            });

        $serviceBookings = ServiceBooking::where('user_id', $user->id)
            ->get()
            ->map(function ($booking) {
                return (object) [
                    'id' => $booking->id,
                    'kind_label' => str_replace('_', ' ', ucfirst((string)$booking->booking_type)),
                    'booking_type' => $booking->booking_type,
                    'reference_no' => $booking->reference_no,
                    'title' => $booking->title,
                    'booking_date' => $booking->booking_date ?? $booking->created_at,
                    'service_date' => $booking->service_date,
                    'end_date' => $booking->service_end_date,
                    'amount' => $booking->amount,
                    'status' => $booking->status,
                    'status_html' => $booking->statusBadge(),
                    'details_url' => route('user.service.booking.details', $booking->id),
                    'legacy_import' => (bool)$booking->legacy_import,
                ];
            });

        $bookings = $tourBookings->toBase()->concat($serviceBookings->toBase());

        if ($search) {
            $bookings = $bookings->filter(function ($booking) use ($search) {
                return str_contains(strtolower($booking->title), strtolower($search))
                    || str_contains(strtolower($booking->kind_label), strtolower($search))
                    || str_contains(strtolower((string) $booking->reference_no), strtolower($search));
            });
        }

        $bookings = $bookings->sortByDesc(function ($booking) {
            return $booking->booking_date ? strtotime($booking->booking_date) : 0;
        })->values();

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = getPaginate();
        $currentItems = $bookings->slice(($page - 1) * $perPage, $perPage)->values();

        $bookings = new LengthAwarePaginator($currentItems, $bookings->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view($this->activeTemplate . 'user.booking_overview.my_booking', compact('pageTitle', 'bookings', 'search'));
    }

    public function bookingNow(Request $request)
    {
        $pageTitle = 'Tour Booking Payment';
        // Step 1: First validate tour_package_id only
        $request->validate([
            'tour_package_id' => 'required|numeric|exists:tour_packages,id',
        ]);

        // Step 2: Now safely get the TourPackage
        $tourPackage = TourPackage::findOrFail($request->tour_package_id);

        // Step 3: Then validate the rest
        $request->validate([
            'tour_package_id' => 'required|numeric|exists:tour_packages,id',
            'seat' => 'required|numeric',
        ]);

        if ($tourPackage->flexible_date == 1 && $request->user_proposal_date) {
            $userProposalDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->user_proposal_date);
            if ($userProposalDate->lt(now())) {
                $notify[] = ['error', 'Proposed date must be today or a future date.'];
                return back()->withNotify($notify);
            }
        }

        if (auth('agent')->user()) {
            $notify[] = ['error', 'Agent is not booking'];
            return back()->withNotify($notify);
        };
        
        // tour end time check
        if ($tourPackage->tour_end < now()) {
            $notify[] = ['error', "Tour package is expired"];
            return back()->withNotify($notify);
        }

        // Seat availability check
        if ($tourPackage->person_capability <= $tourPackage->booking_person) {
            $notify[] = ['error', "Seats are not available for this tour package. Available Seat"];
            return back()->withNotify($notify);
        }

        // Seat availability check plus requests seat
        if ($tourPackage->person_capability < $tourPackage->booking_person + $request->seat) {
            $notify[] = ['warning', "Seats are not available for this tour package. Available seat is " . $tourPackage->person_capability - $tourPackage->booking_person];
            return back()->withNotify($notify);
        }

        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();

        $bookingAmount = showTourPackageCalculateDiscount($tourPackage->price * $request->seat, $tourPackage->discount);
        $cashbackUsed = 0;
        if ($request->boolean('use_cashback')) {
            $cashbackUsed = min(auth()->user()->cashback_balance, $bookingAmount);
        }

        Session::put('tourPackageSession', [
            'tour_package_id' => $request->tour_package_id,
            'user_proposal_date' => $userProposalDate ?? $tourPackage->tour_start,
            'seat' => $request->seat,
            'cashback_used' => $cashbackUsed,
            'booking_amount' => $bookingAmount,
        ]);

        $seat = $request->seat;
        $payableAmount = $bookingAmount - $cashbackUsed;
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle', 'tourPackage', 'seat', 'payableAmount', 'cashbackUsed'));
    }

    public function bookingList(Request $request)
    {
        $pageTitle = 'Tour Packages';
        $tourBookingList = $this->tourPackageData('userAll');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }


    public function bookingDetails($id)
    {

        $pageTitle = 'Tour & Booking Details';
        $bookingDetails = TourBooking::with(['user', 'owner', 'admin', 'tour_package', 'tour_package.category'])
            ->where('id', $id)
            ->where('user_id', auth()->id())
            ->first();
        return view($this->activeTemplate . 'user.tour_booking.details', compact('pageTitle', 'bookingDetails'));
    }


    public function pending()
    {
        $pageTitle = 'User Pending Booking-List';
        $tourBookingList = $this->tourPackageData('userPending');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function approved()
    {
        $pageTitle = 'User Approved Booking-List';
        $tourBookingList = $this->tourPackageData('userApproved');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function canceled()
    {
        $pageTitle = 'User Canceled Booking-List';
        $tourBookingList = $this->tourPackageData('userCanceled');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    public function bookingAgentList()
    {
        $pageTitle = 'Agent Booking-List';
        $tourBookingList = $this->tourPackageData('agent');
        return view($this->activeTemplate . 'user.tour_booking.my_booking', compact('pageTitle', 'tourBookingList'));
    }

    protected function tourPackageData($scope = null)
    {
        if ($scope) {
            $tourBooking = TourBooking::$scope();
        } else {
            $TourBooking = TourBooking::query();
        }
        //search
        $request = request();
        if ($request->search) {
            $search = $request->search;
            $tourBooking = $tourBooking->with('tour_package', 'deposit')
                ->where(function ($query) use ($search) {
                    $query->whereHas('tour_package', function ($tourPackageQuery) use ($search) {
                        $tourPackageQuery->where('title', 'like', "%$search%");
                    })->orWhere('reference_no', 'like', "%$search%");
                });
        }
        return $tourBooking->with('deposit', 'user', 'tour_package.TourPackagePrimaryImage')->orderBy('id', 'desc')->paginate(getPaginate());
    }
}

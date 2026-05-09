<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ServiceBooking;
use Illuminate\Http\Request;

class ServiceBookingController extends Controller
{
    public function myList(Request $request, $type = null)
    {
        $allowedTypes = ['tour', 'flight', 'stay', 'transportation', 'coupon', 'restaurant', 'cafe'];

        if ($type && ! in_array($type, $allowedTypes, true)) {
            abort(404);
        }

        $pageTitle = $type ? ucfirst(str_replace('_', ' ', $type)) . ' Bookings' : 'My Bookings';
        $query = ServiceBooking::with('admin')->where('user_id', auth()->id());

        if ($type) {
            $query->where('booking_type', $type);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($bookingQuery) use ($search) {
                $bookingQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('reference_no', 'like', "%{$search}%");
            });
        }

        $serviceBookings = $query->latest()->paginate(getPaginate());

        return view($this->activeTemplate . 'user.service_booking.my_booking', compact('pageTitle', 'serviceBookings', 'type', 'allowedTypes'));
    }

    public function details($id)
    {
        $pageTitle = 'Service Booking Details';
        $bookingDetails = ServiceBooking::with('admin')
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        return view($this->activeTemplate . 'user.service_booking.details', compact('pageTitle', 'bookingDetails'));
    }
}

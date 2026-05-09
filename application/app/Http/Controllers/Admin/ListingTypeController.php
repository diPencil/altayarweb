<?php

namespace App\Http\Controllers\Admin;

use App\Models\ListingType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListingTypeController extends Controller
{
    public function index()
    {
        $pageTitle = __('Listing Type List');
        $listingTypes = ListingType::getSearch(['name'])->latest()->paginate(getPaginate());
        return view('admin.listing_type.index', compact('pageTitle', 'listingTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
        ]);

        $listingType = new ListingType();
        $listingType->name = $request->name;
        $listingType->name_ar = $request->name_ar;
        $listingType->save();

        $notify[] = ['success', __('Listing type added successfully')];
        return back()->withNotify($notify);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:listing_types,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'status' => 'required|in:0,1',
        ]);

        $listingType = ListingType::findOrFail($request->id);
        $listingType->name = $request->name;
        $listingType->name_ar = $request->name_ar;
        $listingType->status = $request->status;
        $listingType->save();

        $notify[] = ['success', __('Listing type updated successfully')];
        return back()->withNotify($notify);
    }

    public function statusChange($id)
    {
        $listingType = ListingType::findOrFail($id);
        $listingType->status = $listingType->status == 1 ? 0 : 1;
        $listingType->save();

        $notify[] = ['success', __('Status changed successfully')];
        return back()->withNotify($notify);
    }
}

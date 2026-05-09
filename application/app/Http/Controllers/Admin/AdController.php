<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AdController extends Controller
{
    public function getAdvertise(Request $request, $ad, $type, $adType): RedirectResponse
    {
        return redirect()->route('home');
    }

    public function adClicked(Request $request, $adid): RedirectResponse
    {
        return redirect()->route('home');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\TourPackage;
use App\Traits\TourService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TourPackageController extends Controller
{
    use TourService;
    protected function tourPackageTabCounts()
    {
        return [
            'allTourPackages' => TourPackage::count(),
            'myTourPackages' => TourPackage::where('user_type', 'admin')->where('user_id', auth('admin')->id())->count(),
            'allAgentTourPackages' => TourPackage::where('user_type', 'agent')->count(),
            'allCancelledTourPackages' => TourPackage::where('status', 4)->count(),
        ];
    }

    public function index()
    {
      
        $pageTitle = __('Tour Package Lists');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('allTour');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function create()
    {
        $pageTitle = __('Create Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        return view('admin.tour_package.create', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories')));
    }

    public function edit($id)
    {
        $pageTitle = __('Tour Package Edit');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackage =  TourPackage::with('category')->where('id',$id)->first();
        return view('admin.tour_package.edit', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackage')));
    }

    public function active()
    {
        $pageTitle = __('Pending Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('active');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }


    public function pending()
    {
        $pageTitle = __('Pending Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('pending');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function cancelled()
    {
        $pageTitle = __('Cancelled Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('cancelled');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function running()
    {
        $pageTitle = __('Running Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('running');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function expired()
    {
        $pageTitle = __('Expired Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('expired');
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function allAgent(Request $request)
    {
        $pageTitle = __('Agent Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = TourPackage::with('category','TourPackagePrimaryImage','agent')
        ->where('user_type','agent')
        ->orderBy('id', 'desc');
        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }

        $tourPackages = $tourPackages->paginate(getPaginate());
 
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

    public function allemployee(Request $request)
    {
        return $this->allAgent($request);
    }

    public function statusChange($id){
        $tourPackage = TourPackage::where('id',$id)->where('user_id',auth('admin')->id())->where('user_type','admin')->first();
    
        $tourPackage->status = $tourPackage->status == 1 ? 0 : 1;
        $tourPackage->save();
        $notify[] = ['success', __('Status change has been successfully')];
        return back()->withNotify($notify);
    }

    public function myList(Request $request)
    {
        $pageTitle = __('My Tour Package');
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = TourPackage::with('category','TourPackagePrimaryImage','agent')
        ->where('user_type','admin')
        ->where('user_id',auth('admin')->id())
        ->orderBy('id', 'desc');

        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }

        $tourPackages = $tourPackages->paginate(getPaginate());
        return view('admin.tour_package.index', array_merge($this->tourPackageTabCounts(), compact('pageTitle', 'categories', 'tourPackages')));
    }

 
    protected function tourPackageData($scope = null)
    {
        if ($scope) {
            $tourPackages = TourPackage::$scope();
        } else {
            $tourPackages = TourPackage::query();
        }
        //search
        $request = request();
        if ($request->search || $request->category_id) {

            $search = $request->search;
            $categoryId = $request->category_id;
            $tourPackages = $tourPackages->where(function ($query) use ($search, $categoryId) {
                if ($categoryId) {
                    $query->where('category_id', $categoryId);
                } if ($search) {
                    $query->where('title', 'like', "%$search%");
                }
            });
        }
        return $tourPackages->with('category','agent','TourPackagePrimaryImage','agent')->orderBy('id', 'desc')->paginate(getPaginate());
    }
  
}

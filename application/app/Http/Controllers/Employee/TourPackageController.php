<?php

namespace App\Http\Controllers\Employee;

use App\Models\Category;
use App\Models\TourPackage;
use App\Traits\TourService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TourPackageController extends Controller
{
    use TourService;


    public function myList()
    {
        $pageTitle = 'My Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('EmployeeAll');
        return view($this->activeTemplate . 'Employee.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }
   
    public function create()
    {
        $pageTitle = 'Create Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        return view($this->activeTemplate . 'Employee.tour_package.create', compact('pageTitle', 'categories'));
    }

    public function edit($id)
    {
       
        $pageTitle = 'Tour Package Edit';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackage =  TourPackage::with('category')->where('user_type','Employee')->where('user_id',auth('employee')->id())->first();
        if(!$tourPackage){
            $notify[] = ['error', 'Your tour package id is not valid'];
            return back()->withNotify($notify);
        }
        return view($this->activeTemplate . 'Employee.tour_package.edit', compact('pageTitle', 'categories', 'tourPackage'));
    }

    public function active()
    {
        $pageTitle = 'Active Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('active');
        return view($this->activeTemplate . 'Employee.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function pending()
    {
        $pageTitle = 'Pending Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('pending');
        return view($this->activeTemplate . 'Employee.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function running()
    {
        $pageTitle = 'Running Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('running');
        return view($this->activeTemplate . 'Employee.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function expired()
    {
        $pageTitle = 'Expired Tour Package';
        $categories = Category::where('status', 1)->latest()->get();
        $tourPackages = $this->tourPackageData('expired');
        return view($this->activeTemplate . 'Employee.tour_package.index', compact('pageTitle', 'categories', 'tourPackages'));
    }

    public function statusChange($id){
        $tourPackages = TourPackage::where('id',$id)->where('user_id',auth('employee')->id())->where('user_type','Employee')->first();
        $tourPackages->status = $tourPackages->status == 1 ? 0 : 1;
        $tourPackages->save();
        $notify[] = ['success', 'Status change has been successfully'];
        return back()->withNotify($notify);
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
        return $tourPackages->with('category','TourPackagePrimaryImage','Employee')
        ->where('user_type','Employee')
        ->where('user_id',auth('employee')->id())
        ->orderBy('id', 'desc')
        ->paginate(getPaginate());
    }
  
}

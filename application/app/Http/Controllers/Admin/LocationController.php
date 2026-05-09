<?php

namespace App\Http\Controllers\Admin;

use App\Models\Location;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class LocationController extends Controller
{
    public function index(){
        $pageTitle = __('Locations');
        $locations = Location::latest()->get();
        return view('admin.location.index',compact('pageTitle','locations'));
    }

    public function create(){
        $pageTitle = __('Locations');
        return view('admin.location.create',compact('pageTitle'));
    }

    public function store(Request $request){
   
        $request->validate([
            'name'=>'required',
            'name_ar'=>'nullable|string',
            'location'=>'required',
            'location_ar'=>'nullable|string',
            'latitude'=>'required',
            'longitude'=>'required',
            'count'=>'nullable|string',
            'status'=>'required|in:0,1',
            'image' => ['required', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);
     
        $location = new Location();
        $location->name = $request->name;
        $location->name_ar = $request->name_ar;
        $location->location = $request->location;
        $location->location_ar = $request->location_ar;
        $location->latitude = $request->latitude;
        $location->longitude = $request->longitude;
        $location->count = $request->count;
        $location->status = 1;


        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                try {
                    $filePath = fileUploader($request->file('image'), getFilePath('location') , getFileSize('location'));
                    $location->image = $filePath;
                } catch (\Exception $exp) {
                    $notify[] = ['error', __('Couldn\'t upload your file')];
                    return back()->withNotify($notify);
                }
            }
        }

        $location->save();

        $notify[] = ['success', __('Location has been created successfully')];
        return back()->withNotify($notify);
    }

    public function edit($id){
        $pageTitle = __('Update Location');
        $location =  Location::findOrFail($id);
        return view('admin.location.edit',compact('pageTitle','location'));
    }
    
    public function update(Request $request,$id){
        $request->validate([
            'name'=>'required',
            'name_ar'=>'nullable|string',
            'location'=>'required',
            'location_ar'=>'nullable|string',
            'latitude'=>'required',
            'longitude'=>'required',
            'count'=>'nullable|string',
            'status'=>'nullable|in:0,1',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
        ]);

        $location = Location::findOrFail($id);
        $location->name = $request->name;
        $location->name_ar = $request->name_ar;
        $location->location = $request->location;
        $location->location_ar = $request->location_ar;
        $location->latitude = $request->latitude ?? $location->latitude;
        $location->longitude = $request->longitude ?? $location->longitude;
        $location->count = $request->count ?? $location->count;
        $location->status = $request->status ?? $location->status;


        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                try {
                    $old =  $location->image;
                    $filePath = fileUploader($request->file('image'), getFilePath('location') , getFileSize('location'),$old);
                    $location->image = $filePath;
                } catch (\Exception $exp) {
                    $notify[] = ['error', __('Couldn\'t upload your file')];
                    return back()->withNotify($notify);
                }
            }
        }

        $location->save();

        $notify[] = ['success', __('Location has been updated successfully')];
        return to_route('admin.location.index')->withNotify($notify);
    }

    public function delete($id)
    {
        $location = Location::findOrFail($id);
        $old = $location->image;
        fileManager()->removeFile(getFilePath('location') . '/' . $old);
        $location->delete();
        $notify[] = ['success', __('Location has been deleted successfully')];
        return back()->withNotify($notify);
    }
}

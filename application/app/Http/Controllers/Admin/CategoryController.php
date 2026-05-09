<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Rules\FileTypeValidate;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    public function index(){
        $pageTitle = __('Category List');
        $categories = Category::getSearch(['name'])->latest()->paginate(getPaginate());
        return view('admin.category.index',compact('pageTitle','categories'));
    }
    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'name_ar'=>'nullable|string',
            'status'=>'required|string|in:0,1',
        ]);

        $category = new Category();
        $category->name =  $request->name;
        $category->name_ar =  $request->name_ar;

        if ($request->hasFile('image')) {
            try {
                $category->image = fileUploader($request->image, getFilePath('category'), getFileSize('category'));
            } catch (\Exception $exp) {
                $notify[] = ['error', __('Couldn\'t upload your image')];
                return back()->withNotify($notify);
            }
        }

        $category->save();

        $notify[]=['success',__('Category added successfully')];
        return back()->withNotify($notify);
    }
    public function update(Request $request){

        $request->validate([
            'name'=>'required',
            'name_ar'=>'nullable|string',
            'status'=>'required|string|in:0,1',
        ]);

        $category = Category::findOrFail($request->id);
        $category->name =  $request->name;
        $category->name_ar =  $request->name_ar;
        $category->status =  $request->status;

        $category->save();

        $notify[]=['success',__('Category updated successfully')];
        return back()->withNotify($notify);
    }
    public function statusChange($id){
        $category = Category::findOrFail($id);
        $category->status = $category->status == 1 ? 0 : 1;
        $category->save();
    
        $notify[] = ['success', __('Status change has been successfully')];
        return back()->withNotify($notify);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reel;
use App\Models\ReelComment;
use App\Models\TourPackage;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Reels Library');
        $tourPackages = TourPackage::where('status', 1)->latest()->get();
        $reels = Reel::with('tourPackage')->when($request->search, function ($query) use ($request) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery->where('title', 'like', "%{$request->search}%")
                    ->orWhere('source_name', 'like', "%{$request->search}%");
            });
        })->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
            $query->where('status', $request->status);
        })->ordered()->paginate(getPaginate());

        return view('admin.reels.index', compact('pageTitle', 'reels', 'tourPackages'));
    }

    public function create()
    {
        $pageTitle = __('Create Reel');
        $tourPackages = TourPackage::where('status', 1)->latest()->get();

        return view('admin.reels.create', compact('pageTitle', 'tourPackages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'source_name' => 'nullable|string|max:255',
            'source_name_ar' => 'nullable|string|max:255',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
            'video_file' => ['required', 'file', new FileTypeValidate(['mp4', 'webm', 'mov', 'm4v'])],
            'thumbnail' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp'])],
        ]);

        try {
            $reel = new Reel();
            $reel->uploaded_by = auth('admin')->id();
            $reel->tour_package_id = $request->tour_package_id;
            $reel->title = $request->title;
            $reel->title_ar = $request->title_ar;
            $reel->description = $request->description;
            $reel->description_ar = $request->description_ar;
            $reel->source_name = $request->source_name;
            $reel->source_name_ar = $request->source_name_ar;
            $reel->link_url = $request->link_url;
            $reel->sort_order = $request->sort_order ?? 0;
            $reel->status = $request->status;
            $reel->video_path = fileUploader($request->file('video_file'), getFilePath('reelVideo'));

            if ($request->hasFile('thumbnail')) {
                $reel->thumbnail_path = fileUploader($request->file('thumbnail'), getFilePath('reelThumbnail'), getFileSize('reelThumbnail'));
            }

            $reel->save();

            $notify[] = ['success', __('Reel has been created successfully')];
        } catch (\Throwable $exp) {
            $notify[] = ['error', __('Couldn\'t create reel')];
        }

        return back()->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Reel');
        $tourPackages = TourPackage::where('status', 1)->latest()->get();
        $reel = Reel::findOrFail($id);

        return view('admin.reels.edit', compact('pageTitle', 'tourPackages', 'reel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'source_name' => 'nullable|string|max:255',
            'source_name_ar' => 'nullable|string|max:255',
            'tour_package_id' => 'nullable|exists:tour_packages,id',
            'link_url' => 'nullable|url',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:0,1',
            'video_file' => ['nullable', 'file', new FileTypeValidate(['mp4', 'webm', 'mov', 'm4v'])],
            'thumbnail' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'webp'])],
        ]);

        try {
            $reel = Reel::findOrFail($id);
            $reel->tour_package_id = $request->tour_package_id;
            $reel->title = $request->title;
            $reel->title_ar = $request->title_ar;
            $reel->description = $request->description;
            $reel->description_ar = $request->description_ar;
            $reel->source_name = $request->source_name;
            $reel->source_name_ar = $request->source_name_ar;
            $reel->link_url = $request->link_url;
            $reel->sort_order = $request->sort_order ?? 0;
            $reel->status = $request->status;

            if ($request->hasFile('video_file')) {
                $reel->video_path = fileUploader($request->file('video_file'), getFilePath('reelVideo'), null, $reel->video_path);
            }

            if ($request->hasFile('thumbnail')) {
                $reel->thumbnail_path = fileUploader($request->file('thumbnail'), getFilePath('reelThumbnail'), getFileSize('reelThumbnail'), $reel->thumbnail_path);
            }

            $reel->save();

            $notify[] = ['success', __('Reel has been updated successfully')];
        } catch (\Throwable $exp) {
            $notify[] = ['error', __('Couldn\'t update reel')];
        }

        return back()->withNotify($notify);
    }

    public function statusChange($id)
    {
        $reel = Reel::findOrFail($id);
        $reel->status = $reel->status ? 0 : 1;
        $reel->save();

        $notify[] = ['success', __('Status change has been successfully')];
        return back()->withNotify($notify);
    }

    public function delete($id)
    {
        try {
            $reel = Reel::findOrFail($id);

            if ($reel->video_path) {
                fileManager()->removeFile(getFilePath('reelVideo') . '/' . $reel->video_path);
            }

            if ($reel->thumbnail_path) {
                fileManager()->removeFile(getFilePath('reelThumbnail') . '/' . $reel->thumbnail_path);
            }

            $reel->delete();
            $notify[] = ['success', __('Reel deleted successfully')];
        } catch (\Throwable $exp) {
            $notify[] = ['error', __('Couldn\'t delete reel')];
        }

        return back()->withNotify($notify);
    }

    public function comments(Request $request)
    {
        $pageTitle = __('Reel Comments');
        $reels = Reel::active()->latest()->get();
        $comments = ReelComment::with(['reel', 'user', 'adminReplier'])
            ->when($request->reel_id, function ($query) use ($request) {
                $query->where('reel_id', $request->reel_id);
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(getPaginate());

        return view('admin.reels.comments', compact('pageTitle', 'comments', 'reels'));
    }

    public function commentStatusChange($id)
    {
        $comment = ReelComment::findOrFail($id);
        $comment->status = $comment->status ? 0 : 1;
        $comment->save();

        $notify[] = ['success', __('Comment status updated successfully')];
        return back()->withNotify($notify);
    }

    public function commentDelete($id)
    {
        $comment = ReelComment::findOrFail($id);
        $comment->delete();

        $notify[] = ['success', __('Comment deleted successfully')];
        return back()->withNotify($notify);
    }

    public function commentReply(Request $request, $id)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $comment = ReelComment::findOrFail($id);
        $comment->admin_reply = $request->admin_reply;
        $comment->replied_by = auth('admin')->id();
        $comment->replied_at = now();
        $comment->save();

        $notify[] = ['success', __('Reply saved successfully')];
        return back()->withNotify($notify);
    }
}

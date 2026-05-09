<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivilegeCard;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class PrivilegeCardController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = __('Privilege Cards');
        $cards = PrivilegeCard::latest();

        if ($request->search) {
            $search = $request->search;
            $cards = $cards->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('subtitle', 'like', "%{$search}%")
                    ->orWhere('subtitle_ar', 'like', "%{$search}%");
            });
        }

        $cards = $cards->paginate(getPaginate());

        return view('admin.privilege-card.index', compact('pageTitle', 'cards'));
    }

    public function create()
    {
        $pageTitle = __('Create Privilege Card');

        return view('admin.privilege-card.form', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:120',
            'name_ar' => 'nullable|string|max:120',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|string',
            'features' => 'nullable|string',
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'pdf_file' => ['nullable', 'file', new FileTypeValidate(['pdf'])],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $card = new PrivilegeCard();
        $card->name = $request->name;
        $card->name_ar = $request->name_ar;
        $card->subtitle = $request->subtitle;
        $card->subtitle_ar = $request->subtitle_ar;
        $card->description = $request->description;
        $card->description_ar = $request->description_ar;
        $card->price = $request->price ?? 0;
        $card->original_price = $request->original_price;
        $card->benefits = $this->normalizeLines($request->benefits);
        $card->features = $this->normalizeLines($request->features);
        $card->sort_order = $request->sort_order ?? 0;
        $card->is_featured = $request->is_featured ? 1 : 0;
        $card->status = $request->status ? 1 : 0;

        if ($request->hasFile('image_file')) {
            $card->image_file = fileUploader($request->image_file, getFilePath('privilegeCardImage'), null, $card->image_file ?? null);
        }

        if ($request->hasFile('pdf_file')) {
            $card->pdf_file = fileUploader($request->pdf_file, getFilePath('privilegeCardPdf'), null, $card->pdf_file ?? null);
        }

        $card->save();

        $notify[] = ['success', __('Privilege card created successfully')];
        return to_route('admin.privilege.cards')->withNotify($notify);
    }

    public function edit($id)
    {
        $pageTitle = __('Edit Privilege Card');
        $card = PrivilegeCard::findOrFail($id);

        return view('admin.privilege-card.form', compact('pageTitle', 'card'));
    }

    public function update(Request $request, $id)
    {
        $card = PrivilegeCard::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:120',
            'name_ar' => 'nullable|string|max:120',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'benefits' => 'nullable|string',
            'features' => 'nullable|string',
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'pdf_file' => ['nullable', 'file', new FileTypeValidate(['pdf'])],
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $card->name = $request->name;
        $card->name_ar = $request->name_ar;
        $card->subtitle = $request->subtitle;
        $card->subtitle_ar = $request->subtitle_ar;
        $card->description = $request->description;
        $card->description_ar = $request->description_ar;
        $card->price = $request->price ?? 0;
        $card->original_price = $request->original_price;
        $card->benefits = $this->normalizeLines($request->benefits);
        $card->features = $this->normalizeLines($request->features);
        $card->sort_order = $request->sort_order ?? 0;
        $card->is_featured = $request->is_featured ? 1 : 0;
        $card->status = $request->status ? 1 : 0;

        if ($request->hasFile('image_file')) {
            if ($card->image_file) {
                fileManager()->removeFile(getFilePath('privilegeCardImage') . '/' . $card->image_file);
            }
            $card->image_file = fileUploader($request->image_file, getFilePath('privilegeCardImage'), null, $card->image_file);
        }

        if ($request->hasFile('pdf_file')) {
            if ($card->pdf_file) {
                fileManager()->removeFile(getFilePath('privilegeCardPdf') . '/' . $card->pdf_file);
            }
            $card->pdf_file = fileUploader($request->pdf_file, getFilePath('privilegeCardPdf'), null, $card->pdf_file);
        }

        $card->save();

        $notify[] = ['success', __('Privilege card updated successfully')];
        return to_route('admin.privilege.cards')->withNotify($notify);
    }

    public function delete($id)
    {
        $card = PrivilegeCard::findOrFail($id);

        if ($card->image_file) {
            fileManager()->removeFile(getFilePath('privilegeCardImage') . '/' . $card->image_file);
        }

        if ($card->pdf_file) {
            fileManager()->removeFile(getFilePath('privilegeCardPdf') . '/' . $card->pdf_file);
        }

        $card->delete();

        $notify[] = ['success', __('Privilege card deleted successfully')];
        return back()->withNotify($notify);
    }

    public function statusChange($id)
    {
        $card = PrivilegeCard::findOrFail($id);
        $card->status = $card->status ? 0 : 1;
        $card->save();

        $notify[] = ['success', __('Status changed successfully')];
        return back()->withNotify($notify);
    }

    private function normalizeLines(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }
}

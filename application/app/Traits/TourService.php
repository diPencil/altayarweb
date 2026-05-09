<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\TourPackage;
use App\Models\TourPackageImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\TourPackageRequest;


trait TourService
{
    protected $data;

    public function store(TourPackageRequest $request)
    {

  
        DB::beginTransaction();
        try {
            if (count($request->features) != count($request->icons)) {
                $notify[] = ['error', 'Some data are missing'];
                return back()->withNotify($notify);
            }
            $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
            $endDate   = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);

            if ($startDate->lt(now())) {
                $notify[] = ['error', 'Start date must be today or a future date.'];
                return back()->withNotify($notify);
            }
            if (!$endDate->gt($startDate)) {
                $notify[] = ['error', 'End date must be greater than start date.'];
                return back()->withNotify($notify);
            }
            $fullArray = array_map(
                fn($icon, $feature) => [
                    'icon'    => $icon,
                    'feature' => $feature,
                ],
                $request->icons,
                $request->features
            );

            $tourPackage = new TourPackage();
            $purifier = new \HTMLPurifier();
            $tourPackage->user_id = $request->user_id;
            $tourPackage->user_type = $request->user_type;
            $tourPackage->title = $request->tour_title;
            $tourPackage->title_ar = $request->title_ar;
            $tourPackage->address = $request->address;
            $tourPackage->address_ar = $request->address_ar;
            $tourPackage->description = $purifier->purify($request->description);
            $tourPackage->description_ar = $request->description_ar;
            $tourPackage->price = $request->price;
            $tourPackage->discount = $request->discount;
            $tourPackage->currency = $request->currency ?: gs()->cur_text;
            $tourPackage->price_from = $request->price_from;
            $tourPackage->price_to = $request->price_to;
            $tourPackage->price_note = $request->price_note;
            $tourPackage->day_nights = $request->day_nights;
            $tourPackage->person_capability = $request->person_capability;
            $tourPackage->flexible_date = $request->flexible_date;
            $tourPackage->tour_start = $startDate->toDateTimeString();
            $tourPackage->tour_end = $endDate->toDateTimeString();
            $tourPackage->category_id = $request->category_id;
            $tourPackage->tour_type = $request->tour_type ?: optional($tourPackage->category)->name;
            $tourPackage->tour_type_ar = $request->tour_type_ar;
            $tourPackage->latitude = $request->latitude;
            $tourPackage->longitude = $request->longitude;
            $tourPackage->city = $request->city;
            $tourPackage->state = $request->state;
            $tourPackage->country = $request->country;
            $tourPackage->zip_code = $request->zipcode;
            $tourPackage->features = $fullArray;
            $tourPackage->destination_features_ar = $this->buildLocalizedFeatureRows($request);
            $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
            $tourPackage->highlights = $request->highlights;
            $tourPackage->destination_highlights_ar = $this->normalizeLines($request->highlights_ar);
            $tourPackage->includes = $this->normalizeLines($request->includes);
            $tourPackage->includes_ar = $this->normalizeLines($request->includes_ar);
            $tourPackage->excludes = $this->normalizeLines($request->excludes);
            $tourPackage->excludes_ar = $this->normalizeLines($request->excludes_ar);
            $tourPackage->itinerary_days = $this->normalizeItineraryDays($request->itinerary_days, $this->uploadItineraryDayImages($request));
            $tourPackage->cities_covered = $request->cities_covered;
            $tourPackage->cities_covered_ar = $request->cities_covered_ar;
            $tourPackage->accommodation_level = $request->accommodation_level;
            $tourPackage->package_label = $request->package_label;
            $tourPackage->package_label_ar = $request->package_label_ar;
            $tourPackage->departure_from_ar = $request->departure_from_ar;
            $tourPackage->arrival_ar = $request->arrival_ar;
            $tourPackage->transportation_ar = $request->transportation_ar;
            $tourPackage->accommodation_ar = $request->accommodation_ar;
            
            $tourPackage->status = 1;

            $tourPackage->save();

            if ($request->hasFile('images')) {

                foreach ($request->images as $index => $img) {
                    $tourPackageImage = new TourPackageImage();
                    $tourPackageImage->tour_package_id = $tourPackage->id;
                    if ($index === 0) {
                        $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'), '', "365x230");
                    } else {
                        $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                    }
                    $tourPackageImage->save();
                }
            }
            DB::commit();
            $notify[] = ['success', 'Tour Package created successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
            $notify[] = ['success', 'something went wrong'];
        }

        return back()->withNotify($notify);
    }


    public function update(TourPackageRequest $request, $id)
    {
        $tourPackage = TourPackage::with('category', 'tour_package_images')->findOrFail($id);
        if(!$tourPackage){
            $notify[] = ['error', 'Your tour package id is not valid'];
            return back()->withNotify($notify);
        }
      
        DB::beginTransaction();
        try {
        if (count($request->features) != count($request->icons)) {
            $notify[] = ['error', 'Some data are missing'];
            return back()->withNotify($notify);
        }
        $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
        $endDate   = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);
        if (!$endDate->gt($startDate)) {
            $notify[] = ['error', 'End date must be greater than start date.'];
            return back()->withNotify($notify);
        }
        $fullArray = array_map(
            fn($icon, $feature) => [
                'icon'    => $icon,
                'feature' => $feature,
            ],
            $request->icons,
            $request->features
        );

        $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
        $purifier = new \HTMLPurifier();
        $tourPackage->title = $request->tour_title;
    $tourPackage->title_ar = $request->title_ar;
        $tourPackage->address = $request->address;
        $tourPackage->address_ar = $request->address_ar;
        $tourPackage->description = $purifier->purify($request->description);
    $tourPackage->description_ar = $request->description_ar;
        $tourPackage->price = $request->price;
        $tourPackage->discount = $request->discount;
    $tourPackage->currency = $request->currency ?: ($tourPackage->currency ?: gs()->cur_text);
    $tourPackage->price_from = $request->price_from;
    $tourPackage->price_to = $request->price_to;
    $tourPackage->price_note = $request->price_note;
        $tourPackage->day_nights = $request->day_nights;
        $tourPackage->person_capability = $request->person_capability;
        $tourPackage->flexible_date = $request->flexible_date;
        $tourPackage->tour_start = $startDate->toDateTimeString();
        $tourPackage->tour_end = $endDate->toDateTimeString();
        $tourPackage->category_id = $request->category_id;
        $tourPackage->tour_type = $request->tour_type ?: ($tourPackage->tour_type ?: optional($tourPackage->category)->name);
        $tourPackage->tour_type_ar = $request->tour_type_ar;
        $tourPackage->latitude = $request->latitude;
        $tourPackage->longitude = $request->longitude;
        $tourPackage->city = $request->city;
        $tourPackage->state = $request->state;
        $tourPackage->country = $request->country;
        $tourPackage->zip_code = $request->zipcode;
        $tourPackage->features = $fullArray;
        $tourPackage->destination_features_ar = $this->buildLocalizedFeatureRows($request);
        $tourPackage->destination_overview = str_replace('"', "'", ($request->destination_overview));
        $tourPackage->highlights = $request->highlights;
        $tourPackage->destination_highlights_ar = $this->normalizeLines($request->highlights_ar);
        $tourPackage->includes = $this->normalizeLines($request->includes);
        $tourPackage->includes_ar = $this->normalizeLines($request->includes_ar);
        $tourPackage->excludes = $this->normalizeLines($request->excludes);
        $tourPackage->excludes_ar = $this->normalizeLines($request->excludes_ar);
        $tourPackage->itinerary_days = $this->normalizeItineraryDays($request->itinerary_days, $this->uploadItineraryDayImages($request));
        $tourPackage->cities_covered = $request->cities_covered;
        $tourPackage->cities_covered_ar = $request->cities_covered_ar;
        $tourPackage->accommodation_level = $request->accommodation_level;
        $tourPackage->package_label = $request->package_label;
        $tourPackage->package_label_ar = $request->package_label_ar;
        $tourPackage->departure_from_ar = $request->departure_from_ar;
        $tourPackage->arrival_ar = $request->arrival_ar;
        $tourPackage->transportation_ar = $request->transportation_ar;
        $tourPackage->accommodation_ar = $request->accommodation_ar;

        $tourPackage->save();

        if ($request->hasFile('images')) {
            foreach ($request->images as $index => $img) {
                $tourPackageImage = new TourPackageImage();
                $tourPackageImage->tour_package_id = $tourPackage->id;
                $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                $tourPackageImage->save();
            }
        }

        DB::commit();
        $notify[] = ['success', 'Tour Package updated successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
             $notify[] = ['success', 'something went wrong'];

        }
        return back()->withNotify($notify);
    }


    public function tourPackageImageDelete(Request $request)
    {
        try {
            $tourPackageImage = TourPackageImage::findOrFail($request->id);
            fileManager()->removeFile(getFilePath('tourPackageImage') . '/' . $tourPackageImage->image);
            if (file_exists(getFilePath('tourPackageImage') . '/thumb_' . $tourPackageImage->image)) {
                fileManager()->removeFile(getFilePath('tourPackageImage') . '/thumb_' . $tourPackageImage->image);
            }
            $tourPackageImage->delete();
            $data = [
                'status' => "success",
                'message' => "image delete successfully",
            ];
            return response()->json($data);
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t delete your image'];
            return back()->withNotify($notify);
        }
    }

    public function delete($id){
      
        try {
            $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
            foreach($tourPackage->tour_package_images ?? [] as $item){
                fileManager()->removeFile(getFilePath('tourPackageImage') . '/' . $item->image);
                if (file_exists(getFilePath('tourPackageImage') . '/thumb_' . $item->image)) {
                    fileManager()->removeFile(getFilePath('tourPackageImage') . '/thumb_' . $item->image);
                }
                $item->delete();
            }
            $tourPackage->delete();
            $notify[] = ['success', 'Tour Package delete successfully'];
            return back()->withNotify($notify);
        } catch (\Exception $exp) {
            $notify[] = ['success', 'something went wrong'];
            return back()->withNotify($notify);
        }
    }

    private function normalizeLines(?string $value): array
    {
        if (!$value) {
            return [];
        }

        return collect(preg_split('/\r\n|\r|\n/', $value))
            ->map(fn ($line) => trim($line))
            ->filter()
            ->values()
            ->all();
    }

    private function normalizeItineraryDays($days, array $uploadedImages = []): array
    {
        if (!$days || !is_array($days)) {
            return [];
        }

        return collect($days)
            ->map(function ($item, $index) use ($uploadedImages) {
                $item = is_array($item) ? $item : (array) $item;
                $uploadedImage = trim((string) ($uploadedImages[$index] ?? ''));
                $fallbackImage = trim((string) ($item['image'] ?? ''));

                return [
                    'day_number' => trim((string) ($item['day_number'] ?? '')),
                    'title' => trim((string) ($item['title'] ?? '')),
                    'title_ar' => trim((string) ($item['title_ar'] ?? '')),
                    'description' => trim((string) ($item['description'] ?? '')),
                    'description_ar' => trim((string) ($item['description_ar'] ?? '')),
                    'image' => $uploadedImage ?: $fallbackImage,
                ];
            })
            ->filter(function ($item) {
                return collect($item)->filter(fn ($value) => filled($value))->isNotEmpty();
            })
            ->values()
            ->all();
    }

    private function uploadItineraryDayImages(TourPackageRequest $request): array
    {
        $uploadedImages = [];
        $days = $request->file('itinerary_days', []);

        if (!is_array($days)) {
            return $uploadedImages;
        }

        foreach ($days as $index => $day) {
            $file = data_get($day, 'image_file');

            if (!$file) {
                continue;
            }

            $uploadedImages[$index] = fileUploader($file, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'), '', '365x230');
        }

        return $uploadedImages;
    }

    private function buildLocalizedFeatureRows(TourPackageRequest $request): array
    {
        $features = $request->features ?? [];
        $icons = $request->icons ?? [];
        $featuresAr = $request->features_ar ?? [];

        return collect($icons)
            ->map(function ($icon, $index) use ($features, $featuresAr) {
                $feature = trim((string) ($features[$index] ?? ''));
                $featureAr = trim((string) ($featuresAr[$index] ?? ''));

                if ($feature === '' && $featureAr === '' && trim((string) $icon) === '') {
                    return null;
                }

                return [
                    'icon' => trim((string) $icon),
                    'feature' => $feature,
                    'feature_ar' => $featureAr,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }
}

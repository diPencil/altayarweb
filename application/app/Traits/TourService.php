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
            try {
                $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
            } catch (\Exception $e) {
                try {
                    $startDate = Carbon::parse($request->start_date);
                } catch (\Exception $ex) {
                    $notify[] = ['error', 'Invalid start date format.'];
                    return back()->withNotify($notify);
                }
            }

            try {
                $endDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);
            } catch (\Exception $e) {
                try {
                    $endDate = Carbon::parse($request->end_date);
                } catch (\Exception $ex) {
                    $notify[] = ['error', 'Invalid end date format.'];
                    return back()->withNotify($notify);
                }
            }

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
            $tourPackage->setAttribute('features', $fullArray);
            $tourPackage->setAttribute('destination_features_ar', $this->buildLocalizedFeatureRows($request));
            $tourPackage->setAttribute('destination_overview', str_replace('"', "'", ($request->destination_overview)));
            $tourPackage->setAttribute('highlights', $request->highlights);

            $highlightsAr = $request->highlights_ar;
            if (is_array($highlightsAr)) {
                $highlightsAr = implode("\n", array_filter($highlightsAr));
            }
            $highlightsAr = is_string($highlightsAr) ? $highlightsAr : '';
            $tourPackage->setAttribute('destination_highlights_ar', $this->normalizeLines($highlightsAr));

            $includes = $request->includes;
            if (is_array($includes)) {
                $includes = implode("\n", array_filter($includes));
            }
            $includes = is_string($includes) ? $includes : '';
            $tourPackage->setAttribute('includes', $this->normalizeLines($includes));

            $includesAr = $request->includes_ar;
            if (is_array($includesAr)) {
                $includesAr = implode("\n", array_filter($includesAr));
            }
            $includesAr = is_string($includesAr) ? $includesAr : '';
            $tourPackage->setAttribute('includes_ar', $this->normalizeLines($includesAr));

            $excludes = $request->excludes;
            if (is_array($excludes)) {
                $excludes = implode("\n", array_filter($excludes));
            }
            $excludes = is_string($excludes) ? $excludes : '';
            $tourPackage->setAttribute('excludes', $this->normalizeLines($excludes));

            $excludesAr = $request->excludes_ar;
            if (is_array($excludesAr)) {
                $excludesAr = implode("\n", array_filter($excludesAr));
            }
            $excludesAr = is_string($excludesAr) ? $excludesAr : '';
            $tourPackage->setAttribute('excludes_ar', $this->normalizeLines($excludesAr));

            $tourPackage->setAttribute('itinerary_days', $this->normalizeItineraryDays($request->itinerary_days, $this->uploadItineraryDayImages($request)));
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
            \Log::error("Tour Package Store Error: " . $exp->getMessage() . "\n" . $exp->getTraceAsString());
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
        $hasIcons = $request->has('icons') && is_array($request->icons) && count(array_filter($request->icons, fn($val) => trim((string)$val) !== '')) > 0;
        $hasFeatures = $request->has('features') && is_array($request->features) && count(array_filter($request->features, fn($val) => trim((string)$val) !== '')) > 0;

        if ($hasFeatures && $hasIcons) {
            if (count($request->features) != count($request->icons)) {
                $notify[] = ['error', 'Some data are missing'];
                return back()->withNotify($notify);
            }
        }
        try {
            $startDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->start_date);
        } catch (\Exception $e) {
            try {
                $startDate = Carbon::parse($request->start_date);
            } catch (\Exception $ex) {
                $notify[] = ['error', 'Invalid start date format.'];
                return back()->withNotify($notify);
            }
        }

        try {
            $endDate = Carbon::createFromFormat('m/d/Y , h:i a', $request->end_date);
        } catch (\Exception $e) {
            try {
                $endDate = Carbon::parse($request->end_date);
            } catch (\Exception $ex) {
                $notify[] = ['error', 'Invalid end date format.'];
                return back()->withNotify($notify);
            }
        }
        if (!$endDate->gt($startDate)) {
            $notify[] = ['error', 'End date must be greater than start date.'];
            return back()->withNotify($notify);
        }
        $fullArray = null;
        if ($hasFeatures && $hasIcons) {
            $fullArray = array_map(
                fn($icon, $feature) => [
                    'icon'    => $icon,
                    'feature' => $feature,
                ],
                $request->icons,
                $request->features
            );
        }

        $tourPackage = TourPackage::with('tour_package_images')->findOrFail($id);
        $purifier = new \HTMLPurifier();

        if ($request->has('tour_title') && $request->filled('tour_title')) {
            $tourPackage->title = $request->tour_title;
        }
        if ($request->has('title_ar') && $request->filled('title_ar')) {
            $tourPackage->title_ar = $request->title_ar;
        }
        if ($request->has('address') && $request->filled('address')) {
            $tourPackage->address = $request->address;
        }
        if ($request->has('address_ar') && $request->filled('address_ar')) {
            $tourPackage->address_ar = $request->address_ar;
        }
        if ($request->has('description') && $request->filled('description')) {
            $tourPackage->description = $purifier->purify($request->description);
        }
        if ($request->has('description_ar') && $request->filled('description_ar')) {
            $tourPackage->description_ar = $request->description_ar;
        }
        if ($request->has('price') && $request->filled('price')) {
            $tourPackage->price = $request->price;
        }
        if ($request->has('discount')) {
            $tourPackage->discount = $request->discount;
        }
        if ($request->has('currency')) {
            $tourPackage->currency = $request->currency ?: ($tourPackage->currency ?: gs()->cur_text);
        }
        if ($request->has('price_from')) {
            $tourPackage->price_from = $request->price_from;
        }
        if ($request->has('price_to')) {
            $tourPackage->price_to = $request->price_to;
        }
        if ($request->has('price_note')) {
            $tourPackage->price_note = $request->price_note;
        }
        if ($request->has('day_nights') && $request->filled('day_nights')) {
            $tourPackage->day_nights = $request->day_nights;
        }
        if ($request->has('person_capability') && $request->filled('person_capability')) {
            $tourPackage->person_capability = $request->person_capability;
        }
        if ($request->has('flexible_date') && $request->filled('flexible_date')) {
            $tourPackage->flexible_date = $request->flexible_date;
        }

        $tourPackage->tour_start = $startDate->toDateTimeString();
        $tourPackage->tour_end = $endDate->toDateTimeString();

        if ($tourPackage->status == 3) {
            if ($endDate->gt(now())) {
                if ($startDate->lte(now())) {
                    $tourPackage->status = 2; // Running
                } else {
                    $tourPackage->status = 1; // Active
                }
            }
        } elseif ($tourPackage->status == 1 || $tourPackage->status == 2) {
            if ($endDate->lt(now())) {
                $tourPackage->status = 3; // Expired
            } elseif ($startDate->lte(now()) && $endDate->gt(now())) {
                $tourPackage->status = 2; // Running
            } else {
                $tourPackage->status = 1; // Active
            }
        }

        if ($request->has('category_id') && $request->filled('category_id')) {
            $tourPackage->category_id = $request->category_id;
        }
        if ($request->has('tour_type')) {
            $tourPackage->tour_type = $request->tour_type ?: ($tourPackage->tour_type ?: optional($tourPackage->category)->name);
        }
        if ($request->has('tour_type_ar') && $request->filled('tour_type_ar')) {
            $tourPackage->tour_type_ar = $request->tour_type_ar;
        }
        if ($request->has('latitude')) {
            $tourPackage->latitude = $request->latitude;
        }
        if ($request->has('longitude')) {
            $tourPackage->longitude = $request->longitude;
        }
        if ($request->has('city')) {
            $tourPackage->city = $request->city;
        }
        if ($request->has('state')) {
            $tourPackage->state = $request->state;
        }
        if ($request->has('country') && $request->filled('country')) {
            $tourPackage->country = $request->country;
        }
        if ($request->has('zipcode')) {
            $tourPackage->zip_code = $request->zipcode;
        }

        if ($fullArray !== null) {
            $tourPackage->setAttribute('features', $fullArray);
        }

        $hasFeaturesAr = $request->has('features_ar') && is_array($request->features_ar) && count(array_filter($request->features_ar, fn($val) => trim((string)$val) !== '')) > 0;
        if ($hasFeaturesAr || $hasIcons || $hasFeatures) {
            $tourPackage->setAttribute('destination_features_ar', $this->buildLocalizedFeatureRows($request));
        }

        $hasOverview = $request->has('destination_overview') && is_array($request->destination_overview) && count(array_filter($request->destination_overview, fn($val) => trim((string)$val) !== '')) > 0;
        if ($hasOverview) {
            $tourPackage->setAttribute('destination_overview', str_replace('"', "'", ($request->destination_overview)));
        }

        $hasHighlights = $request->has('highlights') && is_array($request->highlights) && count(array_filter($request->highlights, fn($val) => trim((string)$val) !== '')) > 0;
        if ($hasHighlights) {
            $tourPackage->setAttribute('highlights', $request->highlights);
        }

        $hasHighlightsAr = $request->has('highlights_ar') && is_array($request->highlights_ar) && count(array_filter($request->highlights_ar, fn($val) => trim((string)$val) !== '')) > 0;
        if ($hasHighlightsAr) {
            $highlightsAr = $request->highlights_ar;
            if (is_array($highlightsAr)) {
                $highlightsAr = implode("\n", array_filter($highlightsAr));
            }
            $highlightsAr = is_string($highlightsAr) ? $highlightsAr : '';
            $tourPackage->setAttribute('destination_highlights_ar', $this->normalizeLines($highlightsAr));
        }

        if ($request->has('includes') && trim((string)$request->includes) !== '') {
            $includes = $request->includes;
            if (is_array($includes)) {
                $includes = implode("\n", array_filter($includes));
            }
            $includes = is_string($includes) ? $includes : '';
            $tourPackage->setAttribute('includes', $this->normalizeLines($includes));
        }
        if ($request->has('includes_ar') && trim((string)$request->includes_ar) !== '') {
            $includesAr = $request->includes_ar;
            if (is_array($includesAr)) {
                $includesAr = implode("\n", array_filter($includesAr));
            }
            $includesAr = is_string($includesAr) ? $includesAr : '';
            $tourPackage->setAttribute('includes_ar', $this->normalizeLines($includesAr));
        }
        if ($request->has('excludes') && trim((string)$request->excludes) !== '') {
            $excludes = $request->excludes;
            if (is_array($excludes)) {
                $excludes = implode("\n", array_filter($excludes));
            }
            $excludes = is_string($excludes) ? $excludes : '';
            $tourPackage->setAttribute('excludes', $this->normalizeLines($excludes));
        }
        if ($request->has('excludes_ar') && trim((string)$request->excludes_ar) !== '') {
            $excludesAr = $request->excludes_ar;
            if (is_array($excludesAr)) {
                $excludesAr = implode("\n", array_filter($excludesAr));
            }
            $excludesAr = is_string($excludesAr) ? $excludesAr : '';
            $tourPackage->setAttribute('excludes_ar', $this->normalizeLines($excludesAr));
        }

        $hasItinerary = false;
        if ($request->has('itinerary_days') && is_array($request->itinerary_days)) {
            foreach ($request->itinerary_days as $day) {
                if (filled(data_get($day, 'title')) || filled(data_get($day, 'description')) || filled(data_get($day, 'day_number'))) {
                    $hasItinerary = true;
                    break;
                }
            }
        }
        if ($hasItinerary) {
            $tourPackage->setAttribute('itinerary_days', $this->normalizeItineraryDays($request->itinerary_days, $this->uploadItineraryDayImages($request)));
        }

        if ($request->has('cities_covered')) {
            $tourPackage->cities_covered = $request->cities_covered;
        }
        if ($request->has('cities_covered_ar')) {
            $tourPackage->cities_covered_ar = $request->cities_covered_ar;
        }
        if ($request->has('accommodation_level')) {
            $tourPackage->accommodation_level = $request->accommodation_level;
        }
        if ($request->has('package_label')) {
            $tourPackage->package_label = $request->package_label;
        }
        if ($request->has('package_label_ar')) {
            $tourPackage->package_label_ar = $request->package_label_ar;
        }
        if ($request->has('departure_from_ar')) {
            $tourPackage->departure_from_ar = $request->departure_from_ar;
        }
        if ($request->has('arrival_ar')) {
            $tourPackage->arrival_ar = $request->arrival_ar;
        }
        if ($request->has('transportation_ar')) {
            $tourPackage->transportation_ar = $request->transportation_ar;
        }
        if ($request->has('accommodation_ar')) {
            $tourPackage->accommodation_ar = $request->accommodation_ar;
        }

        $tourPackage->save();

        if ($request->hasFile('images')) {
            $existingCount = $tourPackage->tour_package_images()->count();
            foreach ($request->images as $index => $img) {
                $tourPackageImage = new TourPackageImage();
                $tourPackageImage->tour_package_id = $tourPackage->id;
                if ($existingCount == 0 && $index == 0) {
                    $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'), '', "365x230");
                } else {
                    $tourPackageImage->image = fileUploader($img, getFilePath('tourPackageImage'), getFileSize('tourPackageImage'));
                }
                $tourPackageImage->save();
            }
        }

        DB::commit();
        $notify[] = ['success', 'Tour Package updated successfully'];
        } catch (\Exception $exp) {
            DB::rollBack();
            \Log::error("Tour Package Update Error: " . $exp->getMessage() . "\n" . $exp->getTraceAsString());
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

    private function normalizeLines(string $value): array
    {
        if (trim($value) === '') {
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
                if ($item === null) {
                    return null;
                }
                
                $uploadedImage = trim((string) ($uploadedImages[$index] ?? ''));
                $fallbackImage = trim((string) (is_array($item) ? ($item['image'] ?? '') : ($item->image ?? '')));

                return [
                    'day_number' => trim((string) (is_array($item) ? ($item['day_number'] ?? '') : ($item->day_number ?? ''))),
                    'title' => trim((string) (is_array($item) ? ($item['title'] ?? '') : ($item->title ?? ''))),
                    'title_ar' => trim((string) (is_array($item) ? ($item['title_ar'] ?? '') : ($item->title_ar ?? ''))),
                    'description' => trim((string) (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))),
                    'description_ar' => trim((string) (is_array($item) ? ($item['description_ar'] ?? '') : ($item->description_ar ?? ''))),
                    'image' => $uploadedImage ?: $fallbackImage,
                ];
            })
            ->filter(function ($item) {
                return $item !== null && collect($item)->filter(fn ($value) => filled($value))->isNotEmpty();
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

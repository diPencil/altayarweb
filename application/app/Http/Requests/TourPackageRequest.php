<?php

namespace App\Http\Requests;

use App\Rules\FileTypeValidate;
use Illuminate\Foundation\Http\FormRequest;

class TourPackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
      
        $rules =
            [
                'user_id' => 'required|numeric',
                'user_type' => ['required','in:admin,agent'],
                'category_id' => 'required|exists:categories,id',
                'flexible_date' => ['required', 'integer', 'in:1,2'],
                'tour_title' => 'required|string',
                'title_ar' => 'nullable|string|max:255',
                'address' => 'required|string',
                'address_ar' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'country' => 'required|string',
                'city' => 'nullable|string',
                'zipcode' => 'nullable|string',
                'state' => 'nullable|string',
                'start_date' => ['required','string'],
                'end_date'   => ['required','string'],
                'person_capability' => 'required|string',
                'day_nights' => 'required|string',
                'price' => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'discount' => 'nullable|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
                'currency' => 'nullable|in:EGP,SAR,USD,EUR',
                'price_from' => 'nullable|numeric|min:0',
                'price_to' => 'nullable|numeric|min:0',
                'price_note' => 'nullable|string|max:255',
                'description' => 'required|string',
                'description_ar' => 'nullable|string',
                'destination_overview' => 'required|array',
                'destination_overview.*' => 'required',
                'highlights' => 'required|array',
                'highlights.*' => 'required',
                'highlights_ar' => 'nullable|array',
                'highlights_ar.*' => 'nullable|string',
                'icons' => 'required|array|min:1',
                'icons.*' => 'required',
                'features' => 'required|array|min:1',
                'features.*' => 'required',
                'features_ar' => 'nullable|array',
                'features_ar.*' => 'nullable|string',
                'includes' => 'nullable|string',
                'includes_ar' => 'nullable|string',
                'excludes' => 'nullable|string',
                'excludes_ar' => 'nullable|string',
                'itinerary_days' => 'nullable|array',
                'itinerary_days.*.day_number' => 'nullable|string|max:50',
                'itinerary_days.*.title' => 'nullable|string|max:255',
                'itinerary_days.*.title_ar' => 'nullable|string|max:255',
                'itinerary_days.*.description' => 'nullable|string',
                'itinerary_days.*.description_ar' => 'nullable|string',
                'itinerary_days.*.image' => 'nullable|string|max:255',
                'itinerary_days.*.image_file' => ['nullable', 'max:3072', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'])],
                'cities_covered' => 'nullable|string|max:255',
                'cities_covered_ar' => 'nullable|string|max:255',
                'accommodation_level' => 'nullable|string|max:255',
                'package_label' => 'nullable|string|max:255',
                'package_label_ar' => 'nullable|string|max:255',
                'tour_type' => 'nullable|string|max:255',
                'tour_type_ar' => 'nullable|string|max:255',
                'departure_from_ar' => 'nullable|string|max:255',
                'arrival_ar' => 'nullable|string|max:255',
                'transportation_ar' => 'nullable|string|max:255',
                'accommodation_ar' => 'nullable|string|max:255',
                'images' => 'required|array|min:1',
                'images.*' => ['max:3072','image', new FileTypeValidate(['jpg','jpeg','png','JPG','JPEG','PNG'])]

            ];
        if ($this->method() == "PUT" && request()->old_tour_package_images) {
            $rules['images'] = 'nullable|array';
            $rules['images.*'] = ['nullable', 'max:3072', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png', 'JPG', 'JPEG', 'PNG'])];
        }

        return $rules;
    }
}

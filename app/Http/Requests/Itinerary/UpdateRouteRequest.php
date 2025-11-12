<?php

namespace App\Http\Requests\Itinerary;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'cover_image' => ['sometimes', 'nullable', 'string', 'max:2048'],
            'region' => ['sometimes', 'nullable', 'string', 'max:255'],
            'difficulty' => ['sometimes', 'nullable', 'string', 'max:100'],
            'season' => ['sometimes', 'nullable', 'string', 'max:100'],
            'duration_days' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'start_date' => ['sometimes', 'nullable', 'date'],
            'end_date' => ['sometimes', 'nullable', 'date', 'after_or_equal:start_date'],
            'visibility' => ['sometimes', 'nullable', 'in:public,private,crew'],
            'status' => ['sometimes', 'nullable', 'in:draft,active,completed,archived'],
            'tags' => ['sometimes', 'nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'metadata' => ['sometimes', 'nullable', 'array'],
            'stops' => ['sometimes', 'array'],
            'stops.*.id' => ['nullable', 'integer', 'exists:itinerary_route_stops,id'],
            'stops.*.name' => ['required_with:stops.*', 'string', 'max:255'],
            'stops.*.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'stops.*.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'stops.*.sequence' => ['nullable', 'integer', 'min:1'],
            'stops.*.day_number' => ['nullable', 'integer', 'min:1'],
            'stops.*.stay_duration_hours' => ['nullable', 'integer', 'min:0'],
            'stops.*.description' => ['nullable', 'string'],
            'stops.*.notes' => ['nullable', 'string'],
            'stops.*.photos' => ['nullable', 'array'],
            'stops.*.photos.*' => ['string'],
            'stops.*.tasks' => ['nullable', 'array'],
            'stops.*.checklists' => ['nullable', 'array'],
            'stops.*.eta' => ['nullable', 'date'],
            'stops.*.ata' => ['nullable', 'date'],
            'stops.*.requires_clearance' => ['nullable', 'boolean'],
        ];
    }
}


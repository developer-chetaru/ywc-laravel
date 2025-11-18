<?php

namespace App\Http\Requests\Itinerary;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        if ($this->user() === null) {
            if ($this->expectsJson() || $this->is('api/*')) {
                throw new HttpResponseException(
                    response()->json([
                        'message' => 'Unauthenticated.',
                    ], 401)
                );
            }
            return false;
        }
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson() || $this->is('api/*')) {
            throw new HttpResponseException(
                response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'region' => ['nullable', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'max:100'],
            'season' => ['nullable', 'string', 'max:100'],
            'duration_days' => ['nullable', 'integer', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'visibility' => ['nullable', 'in:public,private,crew'],
            'status' => ['nullable', 'in:draft,active,completed,archived'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'metadata' => ['nullable', 'array'],
            'stops' => ['required', 'array', 'min:1'],
            'stops.*.name' => ['required', 'string', 'max:255'],
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
            'stops.*.tasks.*' => ['string'],
            'stops.*.checklists' => ['nullable', 'array'],
            'stops.*.checklists.*' => ['string'],
            'stops.*.eta' => ['nullable', 'date'],
            'stops.*.ata' => ['nullable', 'date'],
            'stops.*.requires_clearance' => ['nullable', 'boolean'],
        ];
    }
}


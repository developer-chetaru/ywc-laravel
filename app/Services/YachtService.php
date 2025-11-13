<?php

namespace App\Services;

use App\Models\Yacht;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class YachtService
{
    public function create(array $data, $file = null)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'type' => 'required|in:motor_yacht,sailing_yacht,explorer,catamaran,other',
            'length_meters' => 'nullable|numeric|min:0',
            'length_feet' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'flag_registry' => 'nullable|string|max:255',
            'home_port' => 'nullable|string|max:255',
            'crew_capacity' => 'nullable|integer|min:1',
            'guest_capacity' => 'nullable|integer|min:1',
            'status' => 'required|in:charter,private,both',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Handle cover image upload
        if ($file) {
            $validated['cover_image'] = $file->store('yachts', 'public');
        }

        $yacht = Yacht::create($validated);

        if ($yacht->cover_image) {
            $yacht->cover_image_url = Storage::disk('public')->url($yacht->cover_image);
        }

        return $yacht;
    }

    public function update(Yacht $yacht, array $data, $file = null)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:motor_yacht,sailing_yacht,explorer,catamaran,other',
            'length_meters' => 'nullable|numeric|min:0',
            'length_feet' => 'nullable|numeric|min:0',
            'year_built' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'flag_registry' => 'nullable|string|max:255',
            'home_port' => 'nullable|string|max:255',
            'crew_capacity' => 'nullable|integer|min:1',
            'guest_capacity' => 'nullable|integer|min:1',
            'status' => 'sometimes|required|in:charter,private,both',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Handle cover image upload
        if ($file) {
            // Delete old image if exists
            if ($yacht->cover_image && Storage::disk('public')->exists($yacht->cover_image)) {
                Storage::disk('public')->delete($yacht->cover_image);
            }
            $validated['cover_image'] = $file->store('yachts', 'public');
        }

        $yacht->update($validated);

        if ($yacht->cover_image) {
            $yacht->cover_image_url = Storage::disk('public')->url($yacht->cover_image);
        }

        return $yacht;
    }

    public function delete(Yacht $yacht)
    {
        // Delete cover image if exists
        if ($yacht->cover_image && Storage::disk('public')->exists($yacht->cover_image)) {
            Storage::disk('public')->delete($yacht->cover_image);
        }

        $yacht->delete();
    }
}


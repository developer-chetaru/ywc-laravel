<?php

namespace App\Services;

use App\Models\Marina;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MarinaService
{
    public function create(array $data, $file = null)
    {
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'required|in:full_service,municipal_port,yacht_club,anchorage,mooring_field,dry_stack,boatyard',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Handle cover image upload
        if ($file) {
            $validated['cover_image'] = $file->store('marinas', 'public');
        }

        $marina = Marina::create($validated);

        if ($marina->cover_image) {
            $marina->cover_image_url = Storage::disk('public')->url($marina->cover_image);
        }

        return $marina;
    }

    public function update(Marina $marina, array $data, $file = null)
    {
        $validator = Validator::make($data, [
            'name' => 'sometimes|required|string|max:255',
            'country' => 'sometimes|required|string|max:255',
            'region' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'type' => 'sometimes|required|in:full_service,municipal_port,yacht_club,anchorage,mooring_field,dry_stack,boatyard',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $validated = $validator->validated();

        // Handle cover image upload
        if ($file) {
            // Delete old image if exists
            if ($marina->cover_image && Storage::disk('public')->exists($marina->cover_image)) {
                Storage::disk('public')->delete($marina->cover_image);
            }
            $validated['cover_image'] = $file->store('marinas', 'public');
        }

        $marina->update($validated);

        if ($marina->cover_image) {
            $marina->cover_image_url = Storage::disk('public')->url($marina->cover_image);
        }

        return $marina;
    }

    public function delete(Marina $marina)
    {
        // Delete cover image if exists
        if ($marina->cover_image && Storage::disk('public')->exists($marina->cover_image)) {
            Storage::disk('public')->delete($marina->cover_image);
        }

        $marina->delete();
    }
}


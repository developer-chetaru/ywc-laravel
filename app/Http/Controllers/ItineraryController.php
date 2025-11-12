<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use Illuminate\Http\Request;

class ItineraryController extends Controller
{
    public function index()
    {
        return response()->json(Itinerary::all());
    }

  public function store(Request $request)
    {
        $itinerary = new Itinerary();
        $itinerary->user_id = $request->user_id;
        $itinerary->title = $request->title;
        $itinerary->description = $request->description;
        $itinerary->day_count = $request->day_count;
        $itinerary->status = 'pending'; // default
        $itinerary->save();

        $days = json_decode($request->itinerary_days, true);
        $dayData = [];

        foreach ($days as $i => $day) {
            $images = [];
            if ($request->hasFile("day_{$i}_files")) {
                foreach ($request->file("day_{$i}_files") as $file) {
                    $path = $file->store('itinerary_images', 'public');
                    $images[] = $path;
                }
            }
            $dayData[] = [
                'topic' => $day['topic'] ?? '',
                'place' => $day['place'] ?? '',
                'description' => $day['description'] ?? '',
                'images' => $images,
            ];
        }

        $itinerary->itinerary_days = json_encode($dayData);
        $itinerary->save();

        return response()->json(['message' => 'Itinerary saved successfully']);
    }


    public function show(Itinerary $itinerary)
    {
        return response()->json($itinerary);
    }

    public function update(Request $request, $id)
    {
        $itinerary = Itinerary::findOrFail($id);

        // Decode JSON from form
        $itinerary_days = json_decode($request->input('itinerary_days'), true) ?? [];

        // Existing stored days from DB
        $existingDays = is_array($itinerary->itinerary_days)
            ? $itinerary->itinerary_days
            : json_decode($itinerary->itinerary_days, true);

        // Loop through new data and handle uploads
        foreach ($itinerary_days as $i => &$day) {
            $filesKey = "day_{$i}_files";
            $uploadedImages = [];

            // Upload new files
            if ($request->hasFile($filesKey)) {
                foreach ($request->file($filesKey) as $file) {
                    $path = $file->store('itinerary_images', 'public');
                    $uploadedImages[] = $path;
                }
            }

            // Preserve existing images from old data
            $oldImages = [];
            if (isset($existingDays[$i]['images'])) {
                $oldImages = is_array($existingDays[$i]['images'])
                    ? $existingDays[$i]['images']
                    : [$existingDays[$i]['images']];
            }

            // Merge old + new images
            $day['images'] = array_merge($oldImages, $uploadedImages);
        }

        // Update record
        $itinerary->update([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'day_count' => $request->input('day_count'),
            'itinerary_days' => json_encode($itinerary_days),
        ]);

        return response()->json([
            'message' => 'Itinerary updated successfully',
            'data' => $itinerary,
        ]);
    }


    public function destroy(Itinerary $itinerary)
    {
        $itinerary->delete();
        return response()->json(['message' => 'Itinerary deleted']);
    }
        public function updateStatus(Request $request, Itinerary $itinerary)
    {
        // $user = $request->user();
        // if (!$user->hasRole(['super_admin', 'admin'])) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $validated = $request->validate([
            'status' => 'required|in:approved,rejected,pending',
        ]);

        $itinerary->status = $validated['status'];
        $itinerary->save();

        return response()->json(['message' => "Itinerary {$validated['status']} successfully"]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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

    public function generateWithAI(Request $request)
    {
        $validated = $request->validate([
            'place' => 'required|string|max:255',
            'days' => 'required|integer|min:1|max:30',
        ]);

        $place = $validated['place'];
        $days = $validated['days'];

        try {
            // Fetch Wikipedia data for the place
            $wikipediaData = $this->fetchWikipediaData($place);

            $titleForLookup = $wikipediaData['title'] ?? $place;
            $relatedPages = array_values($this->fetchWikipediaRelated($titleForLookup));
            $mediaImages = $this->fetchWikipediaImages($titleForLookup);

            $coordinates = $this->fetchCoordinates($place);
            $weatherForecast = $coordinates
                ? $this->fetchWeatherForecast($coordinates['latitude'], $coordinates['longitude'], $days)
                : [];

            $localCuisine = $this->generateLocalCuisineSuggestions($place);
            $travelTips = $this->generateTravelTips($place);
            $packingList = $this->generatePackingList($weatherForecast);

            $itineraryDays = [];
            for ($i = 1; $i <= $days; $i++) {
                $focus = $relatedPages[$i - 1] ?? null;
                $topic = $this->buildDayTopic($place, $i, $focus);
                $description = $this->generateDayDescription($place, $i, $days, $wikipediaData, $focus);
                $images = $this->buildImageGallery($place, $i, $wikipediaData, $focus, $mediaImages);
                $youtubeVideos = $this->fetchYouTubeVideos($focus['title'] ?? $place, $i);
                $schedule = $this->generateDaySchedule($place, $focus, $i, $weatherForecast[$i - 1] ?? null);
                $weather = $weatherForecast[$i - 1] ?? null;

                $itineraryDays[] = [
                    'day' => $i,
                    'topic' => $topic,
                    'place' => $place,
                    'description' => $description,
                    'images' => $images,
                    'youtube_videos' => $youtubeVideos,
                    'schedule' => $schedule,
                    'weather' => $weather,
                    'wikipedia_info' => $focus ?: $wikipediaData,
                    'more_info_url' => $focus['url'] ?? ($wikipediaData['url'] ?? null)
                ];
            }

            return response()->json([
                'success' => true,
                'title' => "Itinerary for {$place} - {$days} Day(s)",
                'description' => $wikipediaData['extract'] ?? "A {$days}-day itinerary for exploring {$place}.",
                'place' => $place,
                'days' => $days,
                'itinerary_days' => $itineraryDays,
                'wikipedia_url' => $wikipediaData['url'] ?? null,
                'wikipedia_title' => $wikipediaData['title'] ?? $place,
                'coordinates' => $coordinates,
                'weather_summary' => $this->summarizeWeather($weatherForecast),
                'local_cuisine' => $localCuisine,
                'travel_tips' => $travelTips,
                'packing_list' => $packingList
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate itinerary: ' . $e->getMessage()
            ], 500);
        }
    }

    private function fetchWikipediaData($place)
    {
        try {
            // Search for the place
            $searchUrl = "https://en.wikipedia.org/api/rest_v1/page/summary/" . urlencode($place);
            $response = Http::timeout(10)->get($searchUrl);
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'title' => $data['title'] ?? $place,
                    'extract' => $data['extract'] ?? "Information about {$place}.",
                    'url' => $data['content_urls']['desktop']['page'] ?? null,
                    'thumbnail' => $data['thumbnail']['source'] ?? null
                ];
            }
        } catch (\Exception $e) {
            // Fallback if Wikipedia fails
        }
        
        return [
            'title' => $place,
            'extract' => "Discover the beauty and culture of {$place}.",
            'url' => null,
            'thumbnail' => null
        ];
    }

    private function buildImageGallery($place, $day, array $wikipediaData, ?array $focus, array $mediaImages)
    {
        $images = [];

        $appendImage = function ($url, $alt, $source) use (&$images) {
            if (!$url) {
                return;
            }
            foreach ($images as $img) {
                if ($img['url'] === $url) {
                    return;
                }
            }
            $images[] = [
                'url' => $url,
                'alt' => $alt,
                'source' => $source
            ];
        };

        // Start with focus thumbnail if available
        if ($focus && isset($focus['thumbnail'])) {
            $appendImage($focus['thumbnail'], ($focus['title'] ?? $place) . ' - Wikipedia', 'Wikipedia');
        }

        // Add main place thumbnail
        if (isset($wikipediaData['thumbnail'])) {
            $appendImage($wikipediaData['thumbnail'], ($wikipediaData['title'] ?? $place) . ' - Wikipedia', 'Wikipedia');
        }

        // Add media images from Wikipedia page media-list
        foreach ($mediaImages as $media) {
            $appendImage($media, $place . ' - Wikimedia', 'Wikimedia Commons');
            if (count($images) >= 4) {
                break;
            }
        }

        // Fallback to Unsplash Source for additional variety
        $keywords = array_filter([
            $focus['title'] ?? null,
            $place . ' attractions',
            $place . ' travel',
            $place . ' tourism',
        ]);

        $keywords = array_values(array_unique($keywords));
        $keywordCount = max(count($keywords), 1);

        while (count($images) < 4) {
            $keyword = $keywords[(count($images) + $day - 1) % $keywordCount] ?? $place;
            $signature = $day . '-' . count($images);
            $imageUrl = 'https://loremflickr.com/800/600/' . urlencode($keyword) . '?lock=' . $signature;
            $appendImage($imageUrl, $keyword . ' - Photo', 'LoremFlickr');
        }

        return array_slice($images, 0, 6);
    }

    private function fetchYouTubeVideos($place, $day)
    {
        // Note: YouTube Data API requires API key, so we'll use a simpler approach
        // You can integrate YouTube Data API v3 if you have an API key
        // For now, we'll return search URLs that can be used
        
        $videos = [];
        $searchTerms = [
            "{$place} travel guide",
            "{$place} things to do",
            "{$place} attractions",
            "{$place} tour"
        ];
        
        $searchTerm = $searchTerms[($day - 1) % count($searchTerms)];
        
        // Return YouTube search URLs (users can click to find videos)
        // Or if you have YouTube API key, you can fetch actual video IDs
        $videos[] = [
            'title' => "{$place} Travel Guide",
            'search_url' => "https://www.youtube.com/results?search_query=" . urlencode($searchTerm),
            'embed_url' => null // Placeholder without API key
        ];
        
        return $videos;
    }

    private function generateDayDescription($place, $day, $totalDays, $wikipediaData, $focus = null)
    {
        if ($focus && !empty($focus['extract'])) {
            return $focus['extract'];
        }

        $baseDescription = $wikipediaData['extract'] ?? "Explore the wonders of {$place}.";

        $dayDescriptions = [
            "Start your journey in {$place}. " . substr($baseDescription, 0, 200) . "...",
            "Continue exploring {$place} with its rich culture and history.",
            "Discover hidden gems and local experiences in {$place}.",
            "Immerse yourself in the local cuisine and traditions of {$place}.",
            "Visit iconic landmarks and attractions in {$place}.",
            "Experience the nightlife and entertainment in {$place}.",
            "Take a day trip to nearby attractions from {$place}.",
            "Enjoy outdoor activities and nature in {$place}.",
            "Explore museums and cultural sites in {$place}.",
            "Relax and enjoy the local atmosphere of {$place}."
        ];

        $descriptionIndex = ($day - 1) % count($dayDescriptions);
        return $dayDescriptions[$descriptionIndex];
    }

    private function fetchWikipediaRelated($title)
    {
        try {
            $searchUrl = "https://en.wikipedia.org/api/rest_v1/page/related/" . rawurlencode($title);
            $response = Http::timeout(10)->get($searchUrl);
            if ($response->successful()) {
                $data = $response->json();
                $pages = $data['pages'] ?? [];
                $formatted = [];
                foreach ($pages as $page) {
                    if (($page['type'] ?? '') !== 'standard') {
                        continue;
                    }
                    $formatted[] = [
                        'title' => $page['normalizedtitle'] ?? $page['displaytitle'] ?? $page['title'] ?? $title,
                        'extract' => $page['extract'] ?? null,
                        'thumbnail' => $page['thumbnail']['source'] ?? null,
                        'url' => $page['content_urls']['desktop']['page'] ?? null,
                    ];
                }
                return $formatted;
            }
        } catch (\Exception $e) {
            // Fallback or error handling
        }
        return [];
    }

    private function fetchWikipediaImages($title)
    {
        try {
            $searchUrl = "https://en.wikipedia.org/api/rest_v1/page/media-list/" . rawurlencode($title);
            $response = Http::timeout(10)->get($searchUrl);
            if ($response->successful()) {
                $data = $response->json();
                $items = $data['items'] ?? [];
                $images = [];
                foreach ($items as $item) {
                    if (($item['type'] ?? '') !== 'image') {
                        continue;
                    }
                    if (isset($item['original']['source'])) {
                        $images[] = $item['original']['source'];
                    } elseif (isset($item['srcset'][0]['src'])) {
                        $images[] = $item['srcset'][0]['src'];
                    }
                    if (count($images) >= 8) {
                        break;
                    }
                }
                return $images;
            }
        } catch (\Exception $e) {
            // Fallback or error handling
        }
        return [];
    }

    private function fetchCoordinates($place)
    {
        try {
            $url = 'https://geocoding-api.open-meteo.com/v1/search?count=1&language=en&name=' . urlencode($place);
            $response = Http::timeout(10)->get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'][0])) {
                    $result = $data['results'][0];
                    return [
                        'name' => $result['name'] ?? $place,
                        'latitude' => $result['latitude'],
                        'longitude' => $result['longitude'],
                        'country' => $result['country'] ?? null,
                        'timezone' => $result['timezone'] ?? 'auto'
                    ];
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        return null;
    }

    private function fetchWeatherForecast($lat, $lon, $days)
    {
        try {
            $url = 'https://api.open-meteo.com/v1/forecast';
            $response = Http::timeout(10)->get($url, [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_probability_mean,weathercode',
                'timezone' => 'auto',
                'forecast_days' => max($days, 3),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $daily = $data['daily'] ?? [];
                $result = [];
                if (!empty($daily['time'])) {
                    foreach ($daily['time'] as $index => $date) {
                        if ($index >= $days) {
                            break;
                        }
                        $weatherCode = $daily['weathercode'][$index] ?? null;
                        $result[] = [
                            'date' => $date,
                            'temperature_max' => $daily['temperature_2m_max'][$index] ?? null,
                            'temperature_min' => $daily['temperature_2m_min'][$index] ?? null,
                            'precipitation_probability' => $daily['precipitation_probability_mean'][$index] ?? null,
                            'weather_code' => $weatherCode,
                            'description' => $this->mapWeatherCode($weatherCode)['description'],
                            'icon' => $this->mapWeatherCode($weatherCode)['icon'],
                        ];
                    }
                }
                return $result;
            }
        } catch (\Exception $e) {
            // ignore
        }

        return [];
    }

    private function mapWeatherCode($code)
    {
        $mapping = [
            0 => ['description' => 'Clear sky', 'icon' => '☀️'],
            1 => ['description' => 'Mainly clear', 'icon' => '🌤️'],
            2 => ['description' => 'Partly cloudy', 'icon' => '⛅'],
            3 => ['description' => 'Overcast', 'icon' => '☁️'],
            45 => ['description' => 'Fog', 'icon' => '🌫️'],
            48 => ['description' => 'Depositing rime fog', 'icon' => '🌫️'],
            51 => ['description' => 'Light drizzle', 'icon' => '🌦️'],
            53 => ['description' => 'Moderate drizzle', 'icon' => '🌦️'],
            55 => ['description' => 'Dense drizzle', 'icon' => '🌧️'],
            61 => ['description' => 'Slight rain', 'icon' => '🌧️'],
            63 => ['description' => 'Moderate rain', 'icon' => '🌧️'],
            65 => ['description' => 'Heavy rain', 'icon' => '🌧️'],
            71 => ['description' => 'Slight snow fall', 'icon' => '❄️'],
            73 => ['description' => 'Moderate snow fall', 'icon' => '❄️'],
            75 => ['description' => 'Heavy snow fall', 'icon' => '❄️'],
            80 => ['description' => 'Rain showers', 'icon' => '🌦️'],
            81 => ['description' => 'Moderate rain showers', 'icon' => '🌧️'],
            82 => ['description' => 'Violent rain showers', 'icon' => '⛈️'],
            95 => ['description' => 'Thunderstorm', 'icon' => '⛈️'],
            99 => ['description' => 'Thunderstorm with hail', 'icon' => '⛈️'],
        ];

        return $mapping[$code] ?? ['description' => 'Weather data unavailable', 'icon' => '❔'];
    }

    private function summarizeWeather(array $forecast)
    {
        if (empty($forecast)) {
            return null;
        }

        $high = max(array_column($forecast, 'temperature_max'));
        $low = min(array_column($forecast, 'temperature_min'));
        $avgPrecip = array_sum(array_column($forecast, 'precipitation_probability')) / max(count($forecast), 1);

        return [
            'summary' => sprintf('Temperatures range from %.0f°C to %.0f°C with average precipitation chance of %.0f%%.', $low, $high, $avgPrecip),
            'high' => $high,
            'low' => $low,
            'average_precipitation' => round($avgPrecip, 1)
        ];
    }

    private function generateDaySchedule($place, ?array $focus, int $day, ?array $weather)
    {
        $focusTitle = $focus['title'] ?? null;
        $baseActivities = [
            'morning' => $focusTitle ? "Guided exploration of {$focusTitle}" : "Morning walk through iconic neighborhoods of {$place}",
            'afternoon' => "Local cuisine tasting and cultural sites in {$place}",
            'evening' => "Sunset viewpoint or river cruise in {$place}"
        ];

        $suggestions = [
            1 => [
                'morning' => "Kick off your adventure with a heritage walking tour.",
                'afternoon' => "Visit top landmarks and enjoy a cafe break.",
                'evening' => "Relax with a scenic dinner and night-time stroll."
            ],
            2 => [
                'morning' => "Head to museums or art districts for immersive exhibits.",
                'afternoon' => "Join a hands-on workshop or market visit.",
                'evening' => "Catch a live performance or cultural show."
            ],
            3 => [
                'morning' => "Take a day trip to nearby attractions or nature spots.",
                'afternoon' => "Sample street food and explore hidden alleys.",
                'evening' => "Experience the nightlife or rooftop views."
            ]
        ];

        $schedule = [];
        foreach ($baseActivities as $slot => $default) {
            $message = $suggestions[$day][$slot] ?? $default;
            if ($focusTitle) {
                $message = str_replace('heritage walking tour', "visit to {$focusTitle}", $message);
            }
            if ($weather && ($weather['precipitation_probability'] ?? 0) > 60 && $slot !== 'evening') {
                $message .= ' (Consider indoor alternatives if it rains)';
            }
            $schedule[$slot] = $message;
        }

        return $schedule;
    }

    private function buildDayTopic(string $place, int $day, ?array $focus)
    {
        $title = $focus['title'] ?? "Exploring {$place}";
        return "Day {$day}: {$title}";
    }

    private function generateLocalCuisineSuggestions($place)
    {
        return [
            "Try authentic dishes or street food markets popular in {$place}.",
            "Visit a local cafe for specialty desserts or beverages unique to {$place}.",
            "Book a tasting menu or food tour to sample regional flavors."
        ];
    }

    private function generateTravelTips($place)
    {
        return [
            "Reserve tickets for popular attractions in {$place} ahead of time to avoid queues.",
            "Use public transport or walking where possible; many highlights in {$place} are within close distance.",
            "Carry a reusable water bottle and stay hydrated while exploring {$place}."
        ];
    }

    private function generatePackingList(array $forecast)
    {
        $items = [
            'Comfortable walking shoes',
            'Portable power bank and local SIM/Wi-Fi eSIM',
            'Reusable water bottle'
        ];

        if (!empty($forecast)) {
            $high = max(array_column($forecast, 'temperature_max'));
            $low = min(array_column($forecast, 'temperature_min'));
            $avgPrecip = array_sum(array_column($forecast, 'precipitation_probability')) / max(count($forecast), 1);

            if ($high >= 28) {
                $items[] = 'Lightweight breathable clothing and sunscreen';
            }
            if ($low <= 10) {
                $items[] = 'Layered clothing or a light jacket for cooler evenings';
            }
            if ($avgPrecip >= 40) {
                $items[] = 'Compact umbrella or waterproof jacket';
            }
        }

        return array_values(array_unique($items));
    }
}

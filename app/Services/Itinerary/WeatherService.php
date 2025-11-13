<?php

namespace App\Services\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteStop;
use App\Models\ItineraryWeatherSnapshot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class WeatherService
{
    protected string $endpoint = 'https://api.open-meteo.com/v1/forecast';

    public function __construct(protected ?string $apiKey = null)
    {
        // Open-Meteo doesn't require an API key, but we keep the parameter for backward compatibility
        $this->apiKey = null;
    }

    /**
     * Sync weather forecast for all stops on the route.
     * 
     * @throws \Exception If all stops fail or if there's a critical error
     */
    public function syncRouteWeather(ItineraryRoute $route, int $days = 7): void
    {
        $errors = [];
        $successCount = 0;
        $totalStops = $route->stops->count();

        foreach ($route->stops as $stop) {
            try {
                $this->syncStopWeather($stop, $days);
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Stop '{$stop->name}': " . $e->getMessage();
                Log::warning('Failed to sync weather for stop, continuing with others', [
                    'stop_id' => $stop->id,
                    'stop_name' => $stop->name,
                    'error' => $e->getMessage(),
                ]);
                // Continue with other stops
            }
        }

        // If all stops failed, throw an exception
        if ($successCount === 0 && !empty($errors)) {
            throw new \Exception("Failed to fetch weather for all stops:\n" . implode("\n", $errors));
        }

        // If some stops failed, log but don't throw (partial success)
        if (!empty($errors)) {
            Log::warning('Partial weather sync success', [
                'route_id' => $route->id,
                'successful' => $successCount,
                'failed' => count($errors),
                'total' => $totalStops,
                'errors' => $errors,
            ]);
        }
    }

    /**
     * Fetch and cache weather forecast for a single stop.
     */
    public function syncStopWeather(ItineraryRouteStop $stop, int $days = 7): void
    {
        // Validate coordinates exist and are valid
        $latitude = $stop->latitude;
        $longitude = $stop->longitude;

        if ($latitude === null || $longitude === null || $latitude === '' || $longitude === '') {
            Log::warning('Stop missing coordinates', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            return;
        }

        // Convert to float and validate range
        $lat = (float) $latitude;
        $lon = (float) $longitude;

        // Check for zero coordinates (likely placeholder/invalid)
        if ($lat == 0 && $lon == 0) {
            Log::warning('Stop has zero coordinates (likely invalid)', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'latitude' => $lat,
                'longitude' => $lon,
            ]);
            throw new \Exception('Coordinates are set to 0,0 which is invalid. Please update the stop coordinates.');
        }

        if ($lat < -90 || $lat > 90) {
            Log::error('Invalid latitude value', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'latitude' => $lat,
            ]);
            throw new \Exception("Invalid latitude: {$lat}. Latitude must be between -90 and 90.");
        }

        if ($lon < -180 || $lon > 180) {
            Log::error('Invalid longitude value', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'longitude' => $lon,
            ]);
            throw new \Exception("Invalid longitude: {$lon}. Longitude must be between -180 and 180.");
        }

        try {
            $forecast = $this->fetchForecast($lat, $lon, $days);
        } catch (\Exception $e) {
            Log::error('Failed to fetch forecast for stop', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'latitude' => $lat,
                'longitude' => $lon,
                'error' => $e->getMessage(),
            ]);
            // Re-throw to be caught by the Livewire component
            throw new \Exception("Failed to fetch weather for '{$stop->name}': " . $e->getMessage());
        }

        if (empty($forecast)) {
            Log::warning('No forecast data returned for stop', [
                'stop_id' => $stop->id,
                'stop_name' => $stop->name,
                'latitude' => $lat,
                'longitude' => $lon,
            ]);
            return;
        }

        foreach ($forecast as $day) {
            ItineraryWeatherSnapshot::updateOrCreate(
                [
                    'stop_id' => $stop->id,
                    'forecast_date' => $day['date'],
                ],
                [
                    'payload' => $day['payload'],
                    'fetched_at' => now(),
                ]
            );
        }

        Log::info('Weather data synced for stop', [
            'stop_id' => $stop->id,
            'stop_name' => $stop->name,
            'forecast_days' => count($forecast),
        ]);
    }

    /**
     * Fetch forecast from Open-Meteo (free weather API).
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchForecast(float $latitude, float $longitude, int $days = 7): array
    {
        try {
            // Validate coordinates before making API call
            if ($latitude < -90 || $latitude > 90) {
                throw new \Exception("Invalid latitude: {$latitude}. Latitude must be between -90 and 90.");
            }

            if ($longitude < -180 || $longitude > 180) {
                throw new \Exception("Invalid longitude: {$longitude}. Longitude must be between -180 and 180.");
            }

            // Limit days to 16 (Open-Meteo free tier limit)
            $days = min($days, 16);

            // Ensure coordinates are properly formatted (remove any trailing zeros if needed)
            $lat = round($latitude, 7);
            $lon = round($longitude, 7);

            $params = [
                'latitude' => $lat,
                'longitude' => $lon,
                'daily' => 'weathercode,temperature_2m_max,temperature_2m_min,windspeed_10m_max,windspeed_10m_min,winddirection_10m_dominant,windgusts_10m_max,precipitation_sum,precipitation_probability_max,sunrise,sunset',
                'timezone' => 'auto',
                'forecast_days' => $days,
            ];

            Log::info('Fetching weather forecast from Open-Meteo', [
                'endpoint' => $this->endpoint,
                'params' => $params,
                'latitude' => $lat,
                'longitude' => $lon,
                'days' => $days,
                'lat_type' => gettype($lat),
                'lon_type' => gettype($lon),
            ]);

            $response = Http::timeout(10)->get($this->endpoint, $params);

            // Log the full URL for debugging
            $fullUrl = $this->endpoint . '?' . http_build_query($params);
            Log::info('Open-Meteo API request URL', ['url' => $fullUrl]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                $statusCode = $response->status();
                
                // Try to parse error message from response
                $errorData = json_decode($errorBody, true);
                $errorMessage = $errorData['reason'] ?? $errorData['error'] ?? $errorData['message'] ?? $errorBody;
                
                Log::error('Open-Meteo API request failed', [
                    'status' => $statusCode,
                    'response' => $errorBody,
                    'response_json' => $errorData,
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'lat_type' => gettype($lat),
                    'lon_type' => gettype($lon),
                    'parsed_error' => $errorMessage,
                    'request_url' => $fullUrl,
                ]);
                
                if ($statusCode === 429) {
                    throw new \Exception('Open-Meteo API rate limit exceeded. Please try again later.');
                } elseif ($statusCode === 400) {
                    // Provide more detailed error message
                    $coordsInfo = "Latitude: {$lat} (type: " . gettype($lat) . "), Longitude: {$lon} (type: " . gettype($lon) . ")";
                    $apiError = $errorMessage && $errorMessage !== $errorBody ? " API error: " . substr($errorMessage, 0, 150) : "";
                    $detailedError = "Invalid coordinates. {$coordsInfo}.{$apiError} Please verify coordinates are valid decimal numbers between -90 to 90 (latitude) and -180 to 180 (longitude).";
                    throw new \Exception($detailedError);
                } else {
                    throw new \Exception("Open-Meteo API error (Status: {$statusCode}): " . substr($errorMessage, 0, 200));
                }
            }

            $data = $response->json();
            
            if (!isset($data['daily']) || !is_array($data['daily'])) {
                Log::warning('Open-Meteo API response missing daily forecast', [
                    'response_keys' => array_keys($data),
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ]);
                return [];
            }

            $daily = $data['daily'];
            $timezone = $data['timezone'] ?? 'UTC';

            // Map Open-Meteo weather codes to descriptions
            $weatherCodes = $this->getWeatherCodeDescriptions();

            $forecast = [];
            $dateCount = count($daily['time'] ?? []);

            for ($i = 0; $i < $dateCount && $i < $days; $i++) {
                $date = $daily['time'][$i] ?? null;
                if (!$date) {
                    continue;
                }

                $weatherCode = $daily['weathercode'][$i] ?? null;
                $weatherInfo = $weatherCodes[$weatherCode] ?? ['main' => 'Unknown', 'description' => 'Unknown weather condition'];

                // Convert sunrise/sunset from ISO 8601 to H:i format
                $sunrise = null;
                $sunset = null;
                if (isset($daily['sunrise'][$i])) {
                    try {
                        $sunriseTime = new \DateTime($daily['sunrise'][$i], new \DateTimeZone($timezone));
                        $sunrise = $sunriseTime->format('H:i');
                    } catch (\Exception $e) {
                        Log::warning('Failed to parse sunrise time', ['time' => $daily['sunrise'][$i] ?? null]);
                    }
                }
                if (isset($daily['sunset'][$i])) {
                    try {
                        $sunsetTime = new \DateTime($daily['sunset'][$i], new \DateTimeZone($timezone));
                        $sunset = $sunsetTime->format('H:i');
                    } catch (\Exception $e) {
                        Log::warning('Failed to parse sunset time', ['time' => $daily['sunset'][$i] ?? null]);
                    }
                }

                // Calculate average wind speed from max and min, convert from km/h to m/s
                $windSpeedMax = $daily['windspeed_10m_max'][$i] ?? null;
                $windSpeedMin = $daily['windspeed_10m_min'][$i] ?? null;
                $windSpeed = null;
                if ($windSpeedMax !== null) {
                    if ($windSpeedMin !== null) {
                        // Average of max and min
                        $windSpeed = (($windSpeedMax + $windSpeedMin) / 2) / 3.6; // Convert km/h to m/s
                    } else {
                        // Use max only
                        $windSpeed = $windSpeedMax / 3.6; // Convert km/h to m/s
                    }
                }

                // Convert wind gusts from km/h to m/s
                $windGust = null;
                if (isset($daily['windgusts_10m_max'][$i])) {
                    $windGust = $daily['windgusts_10m_max'][$i] / 3.6; // Convert km/h to m/s
                }

                $forecast[] = [
                    'date' => $date,
                    'payload' => [
                        'temperature' => [
                            'min' => $daily['temperature_2m_min'][$i] ?? null,
                            'max' => $daily['temperature_2m_max'][$i] ?? null,
                        ],
                        'wind' => [
                            'speed' => $windSpeed,
                            'deg' => $daily['winddirection_10m_dominant'][$i] ?? null,
                            'gust' => $windGust,
                        ],
                        'conditions' => [
                            'code' => $weatherCode,
                            'main' => $weatherInfo['main'],
                            'description' => $weatherInfo['description'],
                            'icon' => $this->getWeatherIcon($weatherCode),
                        ],
                        'waves' => [
                            'height' => null, // Open-Meteo doesn't provide wave data in free tier
                            'direction' => null,
                        ],
                        'precipitation_probability' => isset($daily['precipitation_probability_max'][$i]) && $daily['precipitation_probability_max'][$i] !== null
                            ? ($daily['precipitation_probability_max'][$i] / 100) 
                            : null,
                        'precipitation_sum' => $daily['precipitation_sum'][$i] ?? null,
                        'sunrise' => $sunrise,
                        'sunset' => $sunset,
                    ],
                ];
            }

            return $forecast;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Open-Meteo API connection failed', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            throw new \Exception('Failed to connect to Open-Meteo API. Please check your internet connection.');
        } catch (Throwable $e) {
            Log::error('Open-Meteo API error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'latitude' => $latitude,
                'longitude' => $longitude,
            ]);
            // Re-throw if it's already a user-friendly exception
            if ($e instanceof \Exception && str_contains($e->getMessage(), 'Open-Meteo')) {
                throw $e;
            }
            report($e);
            throw new \Exception('Failed to fetch weather data: ' . $e->getMessage());
        }
    }

    /**
     * Get weather code descriptions based on WMO Weather interpretation codes.
     * 
     * @return array<int, array<string, string>>
     */
    protected function getWeatherCodeDescriptions(): array
    {
        return [
            0 => ['main' => 'Clear', 'description' => 'clear sky'],
            1 => ['main' => 'Clear', 'description' => 'mainly clear'],
            2 => ['main' => 'Clouds', 'description' => 'partly cloudy'],
            3 => ['main' => 'Clouds', 'description' => 'overcast'],
            45 => ['main' => 'Fog', 'description' => 'foggy'],
            48 => ['main' => 'Fog', 'description' => 'depositing rime fog'],
            51 => ['main' => 'Drizzle', 'description' => 'light drizzle'],
            53 => ['main' => 'Drizzle', 'description' => 'moderate drizzle'],
            55 => ['main' => 'Drizzle', 'description' => 'dense drizzle'],
            56 => ['main' => 'Drizzle', 'description' => 'light freezing drizzle'],
            57 => ['main' => 'Drizzle', 'description' => 'dense freezing drizzle'],
            61 => ['main' => 'Rain', 'description' => 'slight rain'],
            63 => ['main' => 'Rain', 'description' => 'moderate rain'],
            65 => ['main' => 'Rain', 'description' => 'heavy rain'],
            66 => ['main' => 'Rain', 'description' => 'light freezing rain'],
            67 => ['main' => 'Rain', 'description' => 'heavy freezing rain'],
            71 => ['main' => 'Snow', 'description' => 'slight snow fall'],
            73 => ['main' => 'Snow', 'description' => 'moderate snow fall'],
            75 => ['main' => 'Snow', 'description' => 'heavy snow fall'],
            77 => ['main' => 'Snow', 'description' => 'snow grains'],
            80 => ['main' => 'Rain', 'description' => 'slight rain showers'],
            81 => ['main' => 'Rain', 'description' => 'moderate rain showers'],
            82 => ['main' => 'Rain', 'description' => 'violent rain showers'],
            85 => ['main' => 'Snow', 'description' => 'slight snow showers'],
            86 => ['main' => 'Snow', 'description' => 'heavy snow showers'],
            95 => ['main' => 'Thunderstorm', 'description' => 'thunderstorm'],
            96 => ['main' => 'Thunderstorm', 'description' => 'thunderstorm with slight hail'],
            99 => ['main' => 'Thunderstorm', 'description' => 'thunderstorm with heavy hail'],
        ];
    }

    /**
     * Get weather icon based on weather code.
     * Maps to OpenWeatherMap icon names for compatibility with existing views.
     */
    protected function getWeatherIcon(?int $weatherCode): ?string
    {
        if ($weatherCode === null) {
            return null;
        }

        // Map weather codes to OpenWeatherMap-style icon names
        $iconMap = [
            0 => '01d', // clear sky
            1 => '02d', // mainly clear
            2 => '03d', // partly cloudy
            3 => '04d', // overcast
            45 => '50d', // fog
            48 => '50d', // fog
            51 => '09d', // drizzle
            53 => '09d',
            55 => '09d',
            56 => '09d',
            57 => '09d',
            61 => '10d', // rain
            63 => '10d',
            65 => '10d',
            66 => '10d',
            67 => '10d',
            71 => '13d', // snow
            73 => '13d',
            75 => '13d',
            77 => '13d',
            80 => '09d', // rain showers
            81 => '09d',
            82 => '09d',
            85 => '13d', // snow showers
            86 => '13d',
            95 => '11d', // thunderstorm
            96 => '11d',
            99 => '11d',
        ];

        return $iconMap[$weatherCode] ?? '02d';
    }

    /**
     * Provide a human-readable summary from snapshot payload.
     */
    public function summarize(array $payload): string
    {
        $temp = $payload['temperature'] ?? [];
        $conditions = $payload['conditions']['description'] ?? 'Weather data unavailable';
        $wind = $payload['wind'] ?? [];

        $parts = [
            Str::ucfirst($conditions),
        ];

        if (!empty($temp['min']) && !empty($temp['max'])) {
            $parts[] = sprintf('Temps %s-%s°C', round($temp['min']), round($temp['max']));
        }

        if (!empty($wind['speed'])) {
            $parts[] = sprintf('Wind %s m/s', round($wind['speed'], 1));
        }

        if (!empty($payload['precipitation_probability'])) {
            $parts[] = sprintf('Precip %.0f%%', $payload['precipitation_probability'] * 100);
        }

        return implode(' • ', $parts);
    }
}


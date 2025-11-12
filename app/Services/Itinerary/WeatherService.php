<?php

namespace App\Services\Itinerary;

use App\Models\ItineraryRoute;
use App\Models\ItineraryRouteStop;
use App\Models\ItineraryWeatherSnapshot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class WeatherService
{
    protected string $endpoint = 'https://api.openweathermap.org/data/2.5/onecall';

    public function __construct(protected ?string $apiKey = null)
    {
        $this->apiKey ??= config('services.openweather.api_key');
    }

    /**
     * Sync weather forecast for all stops on the route.
     */
    public function syncRouteWeather(ItineraryRoute $route, int $days = 7): void
    {
        foreach ($route->stops as $stop) {
            $this->syncStopWeather($stop, $days);
        }
    }

    /**
     * Fetch and cache weather forecast for a single stop.
     */
    public function syncStopWeather(ItineraryRouteStop $stop, int $days = 7): void
    {
        if (!$this->apiKey || !$stop->latitude || !$stop->longitude) {
            return;
        }

        $forecast = $this->fetchForecast((float) $stop->latitude, (float) $stop->longitude, $days);

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
    }

    /**
     * Fetch forecast from OpenWeatherMap.
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchForecast(float $latitude, float $longitude, int $days = 7): array
    {
        try {
            $response = Http::get($this->endpoint, [
                'lat' => $latitude,
                'lon' => $longitude,
                'exclude' => 'minutely,hourly,alerts',
                'units' => 'metric',
                'appid' => $this->apiKey,
            ])->throw();

            $data = $response->json();
            $daily = $data['daily'] ?? [];

            return collect($daily)
                ->take($days)
                ->map(function (array $day) use ($data) {
                    return [
                        'date' => date('Y-m-d', $day['dt']),
                        'payload' => [
                            'temperature' => [
                                'min' => $day['temp']['min'] ?? null,
                                'max' => $day['temp']['max'] ?? null,
                            ],
                            'wind' => [
                                'speed' => $day['wind_speed'] ?? null,
                                'deg' => $day['wind_deg'] ?? null,
                                'gust' => $day['wind_gust'] ?? null,
                            ],
                            'conditions' => [
                                'code' => $day['weather'][0]['id'] ?? null,
                                'main' => $day['weather'][0]['main'] ?? null,
                                'description' => $day['weather'][0]['description'] ?? null,
                                'icon' => $day['weather'][0]['icon'] ?? null,
                            ],
                            'waves' => [
                                'height' => $data['waves']['height'] ?? null,
                                'direction' => $data['waves']['direction'] ?? null,
                            ],
                            'precipitation_probability' => $day['pop'] ?? null,
                            'sunrise' => isset($day['sunrise']) ? date('H:i', $day['sunrise']) : null,
                            'sunset' => isset($day['sunset']) ? date('H:i', $day['sunset']) : null,
                        ],
                    ];
                })
                ->all();
        } catch (Throwable $e) {
            report($e);
            return [];
        }
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


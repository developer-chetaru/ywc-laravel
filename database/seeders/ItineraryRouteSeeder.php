<?php

namespace Database\Seeders;

use App\Models\ItineraryRoute;
use App\Models\User;
use App\Services\Itinerary\RouteBuilder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItineraryRouteSeeder extends Seeder
{
    /**
     * Download and store an image from URL
     */
    protected function downloadAndStoreImage(string $url, string $directory): ?string
    {
        try {
            $response = Http::timeout(10)->get($url);
            
            if (!$response->successful()) {
                $this->command->warn("Failed to download image: {$url}");
                return null;
            }

            // Get file extension from URL or detect from content type
            $parsedUrl = parse_url($url);
            $path = $parsedUrl['path'] ?? '';
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            
            // If no extension in URL, try to detect from content type
            if (empty($extension)) {
                $contentType = $response->header('Content-Type');
                if (str_contains($contentType, 'jpeg') || str_contains($contentType, 'jpg')) {
                    $extension = 'jpg';
                } elseif (str_contains($contentType, 'png')) {
                    $extension = 'png';
                } elseif (str_contains($contentType, 'webp')) {
                    $extension = 'webp';
                } else {
                    $extension = 'jpg'; // Default
                }
            }

            // Clean extension (remove query params if any)
            $extension = strtolower(explode('?', $extension)[0]);
            
            $filename = Str::random(40) . '.' . $extension;
            $filePath = $directory . '/' . $filename;

            // Ensure directory exists
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Store the image
            Storage::disk('public')->put($filePath, $response->body());

            return $filePath;
        } catch (\Exception $e) {
            $this->command->warn("Error downloading image {$url}: {$e->getMessage()}");
            return null;
        }
    }

    /**
     * Process images array - download and store each image
     */
    protected function processImages(array $urls, string $directory): array
    {
        $paths = [];
        foreach ($urls as $url) {
            $path = $this->downloadAndStoreImage($url, $directory);
            if ($path) {
                $paths[] = $path;
            }
            // Small delay to avoid rate limiting
            usleep(200000); // 0.2 seconds
        }
        return $paths;
    }

    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->warn('No user found. Please create a user first.');
            return;
        }

        $builder = app(RouteBuilder::class);

        $routes = [
            [
                'title' => 'Mediterranean Classic: French Riviera to Amalfi Coast',
                'description' => 'Experience the best of the Mediterranean on this stunning 7-day journey from the glamorous French Riviera to the breathtaking Amalfi Coast. Discover charming ports, crystal-clear waters, and world-class cuisine.',
                'region' => 'Mediterranean',
                'difficulty' => 'moderate',
                'season' => 'summer',
                'visibility' => 'public',
                'status' => 'active',
                'cover_image' => 'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=1200&h=600&fit=crop',
                'start_date' => Carbon::now()->addMonths(2)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(2)->addDays(6)->format('Y-m-d'),
                'tags' => ['mediterranean', 'france', 'italy', 'luxury', 'summer'],
                'stops' => [
                    [
                        'name' => 'Nice, France',
                        'location_label' => 'Port of Nice',
                        'latitude' => 43.7102,
                        'longitude' => 7.2620,
                        'day_number' => 1,
                        'sequence' => 1,
                        'stay_duration_hours' => 24,
                        'notes' => 'Explore the Promenade des Anglais and Old Town. Perfect for provisioning.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Monaco',
                        'location_label' => 'Port Hercule',
                        'latitude' => 43.7384,
                        'longitude' => 7.4246,
                        'day_number' => 2,
                        'sequence' => 2,
                        'stay_duration_hours' => 12,
                        'notes' => 'Visit the Prince\'s Palace and Monte Carlo Casino. Clearance required.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1539650116574-75c0c6d73a6e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Portofino, Italy',
                        'location_label' => 'Portofino Harbor',
                        'latitude' => 44.3038,
                        'longitude' => 9.2094,
                        'day_number' => 3,
                        'sequence' => 3,
                        'stay_duration_hours' => 18,
                        'notes' => 'Charming fishing village. Great for hiking and dining.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Cinque Terre',
                        'location_label' => 'Monterosso al Mare',
                        'latitude' => 44.1477,
                        'longitude' => 9.6547,
                        'day_number' => 4,
                        'sequence' => 4,
                        'stay_duration_hours' => 20,
                        'notes' => 'Explore the five colorful villages. UNESCO World Heritage site.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1539650116574-75c0c6d73a6e?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Portofino (Return)',
                        'location_label' => 'Portofino Harbor',
                        'latitude' => 44.3038,
                        'longitude' => 9.2094,
                        'day_number' => 5,
                        'sequence' => 5,
                        'stay_duration_hours' => 12,
                        'notes' => 'Return stop for provisions and fuel.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Amalfi',
                        'location_label' => 'Amalfi Port',
                        'latitude' => 40.6340,
                        'longitude' => 14.6027,
                        'day_number' => 6,
                        'sequence' => 6,
                        'stay_duration_hours' => 24,
                        'notes' => 'Historic maritime republic. Visit the cathedral and paper museum.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Positano',
                        'location_label' => 'Positano Marina',
                        'latitude' => 40.6281,
                        'longitude' => 14.4848,
                        'day_number' => 7,
                        'sequence' => 7,
                        'stay_duration_hours' => 24,
                        'notes' => 'Final destination. Stunning cliffside village. Perfect for photos.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1515542622106-78bda8ba0e5b?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1539650116574-75c0c6d73a6e?w=800&h=600&fit=crop',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Caribbean Adventure: St. Maarten to British Virgin Islands',
                'description' => 'Sail through the turquoise waters of the Caribbean on this 10-day adventure. From the vibrant culture of St. Maarten to the pristine beaches of the BVI, experience the ultimate Caribbean sailing experience.',
                'region' => 'Caribbean',
                'difficulty' => 'easy',
                'season' => 'winter',
                'visibility' => 'public',
                'status' => 'active',
                'cover_image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=1200&h=600&fit=crop',
                'start_date' => Carbon::now()->addMonths(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(1)->addDays(9)->format('Y-m-d'),
                'tags' => ['caribbean', 'bvi', 'st-maarten', 'winter', 'beaches'],
                'stops' => [
                    [
                        'name' => 'Philipsburg, St. Maarten',
                        'location_label' => 'Great Bay Marina',
                        'latitude' => 18.0296,
                        'longitude' => -63.0471,
                        'day_number' => 1,
                        'sequence' => 1,
                        'stay_duration_hours' => 24,
                        'notes' => 'Duty-free shopping and great restaurants. Clearance port.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Anguilla',
                        'location_label' => 'Road Bay',
                        'latitude' => 18.2206,
                        'longitude' => -63.0686,
                        'day_number' => 2,
                        'sequence' => 2,
                        'stay_duration_hours' => 18,
                        'notes' => 'Beautiful beaches and luxury resorts. Snorkeling at Shoal Bay.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'St. Barts',
                        'location_label' => 'Gustavia Harbor',
                        'latitude' => 17.8969,
                        'longitude' => -62.8498,
                        'day_number' => 3,
                        'sequence' => 3,
                        'stay_duration_hours' => 20,
                        'notes' => 'Luxury destination. High-end shopping and dining.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Virgin Gorda, BVI',
                        'location_label' => 'Spanish Town',
                        'latitude' => 18.4283,
                        'longitude' => -64.4278,
                        'day_number' => 4,
                        'sequence' => 4,
                        'stay_duration_hours' => 24,
                        'notes' => 'Visit The Baths - unique rock formations. Clearance required.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Tortola, BVI',
                        'location_label' => 'Road Town',
                        'latitude' => 18.4207,
                        'longitude' => -64.6200,
                        'day_number' => 5,
                        'sequence' => 5,
                        'stay_duration_hours' => 12,
                        'notes' => 'Capital of BVI. Provisioning and fuel available.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Jost Van Dyke',
                        'location_label' => 'Great Harbour',
                        'latitude' => 18.4483,
                        'longitude' => -64.7500,
                        'day_number' => 6,
                        'sequence' => 6,
                        'stay_duration_hours' => 20,
                        'notes' => 'Famous for Foxy\'s Bar. Beautiful beaches and relaxed atmosphere.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Anegada',
                        'location_label' => 'Setting Point',
                        'latitude' => 18.7275,
                        'longitude' => -64.3278,
                        'day_number' => 7,
                        'sequence' => 7,
                        'stay_duration_hours' => 24,
                        'notes' => 'Flat coral island. World-class snorkeling and lobster dining.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Norman Island',
                        'location_label' => 'The Bight',
                        'latitude' => 18.3167,
                        'longitude' => -64.6167,
                        'day_number' => 8,
                        'sequence' => 8,
                        'stay_duration_hours' => 18,
                        'notes' => 'Pirate legends. Great snorkeling at The Caves.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Peter Island',
                        'location_label' => 'Great Harbour',
                        'latitude' => 18.3500,
                        'longitude' => -64.5833,
                        'day_number' => 9,
                        'sequence' => 9,
                        'stay_duration_hours' => 20,
                        'notes' => 'Luxury resort island. Private beaches and fine dining.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Tortola (Final)',
                        'location_label' => 'Nanny Cay Marina',
                        'latitude' => 18.4000,
                        'longitude' => -64.6167,
                        'day_number' => 10,
                        'sequence' => 10,
                        'stay_duration_hours' => 24,
                        'notes' => 'Final stop. Marina facilities and departure point.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Greek Islands Discovery: Athens to Santorini',
                'description' => 'Explore the ancient wonders and stunning beauty of the Greek Islands. From the historic capital of Athens to the iconic sunsets of Santorini, this 5-day journey showcases the best of Greece.',
                'region' => 'Mediterranean',
                'difficulty' => 'moderate',
                'season' => 'summer',
                'visibility' => 'public',
                'status' => 'completed',
                'cover_image' => 'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=1200&h=600&fit=crop',
                'start_date' => Carbon::now()->subMonths(1)->format('Y-m-d'),
                'end_date' => Carbon::now()->subMonths(1)->addDays(4)->format('Y-m-d'),
                'tags' => ['greece', 'santorini', 'mykonos', 'ancient', 'islands'],
                'stops' => [
                    [
                        'name' => 'Athens (Piraeus)',
                        'location_label' => 'Piraeus Port',
                        'latitude' => 37.9420,
                        'longitude' => 23.6462,
                        'day_number' => 1,
                        'sequence' => 1,
                        'stay_duration_hours' => 24,
                        'notes' => 'Visit Acropolis and ancient sites. Clearance port.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Mykonos',
                        'location_label' => 'Mykonos Old Port',
                        'latitude' => 37.4467,
                        'longitude' => 25.3289,
                        'day_number' => 2,
                        'sequence' => 2,
                        'stay_duration_hours' => 20,
                        'notes' => 'Famous for nightlife and windmills. Beautiful beaches.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Paros',
                        'location_label' => 'Parikia Port',
                        'latitude' => 37.0853,
                        'longitude' => 25.1472,
                        'day_number' => 3,
                        'sequence' => 3,
                        'stay_duration_hours' => 18,
                        'notes' => 'Traditional Greek island. Great for authentic experiences.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Naxos',
                        'location_label' => 'Naxos Port',
                        'latitude' => 37.1036,
                        'longitude' => 25.3764,
                        'day_number' => 4,
                        'sequence' => 4,
                        'stay_duration_hours' => 20,
                        'notes' => 'Largest Cycladic island. Ancient ruins and beautiful beaches.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Santorini',
                        'location_label' => 'Fira Port',
                        'latitude' => 36.3932,
                        'longitude' => 25.4615,
                        'day_number' => 5,
                        'sequence' => 5,
                        'stay_duration_hours' => 24,
                        'notes' => 'Iconic sunsets, white-washed buildings, and volcanic beaches.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1613395877344-13d4a8e0d49e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Baltic Sea Explorer: Stockholm to Helsinki',
                'description' => 'Navigate the historic waters of the Baltic Sea on this 6-day journey through Scandinavia. Experience the unique culture, stunning architecture, and pristine nature of the Nordic region.',
                'region' => 'Baltic',
                'difficulty' => 'moderate',
                'season' => 'summer',
                'visibility' => 'public',
                'status' => 'draft',
                'cover_image' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&h=600&fit=crop',
                'start_date' => Carbon::now()->addMonths(3)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(3)->addDays(5)->format('Y-m-d'),
                'tags' => ['baltic', 'sweden', 'finland', 'scandinavia', 'summer'],
                'stops' => [
                    [
                        'name' => 'Stockholm',
                        'location_label' => 'Stockholm Harbor',
                        'latitude' => 59.3293,
                        'longitude' => 18.0686,
                        'day_number' => 1,
                        'sequence' => 1,
                        'stay_duration_hours' => 24,
                        'notes' => 'Venice of the North. Visit Gamla Stan and Vasa Museum.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Åland Islands',
                        'location_label' => 'Mariehamn',
                        'latitude' => 60.0973,
                        'longitude' => 19.9348,
                        'day_number' => 2,
                        'sequence' => 2,
                        'stay_duration_hours' => 18,
                        'notes' => 'Autonomous Finnish region. Beautiful archipelago.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Turku, Finland',
                        'location_label' => 'Turku Harbor',
                        'latitude' => 60.4518,
                        'longitude' => 22.2666,
                        'day_number' => 3,
                        'sequence' => 3,
                        'stay_duration_hours' => 20,
                        'notes' => 'Former capital. Medieval castle and cathedral.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Porvoo',
                        'location_label' => 'Porvoo Old Town',
                        'latitude' => 60.3931,
                        'longitude' => 25.6639,
                        'day_number' => 4,
                        'sequence' => 4,
                        'stay_duration_hours' => 16,
                        'notes' => 'Second oldest town in Finland. Charming wooden houses.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Helsinki',
                        'location_label' => 'Helsinki Harbor',
                        'latitude' => 60.1699,
                        'longitude' => 24.9384,
                        'day_number' => 5,
                        'sequence' => 5,
                        'stay_duration_hours' => 24,
                        'notes' => 'Design capital. Visit Suomenlinna fortress.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Tallinn, Estonia',
                        'location_label' => 'Tallinn Old City Harbor',
                        'latitude' => 59.4370,
                        'longitude' => 24.7536,
                        'day_number' => 6,
                        'sequence' => 6,
                        'stay_duration_hours' => 24,
                        'notes' => 'Medieval old town. UNESCO World Heritage site.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1539650116574-75c0c6d73a6e?w=800&h=600&fit=crop',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Pacific Paradise: Tahiti to Bora Bora',
                'description' => 'Experience the ultimate tropical paradise on this 4-day journey through French Polynesia. From the vibrant culture of Tahiti to the iconic overwater bungalows of Bora Bora, discover why this is a sailor\'s dream destination.',
                'region' => 'Pacific',
                'difficulty' => 'easy',
                'season' => 'winter',
                'visibility' => 'public',
                'status' => 'active',
                'is_featured' => true,
                'cover_image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&h=600&fit=crop',
                'start_date' => Carbon::now()->addMonths(4)->format('Y-m-d'),
                'end_date' => Carbon::now()->addMonths(4)->addDays(3)->format('Y-m-d'),
                'tags' => ['pacific', 'tahiti', 'bora-bora', 'polynesia', 'tropical'],
                'stops' => [
                    [
                        'name' => 'Papeete, Tahiti',
                        'location_label' => 'Port of Papeete',
                        'latitude' => -17.5390,
                        'longitude' => -149.5686,
                        'day_number' => 1,
                        'sequence' => 1,
                        'stay_duration_hours' => 24,
                        'notes' => 'Capital of French Polynesia. Clearance and provisioning.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Moorea',
                        'location_label' => 'Opunohu Bay',
                        'latitude' => -17.5388,
                        'longitude' => -149.8295,
                        'day_number' => 2,
                        'sequence' => 2,
                        'stay_duration_hours' => 20,
                        'notes' => 'Stunning volcanic peaks. Excellent snorkeling and diving.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Raiatea',
                        'location_label' => 'Uturoa',
                        'latitude' => -16.7294,
                        'longitude' => -151.4447,
                        'day_number' => 3,
                        'sequence' => 3,
                        'stay_duration_hours' => 18,
                        'notes' => 'Sacred island. Ancient marae (temples).',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                        ],
                    ],
                    [
                        'name' => 'Bora Bora',
                        'location_label' => 'Vaitape',
                        'latitude' => -16.5004,
                        'longitude' => -151.7415,
                        'day_number' => 4,
                        'sequence' => 4,
                        'stay_duration_hours' => 24,
                        'notes' => 'Iconic destination. Mount Otemanu and crystal-clear lagoon.',
                        'photos' => [
                            'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                            'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=800&h=600&fit=crop',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($routes as $routeData) {
            try {
                // Process cover image
                if (isset($routeData['cover_image']) && filter_var($routeData['cover_image'], FILTER_VALIDATE_URL)) {
                    $coverPath = $this->downloadAndStoreImage($routeData['cover_image'], 'route-covers');
                    if ($coverPath) {
                        $routeData['cover_image'] = $coverPath;
                        $this->command->info("  Downloaded cover image: {$coverPath}");
                    } else {
                        unset($routeData['cover_image']);
                    }
                }

                // Process stop photos
                if (isset($routeData['stops']) && is_array($routeData['stops'])) {
                    foreach ($routeData['stops'] as &$stop) {
                        if (isset($stop['photos']) && is_array($stop['photos'])) {
                            $photoPaths = $this->processImages($stop['photos'], 'route-stops');
                            $stop['photos'] = $photoPaths;
                            if (!empty($photoPaths)) {
                                $this->command->info("  Downloaded " . count($photoPaths) . " photos for stop: {$stop['name']}");
                            }
                        }
                    }
                    unset($stop); // Break reference
                }

                $route = $builder->createRoute($user, $routeData);
                $this->command->info("✅ Created route: {$route->title} (ID: {$route->id})");
            } catch (\Exception $e) {
                $this->command->error("❌ Failed to create route: {$routeData['title']} - {$e->getMessage()}");
            }
        }

        $this->command->info('✅ Itinerary routes seeded successfully!');
    }
}

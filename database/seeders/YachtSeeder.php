<?php

namespace Database\Seeders;

use App\Models\Yacht;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class YachtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing yachts if needed (optional - comment out if you want to keep existing data)
        // Yacht::truncate();
        
        $yachts = [
            [
                'name' => 'Ocean Dream',
                'type' => 'motor_yacht',
                'length_meters' => 45.7,
                'length_feet' => 150,
                'year_built' => 2018,
                'flag_registry' => 'United States',
                'home_port' => 'Fort Lauderdale, FL',
                'builder' => 'Lürssen',
                'crew_capacity' => 12,
                'guest_capacity' => 10,
                'status' => 'charter',
                'home_region' => 'Caribbean',
                'typical_cruising_grounds' => 'Bahamas, Caribbean, Florida Keys',
            ],
            [
                'name' => 'Sea Breeze',
                'type' => 'sailing_yacht',
                'length_meters' => 38.1,
                'length_feet' => 125,
                'year_built' => 2020,
                'flag_registry' => 'United States',
                'home_port' => 'Newport, RI',
                'builder' => 'Perini Navi',
                'crew_capacity' => 8,
                'guest_capacity' => 8,
                'status' => 'charter',
                'home_region' => 'New England',
                'typical_cruising_grounds' => 'New England, Bermuda, Nova Scotia',
            ],
            [
                'name' => 'Pacific Star',
                'type' => 'motor_yacht',
                'length_meters' => 52.4,
                'length_feet' => 172,
                'year_built' => 2019,
                'flag_registry' => 'United States',
                'home_port' => 'San Diego, CA',
                'builder' => 'Feadship',
                'crew_capacity' => 14,
                'guest_capacity' => 12,
                'status' => 'charter',
                'home_region' => 'Pacific Coast',
                'typical_cruising_grounds' => 'California Coast, Mexico, Pacific Islands',
            ],
            [
                'name' => 'Wind Dancer',
                'type' => 'sailing_yacht',
                'length_meters' => 30.5,
                'length_feet' => 100,
                'year_built' => 2021,
                'flag_registry' => 'United States',
                'home_port' => 'Miami, FL',
                'builder' => 'Oyster Yachts',
                'crew_capacity' => 6,
                'guest_capacity' => 6,
                'status' => 'private',
                'home_region' => 'Caribbean',
                'typical_cruising_grounds' => 'Caribbean, Bahamas, Florida Keys',
            ],
            [
                'name' => 'Atlantic Explorer',
                'type' => 'explorer',
                'length_meters' => 60.0,
                'length_feet' => 197,
                'year_built' => 2017,
                'flag_registry' => 'United States',
                'home_port' => 'Charleston, SC',
                'builder' => 'Damen Yachting',
                'crew_capacity' => 16,
                'guest_capacity' => 14,
                'status' => 'charter',
                'home_region' => 'East Coast',
                'typical_cruising_grounds' => 'East Coast USA, Caribbean, Mediterranean',
            ],
            [
                'name' => 'Island Hopper',
                'type' => 'catamaran',
                'length_meters' => 18.3,
                'length_feet' => 60,
                'year_built' => 2022,
                'flag_registry' => 'United States',
                'home_port' => 'Key West, FL',
                'builder' => 'Lagoon',
                'crew_capacity' => 4,
                'guest_capacity' => 8,
                'status' => 'charter',
                'home_region' => 'Florida Keys',
                'typical_cruising_grounds' => 'Florida Keys, Bahamas, Caribbean',
            ],
            [
                'name' => 'Blue Horizon',
                'type' => 'motor_yacht',
                'length_meters' => 40.2,
                'length_feet' => 132,
                'year_built' => 2020,
                'flag_registry' => 'United States',
                'home_port' => 'Seattle, WA',
                'builder' => 'Westport Yachts',
                'crew_capacity' => 10,
                'guest_capacity' => 8,
                'status' => 'charter',
                'home_region' => 'Pacific Northwest',
                'typical_cruising_grounds' => 'Pacific Northwest, Alaska, British Columbia',
            ],
            [
                'name' => 'Caribbean Queen',
                'type' => 'sailing_yacht',
                'length_meters' => 35.0,
                'length_feet' => 115,
                'year_built' => 2019,
                'flag_registry' => 'United States',
                'home_port' => 'St. Thomas, USVI',
                'builder' => 'Benetti',
                'crew_capacity' => 7,
                'guest_capacity' => 6,
                'status' => 'charter',
                'home_region' => 'Caribbean',
                'typical_cruising_grounds' => 'US Virgin Islands, British Virgin Islands, Leeward Islands',
            ],
            [
                'name' => 'Gulf Stream',
                'type' => 'motor_yacht',
                'length_meters' => 48.8,
                'length_feet' => 160,
                'year_built' => 2018,
                'flag_registry' => 'United States',
                'home_port' => 'Galveston, TX',
                'builder' => 'Azimut',
                'crew_capacity' => 12,
                'guest_capacity' => 10,
                'status' => 'charter',
                'home_region' => 'Gulf Coast',
                'typical_cruising_grounds' => 'Gulf of Mexico, Florida, Texas Coast',
            ],
            [
                'name' => 'Nautilus',
                'type' => 'explorer',
                'length_meters' => 55.0,
                'length_feet' => 180,
                'year_built' => 2016,
                'flag_registry' => 'United States',
                'home_port' => 'Portland, ME',
                'builder' => 'Oceanco',
                'crew_capacity' => 15,
                'guest_capacity' => 12,
                'status' => 'private',
                'home_region' => 'New England',
                'typical_cruising_grounds' => 'New England, Canada, Greenland',
            ],
        ];

        foreach ($yachts as $yachtData) {
            // Check if yacht already exists by slug
            $slug = Str::slug($yachtData['name']);
            $existingYacht = Yacht::where('slug', $slug)->first();
            
            if ($existingYacht) {
                $this->command->info("Yacht '{$yachtData['name']}' already exists, skipping...");
                continue;
            }
            
            $yacht = Yacht::create($yachtData);
            
            // Download and store a placeholder image
            $this->downloadYachtImage($yacht);
            
            $this->command->info("Created yacht: {$yacht->name}");
        }
    }

    private function downloadYachtImage(Yacht $yacht): void
    {
        try {
            // Create yachts directory if it doesn't exist
            $directory = 'yachts';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Use placeholder image service with different images
            $imageUrls = [
                'https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop',
            ];
            
            $imageUrl = $imageUrls[array_rand($imageUrls)];
            
            // Use curl if available, otherwise file_get_contents
            if (function_exists('curl_init')) {
                $ch = curl_init($imageUrl);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $imageContent = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode !== 200 || $imageContent === false) {
                    throw new \Exception("Failed to download image: HTTP $httpCode");
                }
            } else {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'follow_location' => true,
                    ]
                ]);
                $imageContent = @file_get_contents($imageUrl, false, $context);
                
                if ($imageContent === false) {
                    throw new \Exception("Failed to download image");
                }
            }
            
            $filename = Str::slug($yacht->name) . '-' . $yacht->id . '.jpg';
            $path = $directory . '/' . $filename;
            
            Storage::disk('public')->put($path, $imageContent);
            $yacht->cover_image = $path;
            $yacht->save();
        } catch (\Exception $e) {
            \Log::warning("Failed to download image for yacht {$yacht->name}: " . $e->getMessage());
            // Create placeholder instead
            $this->createPlaceholderImage($yacht);
        }
    }

    private function createPlaceholderImage(Yacht $yacht): void
    {
        // Create a simple placeholder using GD if available
        if (function_exists('imagecreatetruecolor')) {
            $width = 800;
            $height = 600;
            $image = imagecreatetruecolor($width, $height);
            
            // Set background color (ocean blue)
            $bgColor = imagecolorallocate($image, 30, 144, 255);
            imagefill($image, 0, 0, $bgColor);
            
            // Add text
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $fontSize = 5;
            $text = $yacht->name;
            $x = ($width - strlen($text) * imagefontwidth($fontSize)) / 2;
            $y = ($height - imagefontheight($fontSize)) / 2;
            imagestring($image, $fontSize, $x, $y, $text, $textColor);
            
            $directory = 'yachts';
            $filename = Str::slug($yacht->name) . '-' . $yacht->id . '.jpg';
            $path = $directory . '/' . $filename;
            
            $tempFile = tempnam(sys_get_temp_dir(), 'yacht_');
            imagejpeg($image, $tempFile, 85);
            imagedestroy($image);
            
            Storage::disk('public')->put($path, file_get_contents($tempFile));
            unlink($tempFile);
            
            $yacht->cover_image = $path;
            $yacht->save();
        }
    }
}


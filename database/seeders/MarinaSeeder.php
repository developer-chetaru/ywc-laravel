<?php

namespace Database\Seeders;

use App\Models\Marina;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarinaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing marinas if needed (optional - comment out if you want to keep existing data)
        // Marina::truncate();
        
        $marinas = [
            [
                'name' => 'Fort Lauderdale Marina',
                'country' => 'United States',
                'region' => 'Florida',
                'city' => 'Fort Lauderdale',
                'address' => '1900 SE 15th St, Fort Lauderdale, FL 33316',
                'latitude' => 26.1224,
                'longitude' => -80.1373,
                'phone' => '+1 (954) 525-4000',
                'email' => 'info@ftlauderdalmarina.com',
                'website' => 'https://www.ftlauderdalmarina.com',
                'type' => 'full_service',
                'total_berths' => 500,
                'max_length_meters' => 100,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Newport Harbor Marina',
                'country' => 'United States',
                'region' => 'Rhode Island',
                'city' => 'Newport',
                'address' => '1 Commercial Wharf, Newport, RI 02840',
                'latitude' => 41.4901,
                'longitude' => -71.3128,
                'phone' => '+1 (401) 846-1600',
                'email' => 'info@newportharbormarina.com',
                'website' => 'https://www.newportharbormarina.com',
                'type' => 'full_service',
                'total_berths' => 300,
                'max_length_meters' => 80,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'San Diego Bay Marina',
                'country' => 'United States',
                'region' => 'California',
                'city' => 'San Diego',
                'address' => '1404 Harbor Island Dr, San Diego, CA 92101',
                'latitude' => 32.7157,
                'longitude' => -117.1611,
                'phone' => '+1 (619) 291-6440',
                'email' => 'info@sandiegobaymarina.com',
                'website' => 'https://www.sandiegobaymarina.com',
                'type' => 'full_service',
                'total_berths' => 400,
                'max_length_meters' => 90,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Miami Beach Marina',
                'country' => 'United States',
                'region' => 'Florida',
                'city' => 'Miami Beach',
                'address' => '300 Alton Rd, Miami Beach, FL 33139',
                'latitude' => 25.7907,
                'longitude' => -80.1300,
                'phone' => '+1 (305) 673-6000',
                'email' => 'info@miamibeachmarina.com',
                'website' => 'https://www.miamibeachmarina.com',
                'type' => 'full_service',
                'total_berths' => 600,
                'max_length_meters' => 120,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Charleston City Marina',
                'country' => 'United States',
                'region' => 'South Carolina',
                'city' => 'Charleston',
                'address' => '17 Lockwood Dr, Charleston, SC 29401',
                'latitude' => 32.7765,
                'longitude' => -79.9311,
                'phone' => '+1 (843) 722-4968',
                'email' => 'info@charlestoncitymarina.com',
                'website' => 'https://www.charlestoncitymarina.com',
                'type' => 'full_service',
                'total_berths' => 350,
                'max_length_meters' => 85,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Key West Bight Marina',
                'country' => 'United States',
                'region' => 'Florida',
                'city' => 'Key West',
                'address' => '201 William St, Key West, FL 33040',
                'latitude' => 24.5551,
                'longitude' => -81.8070,
                'phone' => '+1 (305) 292-8161',
                'email' => 'info@keywestbightmarina.com',
                'website' => 'https://www.keywestbightmarina.com',
                'type' => 'full_service',
                'total_berths' => 250,
                'max_length_meters' => 60,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Seattle Yacht Club Marina',
                'country' => 'United States',
                'region' => 'Washington',
                'city' => 'Seattle',
                'address' => '1807 E Hamlin St, Seattle, WA 98112',
                'latitude' => 47.6062,
                'longitude' => -122.3321,
                'phone' => '+1 (206) 325-1000',
                'email' => 'info@seattleyachtclub.com',
                'website' => 'https://www.seattleyachtclub.com',
                'type' => 'yacht_club',
                'total_berths' => 200,
                'max_length_meters' => 70,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => false,
            ],
            [
                'name' => 'Galveston Yacht Basin',
                'country' => 'United States',
                'region' => 'Texas',
                'city' => 'Galveston',
                'address' => '715 21st St, Galveston, TX 77550',
                'latitude' => 29.3013,
                'longitude' => -94.7977,
                'phone' => '+1 (409) 765-9321',
                'email' => 'info@galvestonyachtbasin.com',
                'website' => 'https://www.galvestonyachtbasin.com',
                'type' => 'full_service',
                'total_berths' => 400,
                'max_length_meters' => 75,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'Portland Yacht Services',
                'country' => 'United States',
                'region' => 'Maine',
                'city' => 'Portland',
                'address' => '58 Fore St, Portland, ME 04101',
                'latitude' => 43.6591,
                'longitude' => -70.2568,
                'phone' => '+1 (207) 774-1067',
                'email' => 'info@portlandyachtservices.com',
                'website' => 'https://www.portlandyachtservices.com',
                'type' => 'full_service',
                'total_berths' => 180,
                'max_length_meters' => 65,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
            [
                'name' => 'St. Thomas Yacht Haven',
                'country' => 'United States',
                'region' => 'US Virgin Islands',
                'city' => 'St. Thomas',
                'address' => '6100 Red Hook Quarters, St. Thomas, VI 00802',
                'latitude' => 18.3381,
                'longitude' => -64.8941,
                'phone' => '+1 (340) 775-9500',
                'email' => 'info@styachthaven.com',
                'website' => 'https://www.styachthaven.com',
                'type' => 'full_service',
                'total_berths' => 150,
                'max_length_meters' => 90,
                'fuel_diesel' => true,
                'fuel_gasoline' => true,
                'water_available' => true,
                'electricity_available' => true,
                'wifi_available' => true,
                'showers_available' => true,
                'laundry_available' => true,
                'maintenance_available' => true,
                'provisioning_available' => true,
            ],
        ];

        foreach ($marinas as $marinaData) {
            // Check if marina already exists by slug
            $slug = Str::slug($marinaData['name'] . ' ' . $marinaData['city']);
            $existingMarina = Marina::where('slug', $slug)->first();
            
            if ($existingMarina) {
                $this->command->info("Marina '{$marinaData['name']}' already exists, skipping...");
                continue;
            }
            
            $marina = Marina::create($marinaData);
            
            // Download and store a placeholder image
            $this->downloadMarinaImage($marina);
            
            $this->command->info("Created marina: {$marina->name}");
        }
    }

    private function downloadMarinaImage(Marina $marina): void
    {
        try {
            // Create marinas directory if it doesn't exist
            $directory = 'marinas';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Use placeholder image service with different images
            $imageUrls = [
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=800&h=600&fit=crop',
                'https://images.unsplash.com/photo-1516026672322-bc52d61a55d5?w=800&h=600&fit=crop',
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
            
            $filename = Str::slug($marina->name) . '-' . $marina->id . '.jpg';
            $path = $directory . '/' . $filename;
            
            Storage::disk('public')->put($path, $imageContent);
            $marina->cover_image = $path;
            $marina->save();
        } catch (\Exception $e) {
            \Log::warning("Failed to download image for marina {$marina->name}: " . $e->getMessage());
            // Create placeholder instead
            $this->createPlaceholderImage($marina);
        }
    }

    private function createPlaceholderImage(Marina $marina): void
    {
        // Create a simple placeholder using GD if available
        if (function_exists('imagecreatetruecolor')) {
            $width = 800;
            $height = 600;
            $image = imagecreatetruecolor($width, $height);
            
            // Set background color (harbor blue)
            $bgColor = imagecolorallocate($image, 70, 130, 180);
            imagefill($image, 0, 0, $bgColor);
            
            // Add text
            $textColor = imagecolorallocate($image, 255, 255, 255);
            $fontSize = 5;
            $text = $marina->name;
            $x = ($width - strlen($text) * imagefontwidth($fontSize)) / 2;
            $y = ($height - imagefontheight($fontSize)) / 2;
            imagestring($image, $fontSize, $x, $y, $text, $textColor);
            
            $directory = 'marinas';
            $filename = Str::slug($marina->name) . '-' . $marina->id . '.jpg';
            $path = $directory . '/' . $filename;
            
            $tempFile = tempnam(sys_get_temp_dir(), 'marina_');
            imagejpeg($image, $tempFile, 85);
            imagedestroy($image);
            
            Storage::disk('public')->put($path, file_get_contents($tempFile));
            unlink($tempFile);
            
            $marina->cover_image = $path;
            $marina->save();
        }
    }
}


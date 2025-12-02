<?php

namespace Database\Seeders;

use App\Models\Contractor;
use App\Models\ContractorReview;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContractorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Using updateOrCreate to avoid duplicates
        
        $contractors = [
            [
                'name' => 'Marine Tech Solutions',
                'business_name' => 'Marine Tech Solutions Ltd',
                'category' => 'technical_services',
                'description' => 'Expert marine electronics and navigation systems specialists. Certified Furuno, Simrad, and Garmin dealers.',
                'location' => 'Antibes, France',
                'city' => 'Antibes',
                'country' => 'France',
                'phone' => '+33 4 93 67 89 12',
                'email' => 'info@marinetech.fr',
                'website' => 'www.marinetech.fr',
                'specialties' => ['Furuno certified', 'Simrad expert', 'Garmin authorized', 'GMDSS installations'],
                'languages' => ['French', 'English', 'Italian'],
                'emergency_service' => true,
                'response_time' => 'Usually within 4 hours',
                'service_area' => 'French Riviera, Monaco',
                'price_range' => '€€€',
                'is_verified' => true,
            ],
            [
                'name' => 'South Florida Marine Services',
                'business_name' => 'South Florida Marine Services Inc',
                'category' => 'refit_repair',
                'description' => 'Full-service yacht refit and repair facility. Specializing in paint, carpentry, and mechanical work.',
                'location' => 'Fort Lauderdale, FL',
                'city' => 'Fort Lauderdale',
                'country' => 'United States',
                'phone' => '+1 954-555-0123',
                'email' => 'info@southfloridamarine.com',
                'website' => 'www.southfloridamarine.com',
                'specialties' => ['Paint & coating', 'Carpentry', 'Mechanical', 'Fiberglass repair'],
                'languages' => ['English', 'Spanish'],
                'emergency_service' => true,
                'response_time' => '24/7 emergency service',
                'service_area' => 'South Florida, Bahamas',
                'price_range' => '€€€€',
                'is_verified' => true,
            ],
            [
                'name' => 'Mediterranean Provisions',
                'business_name' => 'Med Provisions Co',
                'category' => 'equipment_supplier',
                'description' => 'Premium yacht provisioning and equipment supplier. Serving the Mediterranean yachting community.',
                'location' => 'Monaco',
                'city' => 'Monaco',
                'country' => 'Monaco',
                'phone' => '+377 93 50 12 34',
                'email' => 'orders@medprovisions.mc',
                'website' => 'www.medprovisions.mc',
                'specialties' => ['Provisioning', 'Safety equipment', 'Uniforms', 'Spare parts'],
                'languages' => ['French', 'English', 'Italian', 'Spanish'],
                'emergency_service' => false,
                'response_time' => 'Next business day',
                'service_area' => 'Mediterranean',
                'price_range' => '€€',
                'is_verified' => true,
            ],
            [
                'name' => 'Yacht Legal Services',
                'business_name' => 'Yacht Legal Services LLP',
                'category' => 'professional_services',
                'description' => 'Maritime law specialists. Yacht registration, compliance, and legal documentation services.',
                'location' => 'London, UK',
                'city' => 'London',
                'country' => 'United Kingdom',
                'phone' => '+44 20 7123 4567',
                'email' => 'info@yachtlegal.co.uk',
                'website' => 'www.yachtlegal.co.uk',
                'specialties' => ['Yacht registration', 'Maritime law', 'Compliance', 'Documentation'],
                'languages' => ['English', 'French', 'Spanish'],
                'emergency_service' => false,
                'response_time' => 'Within 48 hours',
                'service_area' => 'Worldwide',
                'price_range' => '€€€€',
                'is_verified' => true,
            ],
            [
                'name' => 'Crew Training Academy',
                'business_name' => 'Crew Training Academy',
                'category' => 'crew_services',
                'description' => 'Professional yacht crew training and certification. STCW, ENG1, and specialized courses.',
                'location' => 'Southampton, UK',
                'city' => 'Southampton',
                'country' => 'United Kingdom',
                'phone' => '+44 23 8022 1234',
                'email' => 'training@crewacademy.co.uk',
                'website' => 'www.crewacademy.co.uk',
                'specialties' => ['STCW training', 'ENG1 medical', 'Fire fighting', 'Sea survival'],
                'languages' => ['English'],
                'emergency_service' => false,
                'response_time' => 'Next available course',
                'service_area' => 'UK, Europe',
                'price_range' => '€€',
                'is_verified' => true,
            ],
            [
                'name' => 'Caribbean Marine Electronics',
                'business_name' => 'Caribbean Marine Electronics',
                'category' => 'technical_services',
                'description' => 'Marine electronics installation and repair throughout the Caribbean.',
                'location' => 'St. Maarten',
                'city' => 'Philipsburg',
                'country' => 'St. Maarten',
                'phone' => '+1 721 555 7890',
                'email' => 'service@caribmarine.com',
                'website' => 'www.caribmarine.com',
                'specialties' => ['Radar systems', 'GPS/Chartplotters', 'Autopilots', 'Communication systems'],
                'languages' => ['English', 'French', 'Dutch'],
                'emergency_service' => true,
                'response_time' => 'Same day service available',
                'service_area' => 'Caribbean',
                'price_range' => '€€€',
                'is_verified' => true,
            ],
        ];

        $users = User::take(10)->get();

        foreach ($contractors as $index => $contractorData) {
            $contractor = Contractor::updateOrCreate(
                ['slug' => Str::slug($contractorData['name'])],
                [
                'name' => $contractorData['name'],
                'slug' => Str::slug($contractorData['name']),
                'business_name' => $contractorData['business_name'],
                'category' => $contractorData['category'],
                'description' => $contractorData['description'],
                'location' => $contractorData['location'],
                'city' => $contractorData['city'],
                'country' => $contractorData['country'],
                'phone' => $contractorData['phone'],
                'email' => $contractorData['email'],
                'website' => $contractorData['website'],
                'specialties' => $contractorData['specialties'],
                'languages' => $contractorData['languages'],
                'emergency_service' => $contractorData['emergency_service'],
                'response_time' => $contractorData['response_time'],
                'service_area' => $contractorData['service_area'],
                'price_range' => $contractorData['price_range'],
                'logo' => $this->getContractorLogo($contractorData['category'], $index),
                'is_verified' => $contractorData['is_verified'],
                ]
            );

            // Create 5-8 reviews for each contractor (only if contractor is new or has no reviews)
            if ($contractor->reviews()->count() == 0) {
                $reviewCount = rand(5, 8);
                for ($i = 0; $i < $reviewCount; $i++) {
                    $user = $users->random();
                    $overallRating = rand(3, 5);
                    
                    ContractorReview::create([
                    'contractor_id' => $contractor->id,
                    'user_id' => $user->id,
                    'title' => $this->getRandomReviewTitle($contractorData['category']),
                    'review' => $this->getRandomReviewText(),
                    'service_type' => $this->getRandomServiceType($contractorData['category']),
                    'service_cost' => rand(500, 15000),
                    'timeframe' => rand(1, 14) . ' days',
                    'overall_rating' => $overallRating,
                    'quality_rating' => rand($overallRating - 1, 5),
                    'professionalism_rating' => rand($overallRating - 1, 5),
                    'pricing_rating' => rand(3, 5),
                    'timeliness_rating' => rand($overallRating - 1, 5),
                    'would_recommend' => rand(0, 10) > 1,
                    'would_hire_again' => rand(0, 10) > 1,
                    'is_anonymous' => rand(0, 10) > 7,
                    'is_verified' => true,
                    'service_date' => now()->subDays(rand(1, 365)),
                    'yacht_name' => 'M/Y ' . ['Serenity', 'Azure', 'Ocean', 'Sea', 'Blue'][rand(0, 4)],
                    'is_approved' => true,
                    ]);
                }
            }

            // Update contractor rating stats
            $contractor->updateRatingStats();
        }
    }

    private function getRandomReviewTitle($category): string
    {
        $titles = [
            'technical_services' => [
                'Excellent service and expertise',
                'Quick response, great work',
                'Professional and reliable',
                'Top quality installation',
                'Highly recommended',
            ],
            'refit_repair' => [
                'Outstanding refit work',
                'Quality craftsmanship',
                'Completed on time and budget',
                'Professional team',
                'Excellent results',
            ],
            'equipment_supplier' => [
                'Great selection and service',
                'Fast delivery, quality products',
                'Reliable supplier',
                'Good prices and service',
                'Highly recommended',
            ],
            'professional_services' => [
                'Professional and knowledgeable',
                'Excellent legal support',
                'Quick and efficient service',
                'Very helpful and responsive',
                'Top quality service',
            ],
            'crew_services' => [
                'Great training facility',
                'Professional instructors',
                'Comprehensive courses',
                'Well organized',
                'Excellent value',
            ],
        ];

        $categoryTitles = $titles[$category] ?? $titles['technical_services'];
        return $categoryTitles[array_rand($categoryTitles)];
    }

    private function getRandomReviewText(): string
    {
        $reviews = [
            'Excellent service from start to finish. Very professional team with great attention to detail. Would definitely use again.',
            'Quick response time and quality work. The team was knowledgeable and completed the job efficiently.',
            'Professional service with good communication throughout. Fair pricing and excellent results.',
            'Highly recommended. The team was experienced and delivered exactly what was promised.',
            'Great service and reasonable prices. Very satisfied with the work completed.',
            'Professional and reliable. Completed the work on time and to a high standard.',
            'Excellent quality work. The team was responsive and addressed all our concerns.',
            'Very satisfied with the service. Professional team and good value for money.',
        ];

        return $reviews[array_rand($reviews)];
    }

    private function getRandomServiceType($category): string
    {
        $services = [
            'technical_services' => ['Radar installation', 'GPS upgrade', 'Autopilot repair', 'Communication system'],
            'refit_repair' => ['Paint job', 'Carpentry work', 'Mechanical repair', 'Fiberglass repair'],
            'equipment_supplier' => ['Provisioning', 'Safety equipment', 'Spare parts', 'Uniforms'],
            'professional_services' => ['Yacht registration', 'Legal consultation', 'Documentation', 'Compliance'],
            'crew_services' => ['STCW training', 'Medical certification', 'Fire fighting course', 'Sea survival'],
        ];

        $categoryServices = $services[$category] ?? $services['technical_services'];
        return $categoryServices[array_rand($categoryServices)];
    }

    private function getContractorLogo($category, $index): ?string
    {
        // Use placeholder images - these can be replaced with actual logos later
        $logos = [
            'technical_services' => [
                'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=300&fit=crop',
            ],
            'refit_repair' => [
                'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1504307651254-35680f356dfd?w=400&h=300&fit=crop',
            ],
            'equipment_supplier' => [
                'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1586864387967-d02ef85d93e8?w=400&h=300&fit=crop',
            ],
            'professional_services' => [
                'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=400&h=300&fit=crop',
            ],
            'crew_services' => [
                'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=400&h=300&fit=crop',
                'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=300&fit=crop',
            ],
        ];

        $categoryLogos = $logos[$category] ?? $logos['technical_services'];
        return $categoryLogos[$index % count($categoryLogos)] ?? null;
    }
}

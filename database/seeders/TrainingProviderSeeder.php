<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingProvider;
use Illuminate\Support\Str;

class TrainingProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = [
            [
                'name' => 'Maritime Training International',
                'slug' => 'maritime-training-international',
                'description' => 'Leading provider of maritime training with over 25 years of experience. State-of-the-art facilities in Southampton.',
                'company_overview' => 'Maritime Training International has been providing world-class maritime training for over 25 years. We have trained over 50,000 maritime professionals and maintain a 98% first-time pass rate. Our facilities include advanced fire simulation equipment and modern classrooms.',
                'years_in_operation' => 25,
                'accreditations' => ['MCA Approved', 'STCW Certified', 'ISO 9001'],
                'training_facilities' => 'State-of-the-art training center with fire simulation facilities, pool for survival training, and modern classrooms.',
                'website' => 'https://www.maritimetraining.com',
                'email' => 'info@maritimetraining.com',
                'phone' => '+44 23 8023 1234',
                'rating_avg' => 4.8,
                'total_reviews' => 156,
                'pass_rate' => 98.5,
                'total_students_trained' => 50000,
                'total_students_ywc' => 1200,
                'response_time_hours' => 4,
                'is_verified_partner' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Blue Water Training',
                'slug' => 'blue-water-training',
                'description' => 'Specialized yacht crew training provider with facilities in Fort Lauderdale and Antibes.',
                'company_overview' => 'Blue Water Training specializes in yacht crew certifications. We offer courses in prime yachting locations including Fort Lauderdale, Antibes, and Palma. Our instructors are experienced yacht professionals.',
                'years_in_operation' => 15,
                'accreditations' => ['MCA Approved', 'STCW Certified'],
                'training_facilities' => 'Training centers in Fort Lauderdale and Antibes with yacht-specific training equipment.',
                'website' => 'https://www.bluewatertraining.com',
                'email' => 'info@bluewatertraining.com',
                'phone' => '+1 954 123 4567',
                'rating_avg' => 4.6,
                'total_reviews' => 89,
                'pass_rate' => 96.0,
                'total_students_trained' => 15000,
                'total_students_ywc' => 450,
                'response_time_hours' => 6,
                'is_verified_partner' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Maritime Skills Academy',
                'slug' => 'maritime-skills-academy',
                'description' => 'Comprehensive maritime training provider with multiple locations across the UK.',
                'company_overview' => 'Maritime Skills Academy provides comprehensive maritime training across the UK. We offer flexible scheduling and group booking options for yacht crews.',
                'years_in_operation' => 12,
                'accreditations' => ['MCA Approved', 'STCW Certified'],
                'training_facilities' => 'Multiple training centers across the UK with modern facilities.',
                'website' => 'https://www.maritimeskills.com',
                'email' => 'info@maritimeskills.com',
                'phone' => '+44 20 1234 5678',
                'rating_avg' => 4.7,
                'total_reviews' => 203,
                'pass_rate' => 97.2,
                'total_students_trained' => 25000,
                'total_students_ywc' => 800,
                'response_time_hours' => 5,
                'is_verified_partner' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Yacht Crew Training',
                'slug' => 'yacht-crew-training',
                'description' => 'Specialized training provider focused exclusively on yacht crew certifications.',
                'company_overview' => 'Yacht Crew Training focuses exclusively on yacht crew needs. We understand the unique requirements of the superyacht industry and tailor our courses accordingly.',
                'years_in_operation' => 8,
                'accreditations' => ['STCW Certified'],
                'training_facilities' => 'Training facilities in Monaco and Antibes with yacht-specific focus.',
                'website' => 'https://www.yachtcrewtraining.com',
                'email' => 'info@yachtcrewtraining.com',
                'phone' => '+33 4 93 12 34 56',
                'rating_avg' => 4.5,
                'total_reviews' => 67,
                'pass_rate' => 95.5,
                'total_students_trained' => 8000,
                'total_students_ywc' => 320,
                'response_time_hours' => 8,
                'is_verified_partner' => false,
                'is_active' => true,
            ],
            [
                'name' => 'Maritime E-Learning',
                'slug' => 'maritime-e-learning',
                'description' => 'Online maritime training provider offering flexible learning options.',
                'company_overview' => 'Maritime E-Learning provides online and hybrid maritime training courses. Perfect for crew members who need flexible scheduling.',
                'years_in_operation' => 5,
                'accreditations' => ['STCW Certified', 'Online Accredited'],
                'training_facilities' => 'Online platform with virtual classrooms and practical assessment centers.',
                'website' => 'https://www.maritimeelearning.com',
                'email' => 'info@maritimeelearning.com',
                'phone' => '+44 800 123 4567',
                'rating_avg' => 4.3,
                'total_reviews' => 124,
                'pass_rate' => 94.0,
                'total_students_trained' => 12000,
                'total_students_ywc' => 600,
                'response_time_hours' => 12,
                'is_verified_partner' => false,
                'is_active' => true,
            ],
        ];

        foreach ($providers as $provider) {
            TrainingProvider::firstOrCreate(
                ['slug' => $provider['slug']],
                $provider
            );
        }
    }
}

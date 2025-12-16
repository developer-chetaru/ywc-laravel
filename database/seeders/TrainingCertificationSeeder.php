<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TrainingCertification;
use App\Models\TrainingCertificationCategory;

class TrainingCertificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $safetyCategory = TrainingCertificationCategory::where('slug', 'safety')->first();
        $medicalCategory = TrainingCertificationCategory::where('slug', 'medical')->first();
        $securityCategory = TrainingCertificationCategory::where('slug', 'security')->first();
        $engineeringCategory = TrainingCertificationCategory::where('slug', 'engineering')->first();
        $navigationCategory = TrainingCertificationCategory::where('slug', 'navigation')->first();
        $leadershipCategory = TrainingCertificationCategory::where('slug', 'leadership')->first();
        $specializedCategory = TrainingCertificationCategory::where('slug', 'specialized')->first();

        // Image mapping for certifications
        $certificationImages = [
            'stcw-basic-safety-training' => 'images/image1.jpg',
            'advanced-fire-fighting' => 'images/image2.jpg',
            'proficiency-survival-craft-rescue-boats' => 'images/image3.jpg',
            'eng1-medical-certificate' => 'images/image4.jpg',
            'medical-care-on-board-ship' => 'images/tripe-img1.jpg',
            'elementary-first-aid' => 'images/tripe-img2.jpg',
            'ship-security-officer-sso' => 'images/tripe-img3.jpg',
            'security-awareness' => 'images/tripe-img4.jpg',
            'marine-engineering-operations' => 'images/tripe-img5.jpg',
            'colregs-collision-avoidance' => 'images/tripe-img6.jpg',
            'yachtmaster-offshore' => 'images/tripe-img7.jpg',
            'leadership-teamwork' => 'images/tripe-img8.jpg',
            'food-safety-hygiene' => 'images/image1.jpg',
            'wine-service-sommelier' => 'images/image2.jpg',
        ];

        $certifications = [
            // Safety Certifications
            [
                'category_id' => $safetyCategory->id,
                'name' => 'STCW Basic Safety Training',
                'slug' => 'stcw-basic-safety-training',
                'official_designation' => 'STCW A-VI/1-1, A-VI/1-2, A-VI/1-3, A-VI/1-4',
                'description' => 'Comprehensive basic safety training covering Personal Survival Techniques, Fire Prevention and Fire Fighting, Elementary First Aid, and Personal Safety and Social Responsibilities. Required for all seafarers working on commercial vessels.',
                'prerequisites' => 'None - Entry level certification',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher course or full STCW Basic Safety Training course',
                'international_recognition' => 'Recognized by all IMO member states and flag administrations worldwide',
                'career_benefits' => 'Essential for entry-level positions. Required for all yacht crew positions. Opens doors to maritime employment.',
                'positions_requiring' => 'All entry-level deck and interior positions, Junior Deckhand, Steward/ess, Chef, Engineer',
                'cover_image' => $certificationImages['stcw-basic-safety-training'],
                'is_active' => true,
            ],
            [
                'category_id' => $safetyCategory->id,
                'name' => 'Advanced Fire Fighting',
                'slug' => 'advanced-fire-fighting',
                'official_designation' => 'STCW A-VI/3',
                'description' => 'Advanced fire fighting techniques for personnel designated to control fire-fighting operations. Covers fire theory, fire-fighting equipment, and advanced fire-fighting procedures.',
                'prerequisites' => 'STCW Basic Safety Training',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher course within validity period',
                'international_recognition' => 'IMO STCW recognized worldwide',
                'career_benefits' => 'Required for senior deck positions, Bosun, Lead Deckhand, Officer positions',
                'positions_requiring' => 'Bosun, Lead Deckhand, Officer positions, Senior crew members',
                'cover_image' => $certificationImages['advanced-fire-fighting'],
                'is_active' => true,
            ],
            [
                'category_id' => $safetyCategory->id,
                'name' => 'Proficiency in Survival Craft and Rescue Boats',
                'slug' => 'proficiency-survival-craft-rescue-boats',
                'official_designation' => 'STCW A-VI/2-1',
                'description' => 'Training in the operation of survival craft and rescue boats, including launching, recovery, and operation in various sea conditions.',
                'prerequisites' => 'STCW Basic Safety Training',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher course',
                'international_recognition' => 'IMO STCW recognized',
                'career_benefits' => 'Required for designated crew members responsible for survival craft operations',
                'positions_requiring' => 'Deck officers, Designated crew members',
                'cover_image' => $certificationImages['proficiency-survival-craft-rescue-boats'],
                'is_active' => true,
            ],

            // Medical Certifications
            [
                'category_id' => $medicalCategory->id,
                'name' => 'ENG1 Medical Certificate',
                'slug' => 'eng1-medical-certificate',
                'official_designation' => 'ENG1',
                'description' => 'Seafarer medical fitness certificate required for all seafarers working on commercial vessels. Validates physical and mental fitness for sea service.',
                'prerequisites' => 'None - Medical examination required',
                'validity_period_months' => 24,
                'renewal_requirements' => 'Complete medical examination with approved doctor',
                'international_recognition' => 'Recognized by MCA and most flag states',
                'career_benefits' => 'Mandatory for all yacht crew. Required before starting any position.',
                'positions_requiring' => 'All yacht crew positions',
                'cover_image' => $certificationImages['eng1-medical-certificate'],
                'is_active' => true,
            ],
            [
                'category_id' => $medicalCategory->id,
                'name' => 'Medical Care On Board Ship',
                'slug' => 'medical-care-on-board-ship',
                'official_designation' => 'STCW A-VI/4-2',
                'description' => 'Advanced medical training for personnel designated to provide medical care on board ships. Covers medical emergencies, treatment procedures, and medical equipment.',
                'prerequisites' => 'STCW Elementary First Aid, ENG1',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher course',
                'international_recognition' => 'IMO STCW recognized',
                'career_benefits' => 'Required for senior positions, Chief Steward/ess, Purser, Medical Officer',
                'positions_requiring' => 'Chief Steward/ess, Purser, Medical Officer, Senior crew',
                'cover_image' => $certificationImages['medical-care-on-board-ship'],
                'is_active' => true,
            ],
            [
                'category_id' => $medicalCategory->id,
                'name' => 'Elementary First Aid',
                'slug' => 'elementary-first-aid',
                'official_designation' => 'STCW A-VI/1-3',
                'description' => 'Basic first aid training covering immediate response to medical emergencies, CPR, and basic life support techniques.',
                'prerequisites' => 'None - Part of STCW Basic Safety',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher or full STCW Basic Safety',
                'international_recognition' => 'IMO STCW recognized',
                'career_benefits' => 'Essential safety skill for all crew members',
                'positions_requiring' => 'All crew positions',
                'cover_image' => $certificationImages['elementary-first-aid'],
                'is_active' => true,
            ],

            // Security Certifications
            [
                'category_id' => $securityCategory->id,
                'name' => 'Ship Security Officer (SSO)',
                'slug' => 'ship-security-officer-sso',
                'official_designation' => 'STCW A-VI/5',
                'description' => 'Comprehensive security training for personnel designated to perform security duties on board ships. Covers security planning, threat assessment, and security procedures.',
                'prerequisites' => 'STCW Basic Safety Training, Security Awareness',
                'validity_period_months' => 60,
                'renewal_requirements' => 'Complete refresher course',
                'international_recognition' => 'IMO STCW recognized worldwide',
                'career_benefits' => 'Required for security officer positions. Essential for vessels operating in high-risk areas.',
                'positions_requiring' => 'Security Officer, Chief Officer, Captain (in some cases)',
                'cover_image' => $certificationImages['ship-security-officer-sso'],
                'is_active' => true,
            ],
            [
                'category_id' => $securityCategory->id,
                'name' => 'Security Awareness',
                'slug' => 'security-awareness',
                'official_designation' => 'STCW A-VI/6-1',
                'description' => 'Basic security awareness training for all seafarers. Covers security threats, security procedures, and reporting requirements.',
                'prerequisites' => 'None',
                'validity_period_months' => null,
                'renewal_requirements' => 'Not required - one-time certification',
                'international_recognition' => 'IMO STCW recognized',
                'career_benefits' => 'Required for all crew members. Essential for vessels operating in high-risk areas.',
                'positions_requiring' => 'All crew positions',
                'cover_image' => $certificationImages['security-awareness'],
                'is_active' => true,
            ],

            // Engineering Certifications
            [
                'category_id' => $engineeringCategory->id,
                'name' => 'Marine Engineering Operations',
                'slug' => 'marine-engineering-operations',
                'description' => 'Comprehensive training in marine engineering systems, maintenance, and operations for yacht engineers.',
                'prerequisites' => 'Basic engineering knowledge, STCW Basic Safety',
                'validity_period_months' => null,
                'renewal_requirements' => 'Continuous professional development recommended',
                'international_recognition' => 'Industry recognized',
                'career_benefits' => 'Essential for engineering positions. Advances career in technical roles.',
                'positions_requiring' => 'Engineer, Second Engineer, Chief Engineer',
                'cover_image' => $certificationImages['marine-engineering-operations'],
                'is_active' => true,
            ],

            // Navigation Certifications
            [
                'category_id' => $navigationCategory->id,
                'name' => 'COLREGs (Collision Avoidance)',
                'slug' => 'colregs-collision-avoidance',
                'description' => 'International Regulations for Preventing Collisions at Sea. Essential knowledge for all deck officers and watchkeepers.',
                'prerequisites' => 'None',
                'validity_period_months' => null,
                'renewal_requirements' => 'Not required - knowledge-based certification',
                'international_recognition' => 'IMO recognized worldwide',
                'career_benefits' => 'Essential for deck officers. Required for navigation and watchkeeping positions.',
                'positions_requiring' => 'Deck Officers, Watchkeepers, Navigation Officers',
                'cover_image' => $certificationImages['colregs-collision-avoidance'],
                'is_active' => true,
            ],
            [
                'category_id' => $navigationCategory->id,
                'name' => 'Yachtmaster Offshore',
                'slug' => 'yachtmaster-offshore',
                'description' => 'Professional qualification for skippers of yachts up to 200GT. Covers navigation, seamanship, and vessel management.',
                'prerequisites' => 'Sea time requirements, navigation experience',
                'validity_period_months' => null,
                'renewal_requirements' => 'Not required - lifetime qualification',
                'international_recognition' => 'RYA recognized, internationally respected',
                'career_benefits' => 'Highly regarded qualification. Opens doors to senior deck positions and captain roles.',
                'positions_requiring' => 'Captain, First Officer, Senior Deck Officer',
                'cover_image' => $certificationImages['yachtmaster-offshore'],
                'is_active' => true,
            ],

            // Leadership Certifications
            [
                'category_id' => $leadershipCategory->id,
                'name' => 'Leadership & Teamwork',
                'slug' => 'leadership-teamwork',
                'description' => 'Training in leadership skills, team management, conflict resolution, and effective communication for senior crew members.',
                'prerequisites' => 'Experience in senior crew position recommended',
                'validity_period_months' => null,
                'renewal_requirements' => 'Not required',
                'international_recognition' => 'Industry recognized',
                'career_benefits' => 'Essential for senior positions. Develops management and leadership skills.',
                'positions_requiring' => 'Chief Steward/ess, Bosun, Chief Engineer, Senior Officers',
                'cover_image' => $certificationImages['leadership-teamwork'],
                'is_active' => true,
            ],

            // Specialized Certifications
            [
                'category_id' => $specializedCategory->id,
                'name' => 'Food Safety & Hygiene',
                'slug' => 'food-safety-hygiene',
                'description' => 'Food safety and hygiene certification for galley staff. Covers HACCP principles, food handling, and kitchen safety.',
                'prerequisites' => 'None',
                'validity_period_months' => 36,
                'renewal_requirements' => 'Complete refresher course',
                'international_recognition' => 'Industry recognized',
                'career_benefits' => 'Required for galley positions. Essential for chefs and galley staff.',
                'positions_requiring' => 'Chef, Sous Chef, Galley Staff',
                'cover_image' => $certificationImages['food-safety-hygiene'],
                'is_active' => true,
            ],
            [
                'category_id' => $specializedCategory->id,
                'name' => 'Wine Service & Sommelier',
                'slug' => 'wine-service-sommelier',
                'description' => 'Professional wine service training covering wine knowledge, service techniques, and pairing recommendations for luxury yacht service.',
                'prerequisites' => 'None',
                'validity_period_months' => null,
                'renewal_requirements' => 'Not required',
                'international_recognition' => 'Industry recognized',
                'career_benefits' => 'Enhances service skills. Valued in luxury yacht interior positions.',
                'positions_requiring' => 'Chief Steward/ess, Steward/ess, Sommelier',
                'cover_image' => $certificationImages['wine-service-sommelier'],
                'is_active' => true,
            ],
        ];

        foreach ($certifications as $cert) {
            TrainingCertification::firstOrCreate(
                ['slug' => $cert['slug']],
                $cert
            );
        }
    }
}

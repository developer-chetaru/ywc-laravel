<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterData;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Route Visibility Options
        $routeVisibility = [
            ['code' => 'private', 'name' => 'Private - Only you', 'description' => 'Only visible to you', 'sort_order' => 1],
            ['code' => 'crew', 'name' => 'Crew - Shared with crew members', 'description' => 'Visible to crew members', 'sort_order' => 2],
            ['code' => 'public', 'name' => 'Public - Visible to everyone', 'description' => 'Visible to everyone', 'sort_order' => 3],
        ];

        foreach ($routeVisibility as $item) {
            MasterData::firstOrCreate(
                ['type' => 'route_visibility', 'code' => $item['code']],
                array_merge($item, ['type' => 'route_visibility'])
            );
        }

        // Route Status Options
        $routeStatus = [
            ['code' => 'draft', 'name' => 'Draft - Work in progress', 'description' => 'Work in progress', 'sort_order' => 1],
            ['code' => 'active', 'name' => 'Active - Currently sailing', 'description' => 'Currently sailing', 'sort_order' => 2],
            ['code' => 'completed', 'name' => 'Completed - Journey finished', 'description' => 'Journey finished', 'sort_order' => 3],
        ];

        foreach ($routeStatus as $item) {
            MasterData::firstOrCreate(
                ['type' => 'route_status', 'code' => $item['code']],
                array_merge($item, ['type' => 'route_status'])
            );
        }

        // Marina Types
        $marinaTypes = [
            ['code' => 'full_service', 'name' => 'Full Service', 'description' => 'Full service marina with all amenities', 'sort_order' => 1],
            ['code' => 'municipal_port', 'name' => 'Municipal Port', 'description' => 'Municipal or public port', 'sort_order' => 2],
            ['code' => 'yacht_club', 'name' => 'Yacht Club', 'description' => 'Private yacht club', 'sort_order' => 3],
            ['code' => 'anchorage', 'name' => 'Anchorage', 'description' => 'Anchorage area', 'sort_order' => 4],
            ['code' => 'mooring_field', 'name' => 'Mooring Field', 'description' => 'Mooring field', 'sort_order' => 5],
            ['code' => 'dry_stack', 'name' => 'Dry Stack', 'description' => 'Dry stack storage', 'sort_order' => 6],
            ['code' => 'boatyard', 'name' => 'Boatyard', 'description' => 'Boatyard facility', 'sort_order' => 7],
        ];

        foreach ($marinaTypes as $item) {
            MasterData::firstOrCreate(
                ['type' => 'marina_type', 'code' => $item['code']],
                array_merge($item, ['type' => 'marina_type'])
            );
        }

        // Yacht Types
        $yachtTypes = [
            ['code' => 'motor_yacht', 'name' => 'Motor Yacht', 'description' => 'Motor-powered yacht', 'sort_order' => 1],
            ['code' => 'sailing_yacht', 'name' => 'Sailing Yacht', 'description' => 'Sail-powered yacht', 'sort_order' => 2],
            ['code' => 'explorer', 'name' => 'Explorer', 'description' => 'Explorer yacht', 'sort_order' => 3],
            ['code' => 'catamaran', 'name' => 'Catamaran', 'description' => 'Catamaran yacht', 'sort_order' => 4],
            ['code' => 'other', 'name' => 'Other', 'description' => 'Other yacht type', 'sort_order' => 5],
        ];

        foreach ($yachtTypes as $item) {
            MasterData::firstOrCreate(
                ['type' => 'yacht_type', 'code' => $item['code']],
                array_merge($item, ['type' => 'yacht_type'])
            );
        }

        // Countries - Major countries with ISO codes
        $countries = [
            ['code' => 'US', 'name' => 'United States', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'GB', 'name' => 'United Kingdom', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+44']],
            ['code' => 'FR', 'name' => 'France', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+33']],
            ['code' => 'IT', 'name' => 'Italy', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+39']],
            ['code' => 'ES', 'name' => 'Spain', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+34']],
            ['code' => 'GR', 'name' => 'Greece', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+30']],
            ['code' => 'TR', 'name' => 'Turkey', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+90']],
            ['code' => 'AU', 'name' => 'Australia', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+61']],
            ['code' => 'NZ', 'name' => 'New Zealand', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+64']],
            ['code' => 'CA', 'name' => 'Canada', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'MX', 'name' => 'Mexico', 'metadata' => ['continent' => 'North America', 'phone_code' => '+52']],
            ['code' => 'BR', 'name' => 'Brazil', 'metadata' => ['continent' => 'South America', 'phone_code' => '+55']],
            ['code' => 'AR', 'name' => 'Argentina', 'metadata' => ['continent' => 'South America', 'phone_code' => '+54']],
            ['code' => 'DE', 'name' => 'Germany', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+49']],
            ['code' => 'NL', 'name' => 'Netherlands', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+31']],
            ['code' => 'BE', 'name' => 'Belgium', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+32']],
            ['code' => 'CH', 'name' => 'Switzerland', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+41']],
            ['code' => 'AT', 'name' => 'Austria', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+43']],
            ['code' => 'PT', 'name' => 'Portugal', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+351']],
            ['code' => 'HR', 'name' => 'Croatia', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+385']],
            ['code' => 'MT', 'name' => 'Malta', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+356']],
            ['code' => 'CY', 'name' => 'Cyprus', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+357']],
            ['code' => 'MC', 'name' => 'Monaco', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+377']],
            ['code' => 'SG', 'name' => 'Singapore', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+65']],
            ['code' => 'MY', 'name' => 'Malaysia', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+60']],
            ['code' => 'TH', 'name' => 'Thailand', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+66']],
            ['code' => 'ID', 'name' => 'Indonesia', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+62']],
            ['code' => 'PH', 'name' => 'Philippines', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+63']],
            ['code' => 'CN', 'name' => 'China', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+86']],
            ['code' => 'JP', 'name' => 'Japan', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+81']],
            ['code' => 'KR', 'name' => 'South Korea', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+82']],
            ['code' => 'IN', 'name' => 'India', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+91']],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+971']],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+966']],
            ['code' => 'ZA', 'name' => 'South Africa', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+27']],
            ['code' => 'EG', 'name' => 'Egypt', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+20']],
            ['code' => 'MA', 'name' => 'Morocco', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+212']],
            ['code' => 'RU', 'name' => 'Russia', 'metadata' => ['continent' => 'Europe/Asia', 'phone_code' => '+7']],
            ['code' => 'SE', 'name' => 'Sweden', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+46']],
            ['code' => 'NO', 'name' => 'Norway', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+47']],
            ['code' => 'DK', 'name' => 'Denmark', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+45']],
            ['code' => 'FI', 'name' => 'Finland', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+358']],
            ['code' => 'IE', 'name' => 'Ireland', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+353']],
            ['code' => 'IS', 'name' => 'Iceland', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+354']],
            ['code' => 'PL', 'name' => 'Poland', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+48']],
            ['code' => 'CZ', 'name' => 'Czech Republic', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+420']],
            ['code' => 'HU', 'name' => 'Hungary', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+36']],
            ['code' => 'RO', 'name' => 'Romania', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+40']],
            ['code' => 'BG', 'name' => 'Bulgaria', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+359']],
            ['code' => 'RS', 'name' => 'Serbia', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+381']],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+387']],
            ['code' => 'ME', 'name' => 'Montenegro', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+382']],
            ['code' => 'AL', 'name' => 'Albania', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+355']],
            ['code' => 'SI', 'name' => 'Slovenia', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+386']],
            ['code' => 'SK', 'name' => 'Slovakia', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+421']],
            ['code' => 'LU', 'name' => 'Luxembourg', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+352']],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+423']],
            ['code' => 'AD', 'name' => 'Andorra', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+376']],
            ['code' => 'SM', 'name' => 'San Marino', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+378']],
            ['code' => 'VA', 'name' => 'Vatican City', 'metadata' => ['continent' => 'Europe', 'phone_code' => '+39']],
            ['code' => 'BS', 'name' => 'Bahamas', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'BB', 'name' => 'Barbados', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'JM', 'name' => 'Jamaica', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'KY', 'name' => 'Cayman Islands', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'VG', 'name' => 'British Virgin Islands', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'TC', 'name' => 'Turks and Caicos Islands', 'metadata' => ['continent' => 'North America', 'phone_code' => '+1']],
            ['code' => 'BZ', 'name' => 'Belize', 'metadata' => ['continent' => 'North America', 'phone_code' => '+501']],
            ['code' => 'CR', 'name' => 'Costa Rica', 'metadata' => ['continent' => 'North America', 'phone_code' => '+506']],
            ['code' => 'PA', 'name' => 'Panama', 'metadata' => ['continent' => 'North America', 'phone_code' => '+507']],
            ['code' => 'CO', 'name' => 'Colombia', 'metadata' => ['continent' => 'South America', 'phone_code' => '+57']],
            ['code' => 'VE', 'name' => 'Venezuela', 'metadata' => ['continent' => 'South America', 'phone_code' => '+58']],
            ['code' => 'PE', 'name' => 'Peru', 'metadata' => ['continent' => 'South America', 'phone_code' => '+51']],
            ['code' => 'CL', 'name' => 'Chile', 'metadata' => ['continent' => 'South America', 'phone_code' => '+56']],
            ['code' => 'UY', 'name' => 'Uruguay', 'metadata' => ['continent' => 'South America', 'phone_code' => '+598']],
            ['code' => 'FJ', 'name' => 'Fiji', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+679']],
            ['code' => 'PG', 'name' => 'Papua New Guinea', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+675']],
            ['code' => 'NC', 'name' => 'New Caledonia', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+687']],
            ['code' => 'PF', 'name' => 'French Polynesia', 'metadata' => ['continent' => 'Oceania', 'phone_code' => '+689']],
            ['code' => 'BN', 'name' => 'Brunei', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+673']],
            ['code' => 'VN', 'name' => 'Vietnam', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+84']],
            ['code' => 'KH', 'name' => 'Cambodia', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+855']],
            ['code' => 'LA', 'name' => 'Laos', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+856']],
            ['code' => 'MM', 'name' => 'Myanmar', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+95']],
            ['code' => 'BD', 'name' => 'Bangladesh', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+880']],
            ['code' => 'PK', 'name' => 'Pakistan', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+92']],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+94']],
            ['code' => 'MV', 'name' => 'Maldives', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+960']],
            ['code' => 'OM', 'name' => 'Oman', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+968']],
            ['code' => 'KW', 'name' => 'Kuwait', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+965']],
            ['code' => 'QA', 'name' => 'Qatar', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+974']],
            ['code' => 'BH', 'name' => 'Bahrain', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+973']],
            ['code' => 'JO', 'name' => 'Jordan', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+962']],
            ['code' => 'LB', 'name' => 'Lebanon', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+961']],
            ['code' => 'IL', 'name' => 'Israel', 'metadata' => ['continent' => 'Asia', 'phone_code' => '+972']],
            ['code' => 'KE', 'name' => 'Kenya', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+254']],
            ['code' => 'TZ', 'name' => 'Tanzania', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+255']],
            ['code' => 'MU', 'name' => 'Mauritius', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+230']],
            ['code' => 'SC', 'name' => 'Seychelles', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+248']],
            ['code' => 'MG', 'name' => 'Madagascar', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+261']],
            ['code' => 'MZ', 'name' => 'Mozambique', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+258']],
            ['code' => 'TN', 'name' => 'Tunisia', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+216']],
            ['code' => 'DZ', 'name' => 'Algeria', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+213']],
            ['code' => 'LY', 'name' => 'Libya', 'metadata' => ['continent' => 'Africa', 'phone_code' => '+218']],
        ];

        foreach ($countries as $index => $country) {
            MasterData::firstOrCreate(
                ['type' => 'country', 'code' => $country['code']],
                [
                    'type' => 'country',
                    'code' => $country['code'],
                    'name' => $country['name'],
                    'metadata' => $country['metadata'],
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}

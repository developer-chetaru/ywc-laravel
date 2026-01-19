#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\EmployerCrew;
use App\Models\AgencyCandidate;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Hash;

echo "📊 Adding sample data...\n\n";

// Get employer and agency users
$employer = User::where('email', 'employer@test.com')->first();
$agency = User::where('email', 'agency@test.com')->first();

if (!$employer || !$agency) {
    echo "❌ Test users not found. Run setup_test_users.php first!\n";
    exit(1);
}

// Create 3 crew members
echo "1. Creating sample crew members...\n";
for ($i = 1; $i <= 3; $i++) {
    $crew = User::firstOrCreate(
        ['email' => "crew{$i}@test.com"],
        [
            'first_name' => "Crew",
            'last_name' => "Member {$i}",
            'password' => Hash::make('password'),
            'is_active' => true,
        ]
    );

    // Add to employer
    EmployerCrew::updateOrCreate(
        [
            'employer_id' => $employer->id,
            'crew_id' => $crew->id,
        ],
        [
            'position' => ['Captain', 'Chief Engineer', 'Deckhand'][$i-1],
            'vessel_name' => 'Luxury Yacht ' . $i,
            'vessel_imo' => 'IMO' . (1000000 + $i),
            'status' => 'active',
            'contract_start_date' => now()->subMonths($i),
            'contract_end_date' => now()->addMonths(6 + $i),
            'added_by' => $employer->id,
        ]
    );

    // Add to agency candidates
    AgencyCandidate::updateOrCreate(
        [
            'agency_id' => $agency->id,
            'candidate_id' => $crew->id,
        ],
        [
            'desired_position' => ['Captain', 'Engineer', 'Deckhand'][$i-1],
            'desired_vessel_type' => 'Motor Yacht',
            'desired_salary_min' => 5000 + ($i * 1000),
            'desired_salary_max' => 8000 + ($i * 1000),
            'status' => 'active',
            'available_from' => now(),
            'tags' => ['STCW', 'ENG1', 'Experience'],
            'priority' => 10 - $i,
            'added_by' => $agency->id,
        ]
    );

    echo "   ✓ Created crew{$i}@test.com\n";
}

// Create job postings
echo "\n2. Creating sample job postings...\n";
$jobs = [
    [
        'title' => 'Experienced Captain Needed',
        'description' => 'Looking for an experienced captain for 50m motor yacht.',
        'position' => 'Captain',
        'vessel_name' => 'Motor Yacht Excellence',
        'vessel_type' => 'Motor Yacht',
        'salary_min' => 7000,
        'salary_max' => 10000,
    ],
    [
        'title' => 'Chief Engineer Position',
        'description' => 'Seeking qualified chief engineer for luxury yacht.',
        'position' => 'Chief Engineer',
        'vessel_name' => 'Sailing Yacht Serenity',
        'vessel_type' => 'Sailing Yacht',
        'salary_min' => 6000,
        'salary_max' => 9000,
    ],
];

foreach ($jobs as $jobData) {
    JobPosting::create(array_merge($jobData, [
        'posted_by' => $agency->id,
        'location' => 'Mediterranean',
        'contract_duration' => '6-12 months',
        'status' => 'open',
        'start_date' => now()->addMonth(),
        'application_deadline' => now()->addMonths(2),
    ]));
    echo "   ✓ Created job: {$jobData['title']}\n";
}

echo "\n✅ Sample data added successfully!\n\n";
echo "🎯 Now test:\n";
echo "   • /employer/dashboard - See 3 crew members\n";
echo "   • /employer/compliance-report - See compliance data\n";
echo "   • /agency/dashboard - See 3 candidates\n";
echo "   • /agency/jobs - See 2 job postings\n";
echo "   • /agency/jobs/1/matches - See job matching\n\n";

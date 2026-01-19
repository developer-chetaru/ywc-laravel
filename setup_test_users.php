#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "🚀 Setting up test users for Phase 2 features...\n\n";

// Create or update Employer user
echo "1. Creating Employer user...\n";
$employer = User::updateOrCreate(
    ['email' => 'employer@test.com'],
    [
        'first_name' => 'Test',
        'last_name' => 'Employer',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]
);

// Remove all roles first to avoid duplicates
$employer->syncRoles([]);
$employer->assignRole('employer');
echo "   ✓ Employer: employer@test.com / password\n";
echo "   ✓ Dashboard: /employer/dashboard\n\n";

// Create or update Agency user
echo "2. Creating Recruitment Agency user...\n";
$agency = User::updateOrCreate(
    ['email' => 'agency@test.com'],
    [
        'first_name' => 'Test',
        'last_name' => 'Agency',
        'password' => Hash::make('password'),
        'is_active' => true,
    ]
);

$agency->syncRoles([]);
$agency->assignRole('recruitment_agency');
echo "   ✓ Agency: agency@test.com / password\n";
echo "   ✓ Dashboard: /agency/dashboard\n\n";

// Update your current user with employer role (optional)
$currentUser = User::where('email', 'like', '%@%')->first();
if ($currentUser && $currentUser->email !== 'employer@test.com' && $currentUser->email !== 'agency@test.com') {
    echo "3. Adding employer role to: {$currentUser->email}\n";
    if (!$currentUser->hasRole('employer')) {
        $currentUser->assignRole('employer');
        echo "   ✓ Employer role added!\n";
        echo "   ✓ You can now access: /employer/dashboard\n\n";
    } else {
        echo "   ✓ Already has employer role\n\n";
    }
}

echo "✅ Setup complete!\n\n";
echo "📝 Login Credentials:\n";
echo "   • Employer: employer@test.com / password\n";
echo "   • Agency:   agency@test.com / password\n\n";
echo "🎯 Test URLs:\n";
echo "   • /employer/dashboard - Employer Dashboard\n";
echo "   • /agency/dashboard - Agency Dashboard\n";
echo "   • /analytics/user-dashboard - User Analytics\n";
echo "   • /career-history - Career History (OCR editing)\n\n";

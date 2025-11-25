<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkLog;
use App\Models\WorkLogRestPeriod;
use App\Models\User;
use App\Models\Yacht;
use Carbon\Carbon;

class WorkLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->warn('No users found. Please run UserSeeder first.');
            return;
        }

        $yachts = Yacht::all();
        $hasYachts = $yachts->isNotEmpty();
        $locationStatuses = ['at_sea', 'in_port', 'in_yard', 'on_leave', 'shore_leave'];
        $positions = ['Captain', 'First Officer', 'Chief Engineer', 'Deckhand', 'Steward/ess', 'Chef', 'Engineer'];
        $departments = ['Deck', 'Engineering', 'Interior', 'Galley'];
        $weatherConditions = ['Clear', 'Partly Cloudy', 'Cloudy', 'Rainy', 'Stormy', 'Foggy'];
        $seaStates = ['Calm', 'Slight', 'Moderate', 'Rough', 'Very Rough'];
        $visibilities = ['Excellent', 'Good', 'Moderate', 'Poor', 'Very Poor'];
        
        $activitiesList = [
            'Navigation watch',
            'Engine maintenance',
            'Deck cleaning',
            'Guest service',
            'Meal preparation',
            'Safety drills',
            'Anchoring operations',
            'Tender operations',
            'Provisioning',
            'Laundry service',
            'Cabin cleaning',
            'Bridge watch',
            'Engine room watch',
            'Galley service',
            'Security watch'
        ];

        $this->command->info('Creating work log data for ' . $users->count() . ' users...');

        foreach ($users as $user) {
            // Get a random yacht for this user (or null)
            $yacht = $hasYachts ? $yachts->random() : null;
            
            // Create 8 days of work log data (going back 8 days from today)
            for ($i = 7; $i >= 0; $i--) {
                $workDate = Carbon::today()->subDays($i);
                
                // Skip if work log already exists for this date
                if (WorkLog::where('user_id', $user->id)
                    ->where('work_date', $workDate->format('Y-m-d'))
                    ->exists()) {
                    continue;
                }

                // Randomly decide if it's a day off (10% chance)
                $isDayOff = rand(1, 10) === 1;
                
                if ($isDayOff) {
                    // Create a day off entry
                    $workLog = WorkLog::create([
                        'user_id' => $user->id,
                        'work_date' => $workDate,
                        'is_day_off' => true,
                        'location_status' => 'on_leave',
                        'total_hours_worked' => 0,
                        'total_rest_hours' => 24,
                        'is_compliant' => true,
                        'compliance_status' => 'compliant',
                        'counts_towards_sea_service' => false,
                        'is_at_sea' => false,
                    ]);
                    continue;
                }

                // Generate realistic work hours (8-12 hours)
                $totalHoursWorked = rand(800, 1200) / 100; // 8.00 to 12.00 hours
                $overtimeHours = $totalHoursWorked > 10 ? ($totalHoursWorked - 10) : 0;
                
                // Generate break minutes (30-90 minutes)
                $breakMinutes = rand(30, 90);
                
                // Generate rest hours (should be at least 10 hours for compliance)
                $totalRestHours = rand(1000, 1400) / 100; // 10.00 to 14.00 hours
                $sleepHours = rand(6, 9);
                
                // Random location status
                $locationStatus = $locationStatuses[array_rand($locationStatuses)];
                
                // Generate start and end times
                $startHour = rand(6, 8); // Start between 6 AM and 8 AM
                $startMinute = rand(0, 59);
                $startTime = Carbon::createFromTime($startHour, $startMinute, 0);
                
                // End time based on total hours worked
                $endTime = $startTime->copy()->addHours($totalHoursWorked)->addMinutes($breakMinutes);
                
                // Generate location data
                $locationName = null;
                $portName = null;
                $latitude = null;
                $longitude = null;
                
                if ($locationStatus === 'in_port') {
                    $portName = ['Monaco', 'Cannes', 'Nice', 'Barcelona', 'Palma', 'Portofino', 'Sardinia', 'Corsica'][array_rand(['Monaco', 'Cannes', 'Nice', 'Barcelona', 'Palma', 'Portofino', 'Sardinia', 'Corsica'])];
                    $locationName = $portName . ' Marina';
                    $latitude = 43.7 + (rand(-50, 50) / 100); // Around Mediterranean
                    $longitude = 7.4 + (rand(-50, 50) / 100);
                } elseif ($locationStatus === 'at_sea') {
                    $latitude = 40.0 + (rand(-100, 100) / 10); // Mediterranean area
                    $longitude = 5.0 + (rand(-100, 100) / 10);
                    $locationName = 'At Sea';
                }

                // Generate random activities (2-5 activities)
                $numActivities = rand(2, 5);
                $activities = [];
                for ($j = 0; $j < $numActivities; $j++) {
                    $activities[] = $activitiesList[array_rand($activitiesList)];
                }
                $activities = array_unique($activities); // Remove duplicates

                // Determine compliance (90% compliant, 8% warning, 2% violation)
                $complianceRoll = rand(1, 100);
                if ($complianceRoll <= 90) {
                    $isCompliant = true;
                    $complianceStatus = 'compliant';
                    $complianceNotes = null;
                } elseif ($complianceRoll <= 98) {
                    $isCompliant = false;
                    $complianceStatus = 'warning';
                    $complianceNotes = 'Rest hours slightly below recommended minimum';
                } else {
                    $isCompliant = false;
                    $complianceStatus = 'violation';
                    $complianceNotes = 'Insufficient rest hours or excessive work hours';
                }

                // Create work log
                $workLog = WorkLog::create([
                    'user_id' => $user->id,
                    'work_date' => $workDate,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                    'total_hours_worked' => $totalHoursWorked,
                    'overtime_hours' => $overtimeHours,
                    'break_minutes' => $breakMinutes,
                    'total_rest_hours' => $totalRestHours,
                    'rest_uninterrupted' => rand(1, 10) > 2, // 80% uninterrupted
                    'sleep_hours' => $sleepHours,
                    'location_status' => $locationStatus,
                    'location_name' => $locationName,
                    'port_name' => $portName,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'yacht_name' => $yacht ? $yacht->name : 'M/Y Ocean Dream',
                    'yacht_type' => $yacht ? $yacht->type : 'Motor Yacht',
                    'yacht_length' => $yacht ? ($yacht->length_feet . ' ft') : '120 ft',
                    'yacht_flag' => $yacht ? $yacht->flag_registry : 'Malta',
                    'position_rank' => $positions[array_rand($positions)],
                    'department' => $departments[array_rand($departments)],
                    'captain_name' => 'Captain ' . ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones'][array_rand(['Smith', 'Johnson', 'Williams', 'Brown', 'Jones'])],
                    'company_name' => ['Ocean Yachts', 'Maritime Services', 'Luxury Yacht Management'][array_rand(['Ocean Yachts', 'Maritime Services', 'Luxury Yacht Management'])],
                    'weather_conditions' => $weatherConditions[array_rand($weatherConditions)],
                    'sea_state' => $seaStates[array_rand($seaStates)],
                    'visibility' => $visibilities[array_rand($visibilities)],
                    'activities' => $activities,
                    'notes' => rand(1, 3) === 1 ? 'Standard operations day. All systems functioning normally.' : null,
                    'is_compliant' => $isCompliant,
                    'compliance_status' => $complianceStatus,
                    'compliance_notes' => $complianceNotes,
                    'counts_towards_sea_service' => $locationStatus === 'at_sea',
                    'is_at_sea' => $locationStatus === 'at_sea',
                    'is_day_off' => false,
                ]);

                // Create rest periods (1-3 rest periods per day)
                $numRestPeriods = rand(1, 3);
                $restTypes = ['night_sleep', 'afternoon_nap', 'lunch_break', 'coffee_break'];
                
                for ($k = 0; $k < $numRestPeriods; $k++) {
                    $restType = $restTypes[array_rand($restTypes)];
                    
                    // Generate rest period times based on type
                    if ($restType === 'night_sleep') {
                        $restStartHour = rand(22, 23); // 10 PM to 11 PM
                        $restStartMinute = rand(0, 59);
                        $restDuration = rand(600, 900) / 100; // 6-9 hours
                    } elseif ($restType === 'afternoon_nap') {
                        $restStartHour = rand(13, 15); // 1 PM to 3 PM
                        $restStartMinute = rand(0, 59);
                        $restDuration = rand(30, 120) / 100; // 0.5-2 hours
                    } elseif ($restType === 'lunch_break') {
                        $restStartHour = 12;
                        $restStartMinute = rand(0, 30);
                        $restDuration = rand(30, 90) / 100; // 0.5-1.5 hours
                    } else { // coffee_break
                        $restStartHour = rand(10, 11);
                        $restStartMinute = rand(0, 59);
                        $restDuration = rand(15, 30) / 100; // 0.25-0.5 hours
                    }
                    
                    $restStart = Carbon::createFromTime($restStartHour, $restStartMinute, 0);
                    $restEnd = $restStart->copy()->addHours($restDuration);
                    
                    WorkLogRestPeriod::create([
                        'work_log_id' => $workLog->id,
                        'start_time' => $restStart->format('H:i:s'),
                        'end_time' => $restEnd->format('H:i:s'),
                        'duration_hours' => $restDuration,
                        'type' => $restType,
                        'is_uninterrupted' => $restType === 'night_sleep' ? true : rand(1, 10) > 2,
                        'location' => $restType === 'night_sleep' ? 'Crew cabin' : ($restType === 'lunch_break' ? 'Crew mess' : 'On duty'),
                        'notes' => rand(1, 5) === 1 ? 'Restful period' : null,
                    ]);
                }
            }
            
            $this->command->info("Created work logs for user: {$user->first_name} {$user->last_name}");
        }

        $this->command->info('Work log seeding completed!');
    }
}

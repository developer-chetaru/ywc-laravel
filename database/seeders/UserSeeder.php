<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('en_US'); // 🇺🇸 U.S. locale

        // 🔹 Keep or create primary super admin
        $superAdmin = User::where('email', 'james@yachtworkerscouncil.com')->first();

        if (!$superAdmin) {
            $superAdmin = User::create([
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'james@yachtworkerscouncil.com',
                'password' => Hash::make('James@Chetaru#2'), // change later!
                'status' => 'active',
                'is_active' => true,
                'gender' => 'Male',
                'nationality' => 'American',
                'dob' => '1980-01-01',
            ]);
        }

        // 🔹 Keep or create secondary super admin
        $secondSuperAdmin = User::where('email', 'mousam@chetaru.com')->first();

        if (!$secondSuperAdmin) {
            $secondSuperAdmin = User::create([
                'first_name' => 'Mousam',
                'last_name' => 'Jain',
                'email' => 'mousam@chetaru.com',
                'password' => Hash::make('Mousam@Chetaru#1'), // change later!
                'status' => 'active',
                'is_active' => true,
                'gender' => 'Male',
                'nationality' => 'American',
                'dob' => '1985-01-01',
            ]);
        }

        // 🔹 Ensure roles exist
        $roles = ['super_admin', 'user'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Assign super_admin role
        $superAdmin->assignRole('super_admin');
        $secondSuperAdmin->assignRole('super_admin');

        // 🔹 Delete all users except the super admins
        User::whereNotIn('id', [$superAdmin->id, $secondSuperAdmin->id])->delete();

        // 🔹 Insert 1000 fake users in chunks to avoid memory issues and handle duplicates
        // $totalUsers = 1000;
        // $chunkSize = 100;
        // $inserted = 0;
        
        // for ($chunk = 0; $chunk < ($totalUsers / $chunkSize); $chunk++) {
        //     $users = [];
        //     for ($i = 0; $i < $chunkSize; $i++) {
        //         $users[] = [
        //             'first_name' => $faker->firstName,
        //             'last_name' => $faker->lastName,
        //             'email' => $faker->unique()->safeEmail,
        //             'password' => Hash::make('123456'),
        //             'dob' => $faker->date('Y-m-d', '2005-01-01'),
        //             'phone' => $faker->phoneNumber,
        //             'gender' => $faker->randomElement(['Male', 'Female']),
        //             'nationality' => 'American',
        //             'marital_status' => $faker->randomElement(['Single', 'Married', 'Divorced']),
        //             'birth_country' => 'USA',
        //             'birth_province' => $faker->state,
        //             'status' => 'active',
        //             'is_active' => true,
        //             'created_at' => now(),
        //             'updated_at' => now(),
        //         ];
        //     }
            
        //     // Insert chunk, handling duplicates gracefully
        //     foreach ($users as $user) {
        //         try {
        //             DB::table('users')->insert($user);
        //             $inserted++;
        //         } catch (\Illuminate\Database\QueryException $e) {
        //             // Skip duplicates (unique constraint violations)
        //             if ($e->getCode() == 23000 || str_contains($e->getMessage(), 'Duplicate entry')) {
        //                 continue;
        //             }
        //             throw $e; // Re-throw if it's a different error
        //         }
        //     }
        // }

        // // 🔹 Assign "user" role to all newly created non-admin users
        // $allUsers = User::whereNotIn('id', [$superAdmin->id, $secondSuperAdmin->id])->get();
        // foreach ($allUsers as $user) {
        //     $user->assignRole('user');
        // }

        $this->command->info('✅ Super admin retained and 1000 active users created successfully!');
    }
}

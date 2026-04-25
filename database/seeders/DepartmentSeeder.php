<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\School\Models\Department;
use Modules\School\Models\School;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = School::all();

        if ($schools->isEmpty()) {
            $this->command->warn('No schools found. Please run SchoolSeeder first.');
            return;
        }

        $departmentTemplates = [
            [
                'name' => 'Computer Science',
                'code' => 'CS',
                'description' => 'Department focused on computer science, software engineering, and information technology.',
                'head_of_department' => 'Dr. Sokha Chan',
                'email' => 'cs@{school}.edu.kh',
                'phone' => '+855 23 880 001',
                'office_location' => 'Building A, Room 101',
                'established_year' => 2000,
                'total_staff' => 25,
                'total_students' => 500,
            ],
            [
                'name' => 'Business Administration',
                'code' => 'BA',
                'description' => 'Department offering programs in business management, marketing, and entrepreneurship.',
                'head_of_department' => 'Dr. Vanna Sok',
                'email' => 'ba@{school}.edu.kh',
                'phone' => '+855 23 880 002',
                'office_location' => 'Building B, Room 201',
                'established_year' => 1998,
                'total_staff' => 20,
                'total_students' => 600,
            ],
            [
                'name' => 'Engineering',
                'code' => 'ENG',
                'description' => 'Department covering civil, electrical, and mechanical engineering programs.',
                'head_of_department' => 'Dr. Pich Samnang',
                'email' => 'eng@{school}.edu.kh',
                'phone' => '+855 23 880 003',
                'office_location' => 'Building C, Room 301',
                'established_year' => 1995,
                'total_staff' => 30,
                'total_students' => 450,
            ],
            [
                'name' => 'Mathematics',
                'code' => 'MATH',
                'description' => 'Department dedicated to pure and applied mathematics education.',
                'head_of_department' => 'Dr. Srey Leap',
                'email' => 'math@{school}.edu.kh',
                'phone' => '+855 23 880 004',
                'office_location' => 'Building D, Room 401',
                'established_year' => 1990,
                'total_staff' => 15,
                'total_students' => 200,
            ],
            [
                'name' => 'English Language',
                'code' => 'ENG-LANG',
                'description' => 'Department focusing on English language education and linguistics.',
                'head_of_department' => 'Dr. Maly Keo',
                'email' => 'english@{school}.edu.kh',
                'phone' => '+855 23 880 005',
                'office_location' => 'Building E, Room 501',
                'established_year' => 1985,
                'total_staff' => 18,
                'total_students' => 350,
            ],
        ];

        foreach ($schools as $school) {
            // Each school gets 3-5 random departments
            $numDepartments = rand(3, 5);
            $selectedDepartments = collect($departmentTemplates)->random($numDepartments);

            foreach ($selectedDepartments as $dept) {
                $code = $school->code . '-' . $dept['code'];

                // Skip if department with this code already exists
                if (Department::where('code', $code)->exists()) {
                    continue;
                }

                Department::create([
                    'uuid' => (string) Str::uuid(),
                    'school_id' => $school->id,
                    'name' => $dept['name'],
                    'code' => $code,
                    'description' => $dept['description'],
                    'head_of_department' => $dept['head_of_department'],
                    'email' => str_replace('{school}', strtolower($school->code), $dept['email']),
                    'phone' => $dept['phone'],
                    'office_location' => $dept['office_location'],
                    'established_year' => $dept['established_year'],
                    'total_staff' => $dept['total_staff'],
                    'total_students' => $dept['total_students'],
                    'status' => true,
                ]);
            }
        }
    }
}

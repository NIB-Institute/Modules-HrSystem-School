<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\School\Models\Department;
use Modules\School\Models\Program;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::with('school')->get();

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        $programsByDepartment = [
            'Computer Science' => [
                [
                    'name' => 'Bachelor of Computer Science',
                    'code_suffix' => 'BCS',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 120,
                    'tuition_fee' => 2500.00,
                    'description' => 'A comprehensive program covering software development, algorithms, and computer systems.',
                    'admission_requirements' => 'High school diploma with strong math background',
                    'max_students' => 100,
                ],
                [
                    'name' => 'Master of Computer Science',
                    'code_suffix' => 'MCS',
                    'degree_level' => 'master',
                    'duration_years' => 2,
                    'credits_required' => 36,
                    'tuition_fee' => 4000.00,
                    'description' => 'Advanced study in computer science with research opportunities.',
                    'admission_requirements' => 'Bachelor degree in CS or related field',
                    'max_students' => 30,
                ],
            ],
            'Business Administration' => [
                [
                    'name' => 'Bachelor of Business Administration',
                    'code_suffix' => 'BBA',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 120,
                    'tuition_fee' => 2200.00,
                    'description' => 'Comprehensive business education covering management, marketing, and finance.',
                    'admission_requirements' => 'High school diploma',
                    'max_students' => 150,
                ],
                [
                    'name' => 'Master of Business Administration',
                    'code_suffix' => 'MBA',
                    'degree_level' => 'master',
                    'duration_years' => 2,
                    'credits_required' => 42,
                    'tuition_fee' => 5500.00,
                    'description' => 'Professional graduate degree for business leaders.',
                    'admission_requirements' => 'Bachelor degree and 2+ years work experience',
                    'max_students' => 40,
                ],
            ],
            'Engineering' => [
                [
                    'name' => 'Bachelor of Civil Engineering',
                    'code_suffix' => 'BCE',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 130,
                    'tuition_fee' => 2800.00,
                    'description' => 'Study of infrastructure design and construction.',
                    'admission_requirements' => 'High school diploma with physics and math',
                    'max_students' => 80,
                ],
                [
                    'name' => 'Bachelor of Electrical Engineering',
                    'code_suffix' => 'BEE',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 130,
                    'tuition_fee' => 2800.00,
                    'description' => 'Study of electrical systems and electronics.',
                    'admission_requirements' => 'High school diploma with physics and math',
                    'max_students' => 80,
                ],
            ],
            'Mathematics' => [
                [
                    'name' => 'Bachelor of Mathematics',
                    'code_suffix' => 'BMATH',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 120,
                    'tuition_fee' => 2000.00,
                    'description' => 'Study of pure and applied mathematics.',
                    'admission_requirements' => 'High school diploma with strong math grades',
                    'max_students' => 60,
                ],
            ],
            'English Language' => [
                [
                    'name' => 'Bachelor of Arts in English',
                    'code_suffix' => 'BAE',
                    'degree_level' => 'bachelor',
                    'duration_years' => 4,
                    'credits_required' => 120,
                    'tuition_fee' => 1800.00,
                    'description' => 'Study of English language, literature, and linguistics.',
                    'admission_requirements' => 'High school diploma',
                    'max_students' => 100,
                ],
                [
                    'name' => 'Certificate in English Proficiency',
                    'code_suffix' => 'CEP',
                    'degree_level' => 'certificate',
                    'duration_years' => 1,
                    'credits_required' => 24,
                    'tuition_fee' => 800.00,
                    'description' => 'Intensive English language training program.',
                    'admission_requirements' => 'Basic English proficiency',
                    'max_students' => 50,
                ],
            ],
        ];

        foreach ($departments as $department) {
            $programs = $programsByDepartment[$department->name] ?? [];

            foreach ($programs as $prog) {
                $code = $department->code . '-' . $prog['code_suffix'];

                // Skip if program with this code already exists
                if (Program::where('code', $code)->exists()) {
                    continue;
                }

                Program::create([
                    'uuid' => (string) Str::uuid(),
                    'school_id' => $department->school_id,
                    'department_id' => $department->id,
                    'name' => $prog['name'],
                    'code' => $code,
                    'description' => $prog['description'],
                    'degree_level' => $prog['degree_level'],
                    'duration_years' => $prog['duration_years'],
                    'credits_required' => $prog['credits_required'],
                    'tuition_fee' => $prog['tuition_fee'],
                    'admission_requirements' => $prog['admission_requirements'],
                    'program_coordinator' => 'Dr. ' . fake()->name(),
                    'max_students' => $prog['max_students'],
                    'current_enrollment' => rand(10, $prog['max_students'] - 10),
                    'accreditation_status' => 'Accredited',
                    'status' => true,
                ]);
            }
        }
    }
}

<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\School\Models\Course;
use Modules\School\Models\Department;
use Modules\School\Models\Program;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = Program::with(['department', 'school'])->get();

        if ($programs->isEmpty()) {
            $this->command->warn('No programs found. Please run ProgramSeeder first.');
            return;
        }

        $coursesByDepartment = [
            'Computer Science' => [
                ['name' => 'Introduction to Programming', 'code' => 'CS101', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 1],
                ['name' => 'Data Structures', 'code' => 'CS201', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 1],
                ['name' => 'Algorithms', 'code' => 'CS301', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 2],
                ['name' => 'Database Systems', 'code' => 'CS302', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 2],
                ['name' => 'Operating Systems', 'code' => 'CS401', 'credits' => 3, 'type' => 'core', 'semester' => 1, 'year' => 3],
                ['name' => 'Web Development', 'code' => 'CS402', 'credits' => 3, 'type' => 'elective', 'semester' => 2, 'year' => 3],
                ['name' => 'Machine Learning', 'code' => 'CS501', 'credits' => 3, 'type' => 'elective', 'semester' => 1, 'year' => 4],
                ['name' => 'Software Engineering', 'code' => 'CS502', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 4],
            ],
            'Business Administration' => [
                ['name' => 'Principles of Management', 'code' => 'BA101', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 1],
                ['name' => 'Financial Accounting', 'code' => 'BA102', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 1],
                ['name' => 'Marketing Fundamentals', 'code' => 'BA201', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 2],
                ['name' => 'Business Statistics', 'code' => 'BA202', 'credits' => 3, 'type' => 'core', 'semester' => 2, 'year' => 2],
                ['name' => 'Human Resource Management', 'code' => 'BA301', 'credits' => 3, 'type' => 'elective', 'semester' => 1, 'year' => 3],
                ['name' => 'Strategic Management', 'code' => 'BA401', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 4],
            ],
            'Engineering' => [
                ['name' => 'Engineering Mathematics', 'code' => 'ENG101', 'credits' => 4, 'type' => 'required', 'semester' => 1, 'year' => 1],
                ['name' => 'Physics for Engineers', 'code' => 'ENG102', 'credits' => 4, 'type' => 'required', 'semester' => 2, 'year' => 1],
                ['name' => 'Mechanics of Materials', 'code' => 'ENG201', 'credits' => 3, 'type' => 'core', 'semester' => 1, 'year' => 2],
                ['name' => 'Thermodynamics', 'code' => 'ENG202', 'credits' => 3, 'type' => 'core', 'semester' => 2, 'year' => 2],
                ['name' => 'Engineering Design', 'code' => 'ENG301', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 3],
                ['name' => 'Project Management', 'code' => 'ENG401', 'credits' => 3, 'type' => 'elective', 'semester' => 2, 'year' => 4],
            ],
            'Mathematics' => [
                ['name' => 'Calculus I', 'code' => 'MATH101', 'credits' => 4, 'type' => 'required', 'semester' => 1, 'year' => 1],
                ['name' => 'Calculus II', 'code' => 'MATH102', 'credits' => 4, 'type' => 'required', 'semester' => 2, 'year' => 1],
                ['name' => 'Linear Algebra', 'code' => 'MATH201', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 2],
                ['name' => 'Probability & Statistics', 'code' => 'MATH202', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 2],
                ['name' => 'Differential Equations', 'code' => 'MATH301', 'credits' => 3, 'type' => 'core', 'semester' => 1, 'year' => 3],
            ],
            'English Language' => [
                ['name' => 'English Grammar', 'code' => 'ENG-L101', 'credits' => 3, 'type' => 'required', 'semester' => 1, 'year' => 1],
                ['name' => 'Academic Writing', 'code' => 'ENG-L102', 'credits' => 3, 'type' => 'required', 'semester' => 2, 'year' => 1],
                ['name' => 'Public Speaking', 'code' => 'ENG-L201', 'credits' => 3, 'type' => 'core', 'semester' => 1, 'year' => 2],
                ['name' => 'English Literature', 'code' => 'ENG-L202', 'credits' => 3, 'type' => 'elective', 'semester' => 2, 'year' => 2],
                ['name' => 'Translation Studies', 'code' => 'ENG-L301', 'credits' => 3, 'type' => 'elective', 'semester' => 1, 'year' => 3],
            ],
        ];

        $schedules = [
            'Mon/Wed 8:00-9:30',
            'Mon/Wed 10:00-11:30',
            'Tue/Thu 8:00-9:30',
            'Tue/Thu 10:00-11:30',
            'Tue/Thu 13:00-14:30',
            'Mon/Wed 13:00-14:30',
            'Fri 8:00-11:00',
        ];

        $rooms = ['A101', 'A102', 'A201', 'B101', 'B102', 'B201', 'C101', 'C102', 'Lab 1', 'Lab 2'];

        foreach ($programs as $program) {
            if (!$program->department) {
                continue;
            }

            $courses = $coursesByDepartment[$program->department->name] ?? [];

            foreach ($courses as $course) {
                $code = $program->code . '-' . $course['code'];

                // Skip if course with this code already exists
                if (Course::where('code', $code)->exists()) {
                    continue;
                }

                $maxStudents = rand(25, 50);

                Course::create([
                    'uuid' => (string) Str::uuid(),
                    'department_id' => $program->department_id,
                    'program_id' => $program->id,
                    'instructor_id' => null, // No instructors seeded yet
                    'name' => $course['name'],
                    'code' => $code,
                    'description' => 'This course covers ' . strtolower($course['name']) . ' concepts and practical applications.',
                    'credits' => $course['credits'],
                    'type' => $course['type'],
                    'semester' => $course['semester'],
                    'year' => $course['year'],
                    'max_students' => $maxStudents,
                    'current_enrollment' => rand(5, $maxStudents - 5),
                    'schedule' => $schedules[array_rand($schedules)],
                    'room' => $rooms[array_rand($rooms)],
                    'prerequisites' => null,
                    'syllabus' => null,
                    'status' => true,
                ]);
            }
        }
    }
}

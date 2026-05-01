<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Classroom;
use Modules\School\Models\Department;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->isEmpty()) {
            $this->command->warn('No departments found. Please run DepartmentSeeder first.');
            return;
        }

        $departmentByName = $departments->keyBy('name');

        $classrooms = [
            // Lecture Halls — Business Administration owns the big halls.
            [
                'department' => 'Business Administration',
                'name' => 'Main Lecture Hall A',
                'code' => 'LH-A01',
                'building' => 'Main Building',
                'floor' => 1,
                'capacity' => 200,
                'type' => Classroom::TYPE_LECTURE_HALL,
                'equipment' => ['projector', 'audio_system', 'video_conferencing'],
                'description' => 'Large lecture hall with tiered seating',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Business Administration',
                'name' => 'Main Lecture Hall B',
                'code' => 'LH-B01',
                'building' => 'Main Building',
                'floor' => 1,
                'capacity' => 150,
                'type' => Classroom::TYPE_LECTURE_HALL,
                'equipment' => ['projector', 'audio_system'],
                'description' => 'Medium lecture hall',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],

            // Regular Classrooms — split between CS and English Language.
            [
                'department' => 'Computer Science',
                'name' => 'Room 101',
                'code' => 'RM-101',
                'building' => 'Academic Block A',
                'floor' => 1,
                'capacity' => 40,
                'type' => Classroom::TYPE_CLASSROOM,
                'equipment' => ['projector'],
                'description' => 'Standard classroom',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Computer Science',
                'name' => 'Room 102',
                'code' => 'RM-102',
                'building' => 'Academic Block A',
                'floor' => 1,
                'capacity' => 35,
                'type' => Classroom::TYPE_CLASSROOM,
                'equipment' => ['projector', 'smartboard'],
                'description' => 'Smart classroom with interactive board',
                'has_projector' => true,
                'has_whiteboard' => false,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'English Language',
                'name' => 'Room 201',
                'code' => 'RM-201',
                'building' => 'Academic Block A',
                'floor' => 2,
                'capacity' => 45,
                'type' => Classroom::TYPE_CLASSROOM,
                'equipment' => ['projector'],
                'description' => 'Standard classroom on second floor',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'English Language',
                'name' => 'Room 202',
                'code' => 'RM-202',
                'building' => 'Academic Block A',
                'floor' => 2,
                'capacity' => 30,
                'type' => Classroom::TYPE_CLASSROOM,
                'equipment' => [],
                'description' => 'Small classroom',
                'has_projector' => false,
                'has_whiteboard' => true,
                'has_ac' => false,
                'is_available' => true,
                'status' => true,
            ],

            // Computer Labs — Computer Science.
            [
                'department' => 'Computer Science',
                'name' => 'Computer Lab 1',
                'code' => 'CL-01',
                'building' => 'Technology Center',
                'floor' => 1,
                'capacity' => 30,
                'type' => Classroom::TYPE_LAB,
                'equipment' => ['computer', 'projector', 'smartboard'],
                'description' => 'Computer lab with 30 workstations',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Computer Science',
                'name' => 'Computer Lab 2',
                'code' => 'CL-02',
                'building' => 'Technology Center',
                'floor' => 1,
                'capacity' => 25,
                'type' => Classroom::TYPE_LAB,
                'equipment' => ['computer', 'projector'],
                'description' => 'Computer lab with 25 workstations',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Mathematics',
                'name' => 'Science Lab',
                'code' => 'SL-01',
                'building' => 'Science Building',
                'floor' => 2,
                'capacity' => 24,
                'type' => Classroom::TYPE_LAB,
                'equipment' => ['lab_equipment', 'projector', 'document_camera'],
                'description' => 'Fully equipped science laboratory',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Mathematics',
                'name' => 'Physics Lab',
                'code' => 'PL-01',
                'building' => 'Science Building',
                'floor' => 3,
                'capacity' => 20,
                'type' => Classroom::TYPE_LAB,
                'equipment' => ['lab_equipment', 'projector'],
                'description' => 'Physics laboratory with experiment stations',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],

            // Seminar Rooms — Business Administration.
            [
                'department' => 'Business Administration',
                'name' => 'Seminar Room 1',
                'code' => 'SR-01',
                'building' => 'Main Building',
                'floor' => 2,
                'capacity' => 20,
                'type' => Classroom::TYPE_SEMINAR,
                'equipment' => ['projector', 'video_conferencing'],
                'description' => 'Conference-style seminar room',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'Business Administration',
                'name' => 'Seminar Room 2',
                'code' => 'SR-02',
                'building' => 'Main Building',
                'floor' => 2,
                'capacity' => 15,
                'type' => Classroom::TYPE_SEMINAR,
                'equipment' => ['projector'],
                'description' => 'Small seminar room for group discussions',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],

            // Auditorium — Business Administration.
            [
                'department' => 'Business Administration',
                'name' => 'Main Auditorium',
                'code' => 'AUD-01',
                'building' => 'Auditorium Building',
                'floor' => 1,
                'capacity' => 500,
                'type' => Classroom::TYPE_AUDITORIUM,
                'equipment' => ['projector', 'audio_system', 'video_conferencing'],
                'description' => 'Main auditorium for large events and ceremonies',
                'has_projector' => true,
                'has_whiteboard' => false,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],

            // Workshop — Engineering and English (Art Studio).
            [
                'department' => 'Engineering',
                'name' => 'Engineering Workshop',
                'code' => 'WS-01',
                'building' => 'Engineering Block',
                'floor' => 1,
                'capacity' => 30,
                'type' => Classroom::TYPE_WORKSHOP,
                'equipment' => ['workshop_tools', 'projector'],
                'description' => 'Fully equipped engineering workshop',
                'has_projector' => true,
                'has_whiteboard' => true,
                'has_ac' => false,
                'is_available' => true,
                'status' => true,
            ],
            [
                'department' => 'English Language',
                'name' => 'Art Studio',
                'code' => 'AS-01',
                'building' => 'Arts Building',
                'floor' => 1,
                'capacity' => 25,
                'type' => Classroom::TYPE_WORKSHOP,
                'equipment' => [],
                'description' => 'Art studio with natural lighting',
                'has_projector' => false,
                'has_whiteboard' => true,
                'has_ac' => true,
                'is_available' => true,
                'status' => true,
            ],
        ];

        $created = 0;
        foreach ($classrooms as $classroomData) {
            $deptName = $classroomData['department'];
            unset($classroomData['department']);

            $department = $departmentByName->get($deptName);
            if (! $department) {
                $this->command->warn("Department '{$deptName}' not found, skipping classroom '{$classroomData['name']}'.");
                continue;
            }

            // Idempotent: skip if a classroom with this code already exists.
            if (Classroom::where('code', $classroomData['code'])->exists()) {
                continue;
            }

            Classroom::create([
                ...$classroomData,
                'department_id' => $department->id,
            ]);
            $created++;
        }

        $this->command->info("Created {$created} classrooms.");
    }
}

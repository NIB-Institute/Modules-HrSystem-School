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

        $classrooms = [
            // Lecture Halls
            [
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

            // Regular Classrooms
            [
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

            // Computer Labs
            [
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

            // Seminar Rooms
            [
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

            // Auditorium
            [
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

            // Workshop
            [
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

        foreach ($classrooms as $classroomData) {
            // Assign to a random department
            $department = $departments->random();

            Classroom::create([
                ...$classroomData,
                'department_id' => $department->id,
            ]);
        }

        $this->command->info('Created ' . count($classrooms) . ' classrooms.');
    }
}

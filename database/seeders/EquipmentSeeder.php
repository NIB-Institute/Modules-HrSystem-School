<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Equipment;

class EquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $equipment = [
            // Technology
            [
                'name' => 'Projector',
                'slug' => 'projector',
                'icon' => 'projector',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Digital projector for presentations and multimedia',
            ],
            [
                'name' => 'Interactive Whiteboard',
                'slug' => 'interactive-whiteboard',
                'icon' => 'presentation',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Smart board with touch capabilities',
            ],
            [
                'name' => 'Computer',
                'slug' => 'computer',
                'icon' => 'monitor',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Desktop computer workstation',
            ],
            [
                'name' => 'Audio System',
                'slug' => 'audio-system',
                'icon' => 'volume-2',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Speakers and audio equipment',
            ],
            [
                'name' => 'Video Conferencing',
                'slug' => 'video-conferencing',
                'icon' => 'video',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Camera and microphone for remote meetings',
            ],
            [
                'name' => 'Document Camera',
                'slug' => 'document-camera',
                'icon' => 'scan',
                'category' => Equipment::CATEGORY_TECHNOLOGY,
                'description' => 'Camera for displaying documents and objects',
            ],

            // Furniture
            [
                'name' => 'Whiteboard',
                'slug' => 'whiteboard',
                'icon' => 'square',
                'category' => Equipment::CATEGORY_FURNITURE,
                'description' => 'Traditional whiteboard for writing',
            ],
            [
                'name' => 'Podium',
                'slug' => 'podium',
                'icon' => 'mic',
                'category' => Equipment::CATEGORY_FURNITURE,
                'description' => 'Lectern for presentations',
            ],
            [
                'name' => 'Lab Equipment',
                'slug' => 'lab-equipment',
                'icon' => 'flask-conical',
                'category' => Equipment::CATEGORY_OTHER,
                'description' => 'Scientific laboratory equipment',
            ],
            [
                'name' => 'Workshop Tools',
                'slug' => 'workshop-tools',
                'icon' => 'wrench',
                'category' => Equipment::CATEGORY_OTHER,
                'description' => 'Tools for hands-on workshops',
            ],

            // Safety
            [
                'name' => 'Fire Extinguisher',
                'slug' => 'fire-extinguisher',
                'icon' => 'flame',
                'category' => Equipment::CATEGORY_SAFETY,
                'description' => 'Fire safety equipment',
            ],
            [
                'name' => 'First Aid Kit',
                'slug' => 'first-aid-kit',
                'icon' => 'heart-pulse',
                'category' => Equipment::CATEGORY_SAFETY,
                'description' => 'Medical first aid supplies',
            ],
            [
                'name' => 'Emergency Exit Sign',
                'slug' => 'emergency-exit-sign',
                'icon' => 'door-open',
                'category' => Equipment::CATEGORY_SAFETY,
                'description' => 'Illuminated emergency exit signage',
            ],

            // Accessibility
            [
                'name' => 'Wheelchair Accessible',
                'slug' => 'wheelchair-accessible',
                'icon' => 'accessibility',
                'category' => Equipment::CATEGORY_ACCESSIBILITY,
                'description' => 'Wheelchair accessible facilities',
            ],
            [
                'name' => 'Hearing Loop',
                'slug' => 'hearing-loop',
                'icon' => 'ear',
                'category' => Equipment::CATEGORY_ACCESSIBILITY,
                'description' => 'Hearing assistance system',
            ],
        ];

        foreach ($equipment as $item) {
            Equipment::create([
                ...$item,
                'status' => true,
            ]);
        }

        $this->command->info('Created ' . count($equipment) . ' equipment types.');
    }
}

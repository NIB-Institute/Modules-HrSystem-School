<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\School\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'National Institute of Business',
                'code' => 'NIB',
                'type' => 'institute',
                'description' => 'A premier business institute offering professional programs in accounting, finance, marketing, and management.',
                'address' => 'Street 271, Toul Tompong',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 215 505',
                'email' => 'info@nib.edu.kh',
                'website' => 'https://www.nib.edu.kh',
                'established_year' => 2002,
                'accreditation' => 'Ministry of Education, Youth and Sport',
                'total_students' => 4500,
                'total_staff' => 280,
                'status' => true,
            ],
        ];

        foreach ($schools as $school) {
            School::firstOrCreate(
                ['code' => $school['code']],
                array_merge($school, ['uuid' => (string) Str::uuid()])
            );
        }
    }
}

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
                'name' => 'Royal University of Phnom Penh',
                'code' => 'RUPP',
                'type' => 'university',
                'description' => 'The oldest and largest public university in Cambodia, offering a wide range of undergraduate and graduate programs.',
                'address' => 'Russian Federation Blvd',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 883 640',
                'email' => 'info@rupp.edu.kh',
                'website' => 'https://www.rupp.edu.kh',
                'established_year' => 1960,
                'accreditation' => 'Ministry of Education, Youth and Sport',
                'total_students' => 20000,
                'total_staff' => 1200,
                'status' => true,
            ],
            [
                'name' => 'Institute of Technology of Cambodia',
                'code' => 'ITC',
                'type' => 'institute',
                'description' => 'A leading engineering and technology institute in Cambodia.',
                'address' => 'Russian Confederation Blvd',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 880 370',
                'email' => 'info@itc.edu.kh',
                'website' => 'https://www.itc.edu.kh',
                'established_year' => 1964,
                'accreditation' => 'Ministry of Education, Youth and Sport',
                'total_students' => 5000,
                'total_staff' => 400,
                'status' => true,
            ],
            [
                'name' => 'Norton University',
                'code' => 'NU',
                'type' => 'university',
                'description' => 'A private university offering diverse programs in business, IT, and engineering.',
                'address' => 'Street 1003, Phnom Penh Thmey',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 998 124',
                'email' => 'info@norton-u.com',
                'website' => 'https://www.norton-u.com',
                'established_year' => 1996,
                'accreditation' => 'Ministry of Education, Youth and Sport',
                'total_students' => 8000,
                'total_staff' => 350,
                'status' => true,
            ],
            [
                'name' => 'Paragon International University',
                'code' => 'PIU',
                'type' => 'university',
                'description' => 'An international university with English-medium instruction and global partnerships.',
                'address' => 'No. 8, St. 315, BKK1',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 966 001',
                'email' => 'info@paragoniu.edu.kh',
                'website' => 'https://www.paragoniu.edu.kh',
                'established_year' => 2013,
                'accreditation' => 'Ministry of Education, Youth and Sport',
                'total_students' => 3000,
                'total_staff' => 200,
                'status' => true,
            ],
            [
                'name' => 'National Polytechnic Institute of Cambodia',
                'code' => 'NPIC',
                'type' => 'institute',
                'description' => 'A technical institute focused on vocational and technical education.',
                'address' => 'Street 217',
                'city' => 'Phnom Penh',
                'country' => 'Cambodia',
                'phone' => '+855 23 726 061',
                'email' => 'info@npic.edu.kh',
                'website' => 'https://www.npic.edu.kh',
                'established_year' => 2005,
                'accreditation' => 'Ministry of Labour and Vocational Training',
                'total_students' => 2500,
                'total_staff' => 150,
                'status' => true,
            ],
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

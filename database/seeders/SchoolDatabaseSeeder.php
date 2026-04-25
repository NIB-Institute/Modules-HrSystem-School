<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;

class SchoolDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SchoolSeeder::class,
            DepartmentSeeder::class,
            ProgramSeeder::class,
            CourseSeeder::class,
        ]);
    }
}

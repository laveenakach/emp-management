<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Designation;


class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
         $designations = [
            // HR - department_id = 2
            ['department_id' => 2, 'name' => 'HR Intern / Trainee'],
            ['department_id' => 2, 'name' => 'HR Assistant'],
            ['department_id' => 2, 'name' => 'Recruitment Executive'],
            ['department_id' => 2, 'name' => 'HR Executive'],

            // Developer - department_id = 1
            ['department_id' => 1, 'name' => 'Intern / Trainee Developer'],
            ['department_id' => 1, 'name' => 'Junior Developer'],
            ['department_id' => 1, 'name' => 'Software Developer'],
            ['department_id' => 1, 'name' => 'Web Developer'],
            ['department_id' => 1, 'name' => 'Front-End Developer'],
            ['department_id' => 1, 'name' => 'Back-End Developer'],

            // Sales - department_id = 5
            ['department_id' => 5, 'name' => 'Sales Executive / Sales Representative'],
            ['department_id' => 5, 'name' => 'Business Development Executive (BDE)'],
            ['department_id' => 5, 'name' => 'Inside Sales Executive'],
            ['department_id' => 5, 'name' => 'Sales Coordinator'],
            ['department_id' => 5, 'name' => 'Key Account Executive'],
            ['department_id' => 5, 'name' => 'Area Sales Manager (ASM)'],
            ['department_id' => 5, 'name' => 'Business Development Manager (BDM)'],

            // Accounts - department_id = 6
            ['department_id' => 6, 'name' => 'Accountant'],
        ];

        foreach ($designations as $designation) {
            Designation::create($designation);
        }

    }
}

<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'الإدارة العامة', 'code' => 'GM', 'description' => 'الإدارة العليا للجمعية'],
            ['name' => 'شؤون الموظفين', 'code' => 'HR', 'description' => 'إدارة الموارد البشرية'],
            ['name' => 'الشؤون المالية', 'code' => 'FIN', 'description' => 'الإدارة المالية والمحاسبة'],
            ['name' => 'الشؤون القانونية', 'code' => 'LEG', 'description' => 'الشؤون القانونية والعقود'],
            ['name' => 'خدمات الأعضاء', 'code' => 'MS', 'description' => 'خدمة أعضاء الجمعية'],
            ['name' => 'التسويق والإعلام', 'code' => 'MKT', 'description' => 'التسويق والاتصال'],
            ['name' => 'تقنية المعلومات', 'code' => 'IT', 'description' => 'إدارة التقنية والأنظمة'],
            ['name' => 'المشتريات والمخازن', 'code' => 'PROC', 'description' => 'المشتريات والمخزون'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // المدير العام
        User::create([
            'employee_id' => '2048',
            'name' => 'مدير النظام',
            'email' => 'admin@sabahalsalem-co.com',
            'phone' => '90000000',
            'civil_id' => '288012300001',
            'job_title' => 'المدير العام',
            'password' => Hash::make('ECWS@2026'),
            'role' => UserRole::SUPER_ADMIN,
            'department_id' => 1,
            'is_active' => true,
            'password_changed' => false,
        ]);

        // رؤساء الأقسام
        $heads = [
            ['employee_id' => '3001', 'name' => 'رئيس الموارد البشرية', 'email' => 'hr.head@sabahalsalem-co.com', 'civil_id' => '288012300002', 'job_title' => 'رئيس قسم الموارد البشرية', 'dept' => 2],
            ['employee_id' => '3002', 'name' => 'رئيس المالية', 'email' => 'fin.head@sabahalsalem-co.com', 'civil_id' => '288012300003', 'job_title' => 'رئيس قسم المالية', 'dept' => 3],
            ['employee_id' => '3003', 'name' => 'رئيس الشؤون القانونية', 'email' => 'leg.head@sabahalsalem-co.com', 'civil_id' => '288012300004', 'job_title' => 'رئيس قسم الشؤون القانونية', 'dept' => 4],
            ['employee_id' => '3004', 'name' => 'رئيس خدمات الأعضاء', 'email' => 'ms.head@sabahalsalem-co.com', 'civil_id' => '288012300005', 'job_title' => 'رئيس قسم خدمات الأعضاء', 'dept' => 5],
            ['employee_id' => '3005', 'name' => 'رئيس التسويق', 'email' => 'mkt.head@sabahalsalem-co.com', 'civil_id' => '288012300006', 'job_title' => 'رئيس قسم التسويق', 'dept' => 6],
            ['employee_id' => '3006', 'name' => 'رئيس تقنية المعلومات', 'email' => 'it.head@sabahalsalem-co.com', 'civil_id' => '288012300007', 'job_title' => 'رئيس قسم تقنية المعلومات', 'dept' => 7],
            ['employee_id' => '3007', 'name' => 'رئيس المشتريات', 'email' => 'proc.head@sabahalsalem-co.com', 'civil_id' => '288012300008', 'job_title' => 'رئيس قسم المشتريات', 'dept' => 8],
        ];

        foreach ($heads as $head) {
            User::create([
                'employee_id' => $head['employee_id'],
                'name' => $head['name'],
                'email' => $head['email'],
                'civil_id' => $head['civil_id'],
                'job_title' => $head['job_title'],
                'password' => Hash::make('ECWS@2026'),
                'role' => UserRole::DEPARTMENT_HEAD,
                'department_id' => $head['dept'],
                'is_active' => true,
                'password_changed' => false,
            ]);
        }

        // موظفين عاديين
        $employees = [
            ['employee_id' => '4001', 'name' => 'أحمد محمد', 'email' => 'ahmed@sabahalsalem-co.com', 'civil_id' => '288012300009', 'job_title' => 'موظف موارد بشرية', 'dept' => 2],
            ['employee_id' => '4002', 'name' => 'فاطمة عبدالله', 'email' => 'fatima@sabahalsalem-co.com', 'civil_id' => '288012300010', 'job_title' => 'محاسبة', 'dept' => 3],
            ['employee_id' => '4003', 'name' => 'خالد السالم', 'email' => 'khaled@sabahalsalem-co.com', 'civil_id' => '288012300011', 'job_title' => 'مسؤول قانوني', 'dept' => 4],
            ['employee_id' => '4004', 'name' => 'نورة أحمد', 'email' => 'noura@sabahalsalem-co.com', 'civil_id' => '288012300012', 'job_title' => 'مسؤولة خدمة عملاء', 'dept' => 5],
        ];

        foreach ($employees as $emp) {
            User::create([
                'employee_id' => $emp['employee_id'],
                'name' => $emp['name'],
                'email' => $emp['email'],
                'civil_id' => $emp['civil_id'],
                'job_title' => $emp['job_title'],
                'password' => Hash::make('ECWS@2026'),
                'role' => UserRole::EMPLOYEE,
                'department_id' => $emp['dept'],
                'is_active' => true,
                'password_changed' => false,
            ]);
        }
    }
}

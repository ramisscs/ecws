<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case DEPARTMENT_HEAD = 'department_head';
    case EMPLOYEE = 'employee';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match($this) {
            self::SUPER_ADMIN => 'مدير عام',
            self::ADMIN => 'مدير نظام',
            self::DEPARTMENT_HEAD => 'رئيس قسم',
            self::EMPLOYEE => 'موظف',
            self::VIEWER => 'مشاهد فقط',
        };
    }

    public function permissions(): array
    {
        return match($this) {
            self::SUPER_ADMIN => ['*'],
            self::ADMIN => [
                'users.*', 'departments.*', 'transactions.*',
                'workflow.*', 'reports.*', 'settings.*',
                'templates.*', 'archive.*', 'audit.*',
            ],
            self::DEPARTMENT_HEAD => [
                'transactions.create', 'transactions.read', 'transactions.update',
                'transactions.transfer', 'transactions.approve',
                'workflow.execute', 'reports.department',
                'templates.use',
            ],
            self::EMPLOYEE => [
                'transactions.create', 'transactions.read', 'transactions.update',
                'transactions.transfer', 'workflow.execute',
                'templates.use',
            ],
            self::VIEWER => [
                'transactions.read',
            ],
        };
    }
}

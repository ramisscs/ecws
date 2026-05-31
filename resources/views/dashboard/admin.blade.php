@extends('layouts.app')

@section('title', 'لوحة المدير')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-2xl font-bold dark:text-white">لوحة المدير</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">نظرة عامة على النظام</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="text-2xl font-bold dark:text-white">{{ $stats['total_transactions'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">إجمالي المعاملات</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-green-600 dark:text-green-400"></i>
                </div>
                <span class="text-2xl font-bold dark:text-white">{{ $stats['active_users'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">المستخدمين النشطين</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-purple-600 dark:text-purple-400"></i>
                </div>
                <span class="text-2xl font-bold dark:text-white">{{ $stats['departments'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">الأقسام</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <span class="text-2xl font-bold dark:text-white">{{ $stats['pending_approvals'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">بانتظار الاعتماد</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-red-600 dark:text-red-400"></i>
                </div>
                <span class="text-2xl font-bold dark:text-white">{{ $stats['secret_transactions'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">سرية</p>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-bold text-lg mb-4 dark:text-white">
            <i class="fas fa-heartbeat ml-2 text-red-500"></i> صحة النظام
        </h3>
        <div class="grid md:grid-cols-3 gap-4">
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-database text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">حجم قاعدة البيانات</p>
                    <p class="font-bold dark:text-white">{{ $systemHealth['database_size'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hdd text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">التخزين المستخدم</p>
                    <p class="font-bold dark:text-white">{{ $systemHealth['storage_used'] }}</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shield-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">آخر نسخة احتياطية</p>
                    <p class="font-bold dark:text-white">{{ $systemHealth['last_backup'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid md:grid-cols-4 gap-4">
        <a href="{{ route('admin.users') }}" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 transition-colors">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-users-cog text-blue-600 dark:text-blue-400 text-xl"></i>
            </div>
            <div>
                <p class="font-bold dark:text-white">المستخدمين</p>
                <p class="text-sm text-gray-500">إدارة الحسابات</p>
            </div>
        </a>

        <a href="{{ route('admin.departments') }}" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 transition-colors">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
            <div>
                <p class="font-bold dark:text-white">الأقسام</p>
                <p class="text-sm text-gray-500">إدارة الأقسام</p>
            </div>
        </a>

        <a href="{{ route('admin.audit') }}" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 transition-colors">
            <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-history text-gray-600 dark:text-gray-400 text-xl"></i>
            </div>
            <div>
                <p class="font-bold dark:text-white">السجل</p>
                <p class="text-sm text-gray-500">تتبع العمليات</p>
            </div>
        </a>

        <a href="{{ route('admin.settings') }}" class="flex items-center gap-4 p-5 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-primary-300 transition-colors">
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-cog text-orange-600 dark:text-orange-400 text-xl"></i>
            </div>
            <div>
                <p class="font-bold dark:text-white">الإعدادات</p>
                <p class="text-sm text-gray-500">إعدادات النظام</p>
            </div>
        </a>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'الملف الشخصي')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- User Info Card -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-primary-500 rounded-full flex items-center justify-center text-white text-xl font-bold">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div>
                <h2 class="text-xl font-bold dark:text-white">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-primary-500 dark:text-primary-400 font-mono">رقم وظيفي: {{ auth()->user()->employee_id }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->job_title ?? auth()->user()->role->label() }}</p>
            </div>
        </div>

        <!-- Info Grid -->
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">القسم</label>
                <span class="font-medium dark:text-white">{{ auth()->user()->department?->name ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">الدور الوظيفي</label>
                <span class="font-medium dark:text-white">{{ auth()->user()->role->label() }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">البريد الإلكتروني</label>
                <span class="font-medium dark:text-white">{{ auth()->user()->email ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">رقم الهاتف</label>
                <span class="font-medium dark:text-white">{{ auth()->user()->phone ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">الرقم المدني</label>
                <span class="font-medium dark:text-white font-mono">{{ auth()->user()->civil_id ?? '—' }}</span>
            </div>
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                <label class="text-gray-500 dark:text-gray-400 block mb-1">تاريخ أول دخول</label>
                <span class="font-medium dark:text-white">{{ auth()->user()->first_login_at?->format('Y-m-d H:i') ?? '—' }}</span>
            </div>
        </div>
    </div>

    <!-- Edit Profile -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <h3 class="font-bold text-lg mb-4 dark:text-white">
            <i class="fas fa-edit ml-2 text-primary-500"></i>
            تعديل البيانات الشخصية
        </h3>

        <form action="{{ route('profile') }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الاسم</label>
                    <input type="text" name="name" value="{{ auth()->user()->name }}" required
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الهاتف</label>
                    <input type="text" name="phone" value="{{ auth()->user()->phone }}"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                <h4 class="font-bold mb-4 dark:text-white">
                    <i class="fas fa-lock ml-2 text-green-500"></i>
                    تغيير كلمة المرور
                </h4>
                <div class="grid md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">كلمة المرور الحالية</label>
                        <input type="password" name="current_password"
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                               placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">كلمة المرور الجديدة</label>
                        <input type="password" name="new_password" minlength="8"
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                               placeholder="8 أحرف على الأقل">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">تأكيد كلمة المرور</label>
                        <input type="password" name="new_password_confirmation"
                               class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                               placeholder="أعد الإدخال">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i> حفظ التغييرات
                </button>

                <a href="{{ route('2fa.setup') }}" class="px-4 py-2.5 bg-green-50 hover:bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-lg text-sm transition-colors">
                    <i class="fas fa-shield-alt ml-1"></i>
                    {{ auth()->user()->two_factor_confirmed_at ? 'إدارة المصادقة الثنائية' : 'تفعيل المصادقة الثنائية' }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

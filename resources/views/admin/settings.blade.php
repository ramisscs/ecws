@extends('layouts.app')

@section('title', 'إعدادات النظام')

@section('content')
<div class="max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-6 dark:text-white">إعدادات النظام</h2>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('admin.settings') }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="font-bold text-lg mb-4 dark:text-white">عام</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">اسم النظام</label>
                        <input type="text" name="app_name" value="{{ $settings['app_name'] }}"
                               class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">بادئة الترقيم</label>
                        <input type="text" name="prefix" value="{{ $settings['prefix'] }}"
                               class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white font-mono">
                    </div>
                </div>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="font-bold text-lg mb-4 dark:text-white">الذكاء الاصطناعي</h3>
                <div class="flex items-center gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="ai_enabled" value="1" {{ $settings['ai_enabled'] ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">تفعيل AI</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">نموذج AI</label>
                    <input type="text" name="ai_model" value="{{ $settings['ai_model'] }}"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                </div>
            </div>

            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="font-bold text-lg mb-4 dark:text-white">الملفات</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحد الأقصى (KB)</label>
                        <input type="number" name="max_file_size" value="{{ $settings['max_file_size'] }}"
                               class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الامتدادات المسموحة</label>
                        <input type="text" name="allowed_extensions" value="{{ $settings['allowed_extensions'] }}"
                               class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4 dark:text-white">السجل</h3>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">فترة الاحتفاظ (يوم)</label>
                    <input type="number" name="audit_retention" value="{{ $settings['audit_retention'] }}"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-save ml-2"></i> حفظ الإعدادات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

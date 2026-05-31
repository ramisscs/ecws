@extends('layouts.app')

@section('title', 'معاملة جديدة')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold dark:text-white">إنشاء معاملة جديدة</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">أدخل بيانات المعاملة المطلوبة</p>
        </div>
        <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
            <i class="fas fa-arrow-right ml-1"></i> رجوع
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Template Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">قالب (اختياري)</label>
                <select name="template_id" id="template" onchange="loadTemplate(this)"
                        class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    <option value="">بدون قالب</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" data-subject="{{ $template->subject }}" data-content="{{ $template->content }}">
                            {{ $template->name }} ({{ $template->type->label() }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Transaction Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    نوع المعاملة <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($types as $type)
                    <label class="cursor-pointer">
                        <input type="radio" name="type" value="{{ $type->value }}" class="peer sr-only" {{ old('type') == $type->value ? 'checked' : '' }}>
                        <div class="flex items-center gap-3 p-3 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-primary-300 peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-all">
                            <div class="w-8 h-8 rounded {{ $type->color() }} flex items-center justify-center">
                                <i class="fas {{ $type->icon() }} text-white text-sm"></i>
                            </div>
                            <span class="text-sm font-medium dark:text-white">{{ $type->label() }}</span>
                        </div>
                    </label>
                    @endforeach
                </div>
                @error('type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Subject -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    الموضوع <span class="text-red-500">*</span>
                </label>
                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                       class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                       placeholder="أدخل موضوع المعاملة">
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Content -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    المحتوى <span class="text-red-500">*</span>
                </label>
                <textarea name="content" id="content" rows="8" required
                          class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white resize-none"
                          placeholder="أدخل تفاصيل المعاملة">{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <!-- Destination Department -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        القسم المستلم <span class="text-red-500">*</span>
                    </label>
                    <select name="to_department_id" required
                            class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                        <option value="">اختر القسم</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('to_department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('to_department_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">تاريخ الاستحقاق</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                </div>
            </div>

            <!-- Options -->
            <div class="flex flex-wrap gap-6">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_secret" value="1" {{ old('is_secret') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-lock text-red-500 ml-1"></i> معاملة سرية
                    </span>
                </label>

                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_urgent" value="1" {{ old('is_urgent') ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-gray-300 text-red-500 focus:ring-red-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        <i class="fas fa-exclamation-triangle text-yellow-500 ml-1"></i> مستعجلة
                    </span>
                </label>
            </div>

            <!-- Attachments -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">المرفقات</label>
                <input type="file" name="attachments[]" multiple
                       class="w-full px-3 py-2.5 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 border-dashed rounded-lg dark:text-white"
                       accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.zip">
                <p class="text-xs text-gray-500 mt-1">PDF, Word, Excel, Images, ZIP (Max: 10MB)</p>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="submit" 
                        class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-paper-plane ml-2"></i>
                    إنشاء المعاملة
                </button>
                <a href="{{ route('transactions.index') }}" 
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function loadTemplate(select) {
    const option = select.options[select.selectedIndex];
    if (option.value) {
        document.getElementById('subject').value = option.dataset.subject || '';
        document.getElementById('content').value = option.dataset.content || '';
    }
}
</script>
@endsection

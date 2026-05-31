@extends('layouts.app')

@section('title', 'المعاملات')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold dark:text-white">المعاملات</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">إدارة وعرض جميع المعاملات</p>
        </div>
        <a href="{{ route('transactions.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
            <i class="fas fa-plus"></i>
            معاملة جديدة
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
        <form action="{{ route('transactions.index') }}" method="GET" class="grid md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم المعاملة</label>
                <input type="text" name="tracking_number" value="{{ request('tracking_number') }}"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">النوع</label>
                <select name="type" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
                    <option value="">الكل</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ request('type') == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الحالة</label>
                <select name="status" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
                    <option value="">الكل</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">القسم</label>
                <select name="department_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
                    <option value="">الكل</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الموضوع</label>
                <input type="text" name="subject" value="{{ request('subject') }}"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm transition-colors">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
                <a href="{{ route('transactions.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg text-sm transition-colors">
                    إعادة
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-right font-medium">الرقم</th>
                        <th class="px-4 py-3 text-right font-medium">النوع</th>
                        <th class="px-4 py-3 text-right font-medium">الموضوع</th>
                        <th class="px-4 py-3 text-right font-medium">القسم المرسل</th>
                        <th class="px-4 py-3 text-right font-medium">الحالة</th>
                        <th class="px-4 py-3 text-right font-medium">التاريخ</th>
                        <th class="px-4 py-3 text-right font-medium">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500 dark:text-gray-400">
                            {{ $tx->tracking_number }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-medium rounded {{ $tx->type->color() }} text-white">
                                <i class="fas {{ $tx->type->icon() }} text-xs"></i>
                                {{ $tx->type->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-medium dark:text-white truncate max-w-xs">{{ $tx->subject }}</p>
                            @if($tx->is_secret)
                                <span class="inline-flex items-center gap-1 text-xs text-red-500 mt-0.5">
                                    <i class="fas fa-lock"></i> سرية
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">
                            {{ $tx->fromDepartment?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs rounded-full {{ $tx->status->color() }} text-white">
                                <i class="fas {{ $tx->status->icon() }} text-xs"></i>
                                {{ $tx->status->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $tx->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('transactions.show', $tx) }}" 
                               class="text-primary-500 hover:text-primary-600 text-sm">
                                <i class="fas fa-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-3 block"></i>
                            لا توجد معاملات
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

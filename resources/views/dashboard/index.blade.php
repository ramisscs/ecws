@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<div class="space-y-6">
    <!-- Welcome -->
    <div class="bg-primary-500 rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">أهلاً، {{ auth()->user()->name }}</h2>
                <p class="text-primary-200 mt-1">نظرة عامة على معاملاتك اليوم</p>
            </div>
            <div class="hidden md:block w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                <i class="fas fa-user-tie text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-inbox text-blue-600 dark:text-blue-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['new_transactions'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">جديدة</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['pending_approval'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">بانتظار الاعتماد</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-share text-indigo-600 dark:text-indigo-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['transferred_to_me'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">محولة لك</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-double text-green-600 dark:text-green-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_approved'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">معتمدة</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_rejected'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">مرفوضة</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-lock text-gray-600 dark:text-gray-400"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['closed_this_month'] }}</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">مغلقة هذا الشهر</p>
        </div>
    </div>

    <!-- Recent Transactions & Activity -->
    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-bold text-lg dark:text-white">
                    <i class="fas fa-list ml-2 text-primary-500"></i>
                    أحدث المعاملات
                </h3>
                <a href="{{ route('transactions.index') }}" class="text-sm text-primary-500 hover:text-primary-600">
                    عرض الكل
                </a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentTransactions as $tx)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-1 text-xs font-medium rounded {{ $tx->type->color() }} text-white">
                                {{ $tx->type->label() }}
                            </span>
                            <div>
                                <p class="font-medium text-sm dark:text-white">{{ $tx->subject }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $tx->tracking_number }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-400">{{ $tx->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>لا توجد معاملات حديثة</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <div class="p-5 border-b border-gray-200 dark:border-gray-700">
                <h3 class="font-bold text-lg dark:text-white">
                    <i class="fas fa-chart-line ml-2 text-primary-500"></i>
                    نشاطك الأخير
                </h3>
            </div>
            <div class="p-4 space-y-4">
                @forelse($recentActivity as $activity)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-{{ $activity->action->icon() }} text-xs text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <p class="text-sm dark:text-white">
                            <span class="font-medium">{{ $activity->action->label() }}</span>
                            — {{ $activity->transaction?->tracking_number ?? '—' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    <i class="fas fa-history text-4xl mb-3"></i>
                    <p>لا يوجد نشاط حديث</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

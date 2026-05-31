@extends('layouts.app')

@section('title', 'تتبع معاملة')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-2xl font-bold dark:text-white">تتبع معاملة</h2>
        <p class="text-gray-500 dark:text-gray-400 mt-1">ابحث عن معاملة برقم التتبع</p>
    </div>

    <!-- Search Form -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <form action="{{ route('transactions.track') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <input type="text" name="tracking_number" value="{{ $trackingNumber }}" required
                       placeholder="أدخل رقم المعاملة (مثال: SSCS-IN-2026-000001)"
                       class="w-full pl-4 pr-12 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white font-mono">
                <i class="fas fa-barcode absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
            <button type="submit" class="px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                <i class="fas fa-search ml-2"></i> بحث
            </button>
        </form>
    </div>

    @if($transaction)
    <!-- Transaction Found -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded {{ $transaction->type->color() }} text-white">
                        {{ $transaction->type->label() }}
                    </span>
                    <h3 class="text-lg font-bold mt-2 dark:text-white">{{ $transaction->subject }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono mt-1">{{ $transaction->tracking_number }}</p>
                </div>
                <span class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium rounded-full {{ $transaction->status->color() }} text-white">
                    <i class="fas {{ $transaction->status->icon() }}"></i>
                    {{ $transaction->status->label() }}
                </span>
            </div>
        </div>

        <!-- Timeline -->
        <div class="p-6">
            <h4 class="font-bold mb-4 dark:text-white">سير العمل</h4>
            <div class="space-y-0">
                @foreach($transaction->workflowLogs as $index => $log)
                <div class="flex gap-4 relative {{ !$loop->last ? 'pb-8' : '' }}">
                    @if(!$loop->last)
                    <div class="absolute right-5 top-10 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
                    @endif
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0 z-10">
                        <i class="fas {{ $log->action->icon() }} text-primary-600 dark:text-primary-400 text-sm"></i>
                    </div>
                    <div class="flex-1 pt-1">
                        <p class="font-medium text-sm dark:text-white">{{ $log->action->label() }}</p>
                        @if($log->user)
                            <p class="text-xs text-gray-500">{{ $log->user->name }}</p>
                        @endif
                        <p class="text-xs text-gray-400 mt-1">
                            <i class="fas fa-clock ml-1"></i>{{ $log->created_at->format('Y-m-d H:i') }}
                        </p>
                        @if($log->notes)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 bg-gray-50 dark:bg-gray-700 p-2 rounded">{{ $log->notes }}</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @elseif($trackingNumber)
    <!-- Not Found -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-12 text-center">
        <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
        <h3 class="text-lg font-bold dark:text-white mb-2">لم يتم العثور على المعاملة</h3>
        <p class="text-gray-500 dark:text-gray-400">تأكد من رقم المعاملة وحاول مرة أخرى</p>
    </div>
    @endif
</div>
@endsection

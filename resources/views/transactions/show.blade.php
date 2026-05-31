@extends('layouts.app')

@section('title', 'عرض المعاملة')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <i class="fas fa-arrow-right"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 text-xs font-medium rounded {{ $transaction->type->color() }} text-white">
                        {{ $transaction->type->label() }}
                    </span>
                    <h2 class="text-xl font-bold dark:text-white">{{ $transaction->subject }}</h2>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $transaction->tracking_number }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('transactions.print', $transaction) }}" target="_blank"
               class="px-3 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg text-sm transition-colors">
                <i class="fas fa-print ml-1"></i> طباعة
            </a>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium rounded-full {{ $transaction->status->color() }} text-white">
                        <i class="fas {{ $transaction->status->icon() }}"></i>
                        {{ $transaction->status->label() }}
                    </span>
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($transaction->is_secret)
                            <span class="text-red-500"><i class="fas fa-lock"></i> سرية</span>
                        @endif
                        @if($transaction->is_urgent)
                            <span class="text-yellow-500"><i class="fas fa-exclamation-triangle"></i> مستعجلة</span>
                        @endif
                    </div>
                </div>

                <div class="prose dark:prose-invert max-w-none mt-4">
                    <p class="whitespace-pre-line dark:text-gray-300">{{ $transaction->content }}</p>
                </div>
            </div>

            <!-- Attachments -->
            @if($transaction->attachments->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-bold text-lg mb-4 dark:text-white">
                    <i class="fas fa-paperclip ml-2 text-primary-500"></i> المرفقات
                </h3>
                <div class="space-y-2">
                    @foreach($transaction->attachments as $attachment)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file text-gray-400"></i>
                            <div>
                                <p class="text-sm font-medium dark:text-white">{{ $attachment->original_name }}</p>
                                <p class="text-xs text-gray-500">{{ $attachment->getHumanReadableSize() }}</p>
                            </div>
                        </div>
                        <a href="{{ Storage::url($attachment->file_path) }}" download
                           class="text-primary-500 hover:text-primary-600 text-sm">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Workflow History -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-bold text-lg mb-4 dark:text-white">
                    <i class="fas fa-history ml-2 text-primary-500"></i> سير العمل
                </h3>
                <div class="space-y-4">
                    @forelse($history as $log)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas {{ $log->action->icon() }} text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <span class="font-medium text-sm dark:text-white">{{ $log->action->label() }}</span>
                                @if($log->user)
                                    <span class="text-xs text-gray-500">— {{ $log->user->name }}</span>
                                @endif
                            </div>
                            @if($log->notes)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $log->notes }}</p>
                            @endif
                            <div class="flex items-center gap-4 mt-1 text-xs text-gray-400">
                                <span><i class="fas fa-clock ml-1"></i>{{ $log->created_at->format('Y-m-d H:i') }}</span>
                                <span><i class="fas fa-globe ml-1"></i>{{ $log->ip_address }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">لا يوجد سجل</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-bold mb-4 dark:text-white">إجراءات سريعة</h3>
                <div class="space-y-2">
                    @if(in_array($transaction->status->value, ['new', 'review', 'transferred', 'pending']))
                    <form action="{{ route('transactions.approve', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-check-double"></i> اعتماد
                        </button>
                    </form>

                    <form action="{{ route('transactions.reject', $transaction) }}" method="POST">
                        @csrf
                        <div class="space-y-2">
                            <textarea name="reason" rows="2" placeholder="سبب الرفض..." required
                                      class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white resize-none"></textarea>
                            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors">
                                <i class="fas fa-times"></i> رفض
                            </button>
                        </div>
                    </form>
                    @endif

                    @if(!in_array($transaction->status->value, ['closed']))
                    <form action="{{ route('transactions.close', $transaction) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-lock"></i> إغلاق
                        </button>
                    </form>
                    @endif

                    <!-- Transfer -->
                    <form action="{{ route('transactions.transfer', $transaction) }}" method="POST" class="pt-2 border-t border-gray-200 dark:border-gray-700">
                        @csrf
                        <p class="text-sm font-medium mb-2 dark:text-white">تحويل المعاملة</p>
                        <select name="to_department_id" required
                                class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white mb-2">
                            <option value="">اختر القسم</option>
                            @foreach(\App\Models\Department::where('is_active', true)->get() as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                        <textarea name="notes" rows="2" placeholder="ملاحظات التحويل..."
                                  class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm dark:text-white resize-none mb-2"></textarea>
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-share"></i> تحويل
                        </button>
                    </form>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-bold mb-4 dark:text-white">التفاصيل</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">القسم المرسل</span>
                        <span class="font-medium dark:text-white">{{ $transaction->fromDepartment?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">المرسل</span>
                        <span class="font-medium dark:text-white">{{ $transaction->fromUser?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">القسم المستلم</span>
                        <span class="font-medium dark:text-white">{{ $transaction->toDepartment?->name ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">المستلم</span>
                        <span class="font-medium dark:text-white">{{ $transaction->toUser?->name ?? '—' }}</span>
                    </div>
                    @if($transaction->due_date)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">تاريخ الاستحقاق</span>
                        <span class="font-medium dark:text-white">{{ $transaction->due_date->format('Y-m-d') }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                        <span class="font-medium dark:text-white">{{ $transaction->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($transaction->approved_at)
                    <div class="flex justify-between">
                        <span class="text-gray-500 dark:text-gray-400">تاريخ الاعتماد</span>
                        <span class="font-medium dark:text-white">{{ $transaction->approved_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- AI Tools -->
            @if(app(\App\Services\AIService::class)->isEnabled())
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="font-bold mb-4 dark:text-white">
                    <i class="fas fa-robot ml-2 text-accent-500"></i> أدوات الذكاء الاصطناعي
                </h3>
                <div class="space-y-2">
                    <button onclick="loadAI('summarize')" class="w-full flex items-center gap-2 px-3 py-2 bg-accent-50 hover:bg-accent-100 dark:bg-accent-900/20 dark:hover:bg-accent-900/30 text-accent-700 dark:text-accent-300 rounded-lg text-sm transition-colors">
                        <i class="fas fa-compress"></i> تلخيص المعاملة
                    </button>
                    <button onclick="loadAI('suggest')" class="w-full flex items-center gap-2 px-3 py-2 bg-accent-50 hover:bg-accent-100 dark:bg-accent-900/20 dark:hover:bg-accent-900/30 text-accent-700 dark:text-accent-300 rounded-lg text-sm transition-colors">
                        <i class="fas fa-comment-dots"></i> اقتراح رد
                    </button>
                </div>
                <div id="ai-result" class="mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm dark:text-white hidden"></div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
async function loadAI(action) {
    const resultDiv = document.getElementById('ai-result');
    resultDiv.classList.remove('hidden');
    resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري المعالجة...';
    
    try {
        const response = await fetch(`/ai/${action}/{{ $transaction->id }}`);
        const data = await response.json();
        resultDiv.innerHTML = data.summary || data.response || 'لا يوجد نتيجة';
    } catch (error) {
        resultDiv.innerHTML = '<span class="text-red-500">حدث خطأ</span>';
    }
}
</script>
@endsection

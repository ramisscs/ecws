@extends('layouts.app')

@section('title', 'إعداد المصادقة الثنائية')

@section('content')
<div class="max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6 dark:text-white">إعداد المصادقة الثنائية</h2>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 text-center">
        <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-qrcode text-2xl text-green-600 dark:text-green-400"></i>
        </div>

        <p class="text-gray-600 dark:text-gray-400 mb-6">
            امسح رمز QR باستخدام تطبيق Google Authenticator أو Authy
        </p>

        <div class="inline-block p-4 bg-white rounded-lg shadow mb-6">
            {!! $qrCodeUrl !!}
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">أو أدخل المفتاح يدوياً:</p>
            <code class="text-sm font-mono bg-white dark:bg-gray-600 px-3 py-2 rounded block">{{ $secret }}</code>
        </div>

        <form action="{{ route('2fa.setup') }}" method="POST" class="max-w-xs mx-auto">
            @csrf
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                أدخل رمز التحقق للتأكيد
            </label>
            <input type="text" name="otp" required maxlength="6"
                   class="w-full text-center text-xl tracking-[0.5em] px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white mb-4"
                   placeholder="000000" inputmode="numeric">
            <button type="submit" class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-check ml-2"></i> تفعيل
            </button>
        </form>
    </div>
</div>
@endsection

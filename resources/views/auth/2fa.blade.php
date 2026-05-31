<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التحقق الثنائي | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 text-center">
            <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-2xl text-primary-600 dark:text-primary-400"></i>
            </div>
            <h2 class="text-xl font-bold dark:text-white mb-2">التحقق الثنائي</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">أدخل رمز التحقق من تطبيق المصادقة</p>

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('2fa.verify') }}">
                @csrf
                <input type="text" name="otp" required
                       class="w-full text-center text-2xl tracking-[0.5em] px-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white mb-4"
                       placeholder="000000" maxlength="6" inputmode="numeric" autocomplete="one-time-code">
                
                <button type="submit" class="w-full py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors">
                    تحقق
                </button>
            </form>
        </div>
    </div>
</body>
</html>

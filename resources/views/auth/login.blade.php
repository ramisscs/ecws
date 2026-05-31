<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-envelope-open-text text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">ECWS</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">نظام المراسلات الإلكترونية</p>
            <p class="text-sm text-gray-400 dark:text-gray-500">جمعية صباح السالم التعاونية</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <h2 class="text-xl font-bold text-center mb-6 dark:text-white">تسجيل الدخول</h2>

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- الرقم الوظيفي -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        الرقم الوظيفي <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="employee_id" value="{{ old('employee_id') }}" required autofocus
                               class="w-full pr-10 pl-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white font-mono text-lg"
                               placeholder="مثال: 2048">
                        <i class="fas fa-id-badge absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" required
                               class="w-full pr-10 pl-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white"
                               placeholder="كلمة المرور الافتراضية">
                        <i class="fas fa-lock absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                        <span class="text-sm text-gray-600 dark:text-gray-400">تذكرني</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-primary-500 hover:bg-primary-600 text-white font-medium rounded-lg transition-colors focus:ring-4 focus:ring-primary-500/20">
                    <i class="fas fa-sign-in-alt ml-2"></i>
                    دخول
                </button>
            </form>
        </div>

        <!-- ملاحظة -->
        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-sm">
            <p class="font-medium text-blue-800 dark:text-blue-300 mb-2">
                <i class="fas fa-info-circle ml-1"></i>
                تعليمات:
            </p>
            <ul class="space-y-1 text-blue-700 dark:text-blue-400 text-xs">
                <li>سجّل الدخول بالرقم الوظيفي الممنوح لك من الإدارة</li>
                <li>عند أول دخول، سُطلب منك تغيير كلمة المرور الافتراضية</li>
                <li>كلمة المرور الافتراضية: <code class="bg-white dark:bg-gray-800 px-1 rounded">ECWS@2026</code></li>
            </ul>
        </div>
    </div>

    <script>
        // تركيز تلقائي على حقل الرقم الوظيفي
        document.querySelector('input[name="employee_id"]').focus();
    </script>
</body>
</html>

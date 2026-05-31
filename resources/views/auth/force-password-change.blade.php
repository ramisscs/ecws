<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة المرور | {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-sans bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-shield-alt text-2xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">أول دخول — تغيير كلمة المرور</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">لأمان حسابك، يجب تغيير كلمة المرور الافتراضية</p>
        </div>

        <!-- Change Password Card -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <!-- User Info -->
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center text-white font-bold">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-primary-900 dark:text-white">{{ $user->name }}</h3>
                        <p class="text-sm text-primary-700 dark:text-primary-300 font-mono">رقم وظيفي: {{ $user->employee_id }}</p>
                        <p class="text-xs text-primary-600 dark:text-primary-400">{{ $user->job_title ?? $user->role->label() }}</p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg text-sm">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.force-change') }}" class="space-y-5">
                @csrf

                <!-- كلمة المرور الافتراضية الحالية (للتأكد فقط) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        كلمة المرور الحالية (الافتراضية)
                    </label>
                    <input type="password" name="old_password_check" value="ECWS@2026" disabled
                           class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-500 dark:text-gray-400 line-through">
                    <input type="hidden" name="old_password_check" value="ECWS@2026">
                </div>

                <!-- كلمة المرور الجديدة -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        كلمة المرور الجديدة <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password" required minlength="8"
                               class="w-full pr-10 pl-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white"
                               placeholder="أدخل كلمة مرور جديدة (8 أحرف على الأقل)">
                        <button type="button" onclick="togglePassword('new_password')"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="eye-new_password"></i>
                        </button>
                    </div>
                    @error('new_password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <!-- قوة كلمة المرور -->
                    <div class="mt-2" id="password-strength" style="display: none;">
                        <div class="h-1.5 bg-gray-200 rounded-full overflow-hidden">
                            <div id="strength-bar" class="h-full transition-all duration-300 w-0"></div>
                        </div>
                        <p class="text-xs mt-1 text-gray-500" id="strength-text"></p>
                    </div>
                </div>

                <!-- تأكيد كلمة المرور -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        تأكيد كلمة المرور الجديدة <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" required minlength="8"
                               class="w-full pr-10 pl-4 py-3 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent dark:text-white"
                               placeholder="أعد إدخال كلمة المرور الجديدة">
                        <button type="button" onclick="togglePassword('new_password_confirmation')"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-eye" id="eye-new_password_confirmation"></i>
                        </button>
                    </div>
                    @error('new_password_confirmation')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <!-- تطابق كلمة المرور -->
                    <div class="mt-1" id="match-indicator" style="display: none;">
                        <p class="text-xs" id="match-text"></p>
                    </div>
                </div>

                <!-- متطلبات كلمة المرور -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 text-sm">
                    <h4 class="font-medium mb-2 dark:text-white">متطلبات كلمة المرور:</h4>
                    <ul class="space-y-1 text-gray-600 dark:text-gray-400">
                        <li id="req-length"><i class="fas fa-circle text-xs ml-2 text-gray-300"></i> 8 أحرف على الأقل</li>
                        <li id="req-upper"><i class="fas fa-circle text-xs ml-2 text-gray-300"></i> حرف كبير (A-Z)</li>
                        <li id="req-lower"><i class="fas fa-circle text-xs ml-2 text-gray-300"></i> حرف صغير (a-z)</li>
                        <li id="req-number"><i class="fas fa-circle text-xs ml-2 text-gray-300"></i> رقم (0-9)</li>
                        <li id="req-special"><i class="fas fa-circle text-xs ml-2 text-gray-300"></i> رمز خاص (!@#$%...)</li>
                    </ul>
                </div>

                <button type="submit" 
                        class="w-full py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors focus:ring-4 focus:ring-green-500/20">
                    <i class="fas fa-check-circle ml-2"></i>
                    تأكيد وتغيير كلمة المرور
                </button>
            </form>
        </div>
    </div>

    <script>
        // إظهار/إخفاء كلمة المرور
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById('eye-' + inputId);
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }

        // فحص قوة كلمة المرور
        const passwordInput = document.getElementById('new_password');
        const confirmInput = document.getElementById('new_password_confirmation');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        const strengthDiv = document.getElementById('password-strength');

        passwordInput.addEventListener('input', function() {
            const val = this.value;
            strengthDiv.style.display = val.length > 0 ? 'block' : 'none';

            let score = 0;
            if (val.length >= 8) score++;
            if (/[A-Z]/.test(val)) score++;
            if (/[a-z]/.test(val)) score++;
            if (/[0-9]/.test(val)) score++;
            if (/[^A-Za-z0-9]/.test(val)) score++;

            // تحديث المتطلبات
            document.getElementById('req-length').innerHTML = '<i class="fas fa-circle text-xs ml-2 ' + (val.length >= 8 ? 'text-green-500' : 'text-gray-300') + '"></i> 8 أحرف على الأقل';
            document.getElementById('req-upper').innerHTML = '<i class="fas fa-circle text-xs ml-2 ' + (/[A-Z]/.test(val) ? 'text-green-500' : 'text-gray-300') + '"></i> حرف كبير (A-Z)';
            document.getElementById('req-lower').innerHTML = '<i class="fas fa-circle text-xs ml-2 ' + (/[a-z]/.test(val) ? 'text-green-500' : 'text-gray-300') + '"></i> حرف صغير (a-z)';
            document.getElementById('req-number').innerHTML = '<i class="fas fa-circle text-xs ml-2 ' + (/[0-9]/.test(val) ? 'text-green-500' : 'text-gray-300') + '"></i> رقم (0-9)';
            document.getElementById('req-special').innerHTML = '<i class="fas fa-circle text-xs ml-2 ' + (/[^A-Za-z0-9]/.test(val) ? 'text-green-500' : 'text-gray-300') + '"></i> رمز خاص (!@#$%...)';

            // تحديث شريط القوة
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-400', 'bg-green-500'];
            const texts = ['ضعيفة جداً', 'ضعيفة', 'متوسطة', 'قوية', 'قوية جداً'];
            const widths = ['20%', '40%', '60%', '80%', '100%'];

            strengthBar.className = 'h-full transition-all duration-300 ' + colors[Math.min(score, 4)];
            strengthBar.style.width = widths[Math.min(score, 4)];
            strengthText.textContent = texts[Math.min(score, 4)];
            strengthText.className = 'text-xs mt-1 ' + (score >= 3 ? 'text-green-600' : score >= 2 ? 'text-yellow-600' : 'text-red-600');
        });

        // فحص تطابق كلمة المرور
        confirmInput.addEventListener('input', function() {
            const matchDiv = document.getElementById('match-indicator');
            const matchText = document.getElementById('match-text');

            if (this.value.length === 0) {
                matchDiv.style.display = 'none';
                return;
            }

            matchDiv.style.display = 'block';
            if (this.value === passwordInput.value) {
                matchText.innerHTML = '<i class="fas fa-check-circle text-green-500 ml-1"></i> كلمة المرور متطابقة';
                matchText.className = 'text-xs text-green-600';
            } else {
                matchText.innerHTML = '<i class="fas fa-times-circle text-red-500 ml-1"></i> كلمة المرور غير متطابقة';
                matchText.className = 'text-xs text-red-600';
            }
        });
    </script>
</body>
</html>

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AIController;

// ============= Guest Routes =============
Route::middleware('guest')->group(function () {
    // تسجيل الدخول
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // إجبار تغيير كلمة المرور (أول دخول)
    Route::get('/password/force-change', [AuthController::class, 'showForcePasswordChange'])
         ->name('password.force-change');
    Route::post('/password/force-change', [AuthController::class, 'forcePasswordChange']);

    // التحقق الثنائي
    Route::get('/2fa', [AuthController::class, 'show2FA'])->name('2fa.verify');
    Route::post('/2fa', [AuthController::class, 'verify2FA']);
});

// ============= Authenticated Routes =============
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // لوحة التحكم
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/executive', [DashboardController::class, 'executive'])->name('dashboard.executive');
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

    // الملف الشخصي
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // إعداد التحقق الثنائي
    Route::get('/2fa/setup', [AuthController::class, 'showSetup2FA'])->name('2fa.setup');
    Route::post('/2fa/setup', [AuthController::class, 'confirmSetup2FA']);

    // المعاملات
    Route::resource('transactions', TransactionController::class);
    Route::post('/transactions/{transaction}/transfer', [TransactionController::class, 'transfer'])->name('transactions.transfer');
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    Route::post('/transactions/{transaction}/close', [TransactionController::class, 'close'])->name('transactions.close');
    Route::get('/transactions/{transaction}/print', [TransactionController::class, 'print'])->name('transactions.print');
    Route::get('/track', [TransactionController::class, 'track'])->name('transactions.track');

    // خدمات الذكاء الاصطناعي
    Route::get('/ai/summarize/{transaction}', [AIController::class, 'summarize'])->name('ai.summarize');
    Route::get('/ai/suggest-response/{transaction}', [AIController::class, 'suggestResponse'])->name('ai.suggest');
    Route::post('/ai/rewrite', [AIController::class, 'rewrite'])->name('ai.rewrite');
    Route::get('/ai/extract-tasks', [AIController::class, 'extractTasks'])->name('ai.tasks');
    Route::get('/ai/priority/{transaction}', [AIController::class, 'suggestPriority'])->name('ai.priority');
});

// ============= Admin Only Routes =============
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // المستخدمين
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser']);
    Route::put('/users/{user}', [AdminController::class, 'updateUser']);
    Route::put('/users/{user}/toggle', [AdminController::class, 'toggleUser'])->name('users.toggle');
    Route::post('/users/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');

    // الأقسام
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminController::class, 'storeDepartment']);
    Route::put('/departments/{department}', [AdminController::class, 'updateDepartment']);

    // سجل التدقيق
    Route::get('/audit-logs', [AdminController::class, 'auditLogs'])->name('audit');

    // الإعدادات
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings']);

    // القوالب
    Route::get('/templates', [AdminController::class, 'templates'])->name('templates');
    Route::post('/templates', [AdminController::class, 'storeTemplate']);
    Route::put('/templates/{template}', [AdminController::class, 'updateTemplate']);

    // التقارير
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/backup', [AdminController::class, 'backup'])->name('backup');
});

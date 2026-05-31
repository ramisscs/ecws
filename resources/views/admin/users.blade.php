@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold dark:text-white">إدارة المستخدمين</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">إنشاء وإدارة حسابات الموظفين</p>
        </div>
        <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')" 
                class="px-4 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors shadow-sm">
            <i class="fas fa-user-plus ml-2"></i> موظف جديد
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <form action="{{ route('admin.users') }}" method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="بحث بالاسم..."
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
            </div>
            <div class="min-w-[150px]">
                <input type="text" name="employee_id" value="{{ request('employee_id') }}" placeholder="الرقم الوظيفي..."
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white font-mono">
            </div>
            <div class="min-w-[150px]">
                <select name="role" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    <option value="">كل الأدوار</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->value }}" {{ request('role') == $role->value ? 'selected' : '' }}>{{ $role->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="min-w-[150px]">
                <select name="department_id" class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                    <option value="">كل الأقسام</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg">
                <i class="fas fa-search"></i>
            </button>
            <a href="{{ route('admin.users') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg">
                إعادة
            </a>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-right font-medium">الرقم الوظيفي</th>
                        <th class="px-4 py-3 text-right font-medium">الاسم</th>
                        <th class="px-4 py-3 text-right font-medium">المسمى الوظيفي</th>
                        <th class="px-4 py-3 text-right font-medium">القسم</th>
                        <th class="px-4 py-3 text-right font-medium">الدور</th>
                        <th class="px-4 py-3 text-right font-medium">الحالة</th>
                        <th class="px-4 py-3 text-right font-medium">أول دخول</th>
                        <th class="px-4 py-3 text-right font-medium">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-mono font-medium text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 px-2 py-1 rounded">
                                {{ $user->employee_id }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-xs font-bold text-primary-600">
                                    {{ substr($user->name, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-medium dark:text-white">{{ $user->name }}</p>
                                    @if($user->civil_id)
                                        <p class="text-xs text-gray-400 font-mono">{{ $user->civil_id }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->job_title ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $user->department?->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full {{ $user->role->value === 'super_admin' ? 'bg-red-100 text-red-700' : ($user->role->value === 'admin' ? 'bg-orange-100 text-orange-700' : ($user->role->value === 'department_head' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700')) }}">
                                {{ $user->role->label() }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                <i class="fas fa-circle text-[8px]"></i>
                                {{ $user->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->password_changed)
                                <span class="text-green-500 text-xs"><i class="fas fa-check-circle"></i> تم</span>
                            @else
                                <span class="text-yellow-500 text-xs"><i class="fas fa-clock"></i> لم يدخل بعد</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <!-- Reset Password -->
                                <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من إعادة تعيين كلمة المرور؟')">
                                    @csrf
                                    <input type="hidden" name="new_password" value="ECWS@2026">
                                    <button type="submit" class="text-blue-500 hover:text-blue-600 text-sm" title="إعادة تعيين كلمة المرور">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                                <!-- Toggle -->
                                <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-sm {{ $user->is_active ? 'text-red-500 hover:text-red-600' : 'text-green-500 hover:text-green-600' }}"
                                            title="{{ $user->is_active ? 'تعطيل' : 'تفعيل' }}">
                                        <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                            <i class="fas fa-users text-4xl mb-3 block"></i>
                            لا يوجد مستخدمين
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- ============= Add User Modal ============= -->
<div id="add-user-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onclick="if(event.target===this)this.classList.add('hidden')">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="font-bold text-lg dark:text-white">
                <i class="fas fa-user-plus ml-2 text-primary-500"></i>
                إنشاء حساب موظف جديد
            </h3>
            <button onclick="document.getElementById('add-user-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="{{ route('admin.users') }}" method="POST" class="space-y-4">
            @csrf

            <!-- الرقم الوظيفي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    الرقم الوظيفي <span class="text-red-500">*</span>
                </label>
                <input type="text" name="employee_id" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white font-mono"
                       placeholder="مثال: 2048">
            </div>

            <!-- الاسم -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    الاسم الكامل <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" required
                       class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                       placeholder="الاسم رباعي">
            </div>

            <!-- الرقم المدني + المسمى الوظيفي -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">الرقم المدني</label>
                    <input type="text" name="civil_id" maxlength="12"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white font-mono"
                           placeholder="2880123XXXXX">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">المسمى الوظيفي</label>
                    <input type="text" name="job_title"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                           placeholder="مثال: مسؤول مالي">
                </div>
            </div>

            <!-- البريد + الهاتف -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">البريد الإلكتروني</label>
                    <input type="email" name="email"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                           placeholder="اختياري">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">رقم الهاتف</label>
                    <input type="text" name="phone"
                           class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white"
                           placeholder="مثال: 90000000">
                </div>
            </div>

            <!-- الدور + القسم -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        الدور الوظيفي <span class="text-red-500">*</span>
                    </label>
                    <select name="role" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                        <option value="">اختر الدور</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        القسم <span class="text-red-500">*</span>
                    </label>
                    <select name="department_id" required class="w-full px-3 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                        <option value="">اختر القسم</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- كلمة المرور الافتراضية -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    كلمة المرور الافتراضية <span class="text-red-500">*</span>
                </label>
                <input type="text" name="password" required value="ECWS@2026"
                       class="w-full px-3 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-700 rounded-lg dark:text-white font-mono"
                       placeholder="كلمة المرور الافتراضية">
                <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-1">
                    <i class="fas fa-exclamation-triangle"></i>
                    سيطلب من الموظف تغييرها عند أول دخول
                </p>
            </div>

            <!-- تفعيل -->
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" checked
                       class="w-4 h-4 rounded border-gray-300 text-primary-500 focus:ring-primary-500">
                <label class="text-sm text-gray-700 dark:text-gray-300">تفعيل الحساب مباشرة</label>
            </div>

            <!-- Submit -->
            <div class="flex justify-end gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="document.getElementById('add-user-modal').classList.add('hidden')" 
                        class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 rounded-lg transition-colors">
                    إلغاء
                </button>
                <button type="submit" class="px-6 py-2.5 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-check ml-2"></i> إنشاء الحساب
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

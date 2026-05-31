<!-- Mobile Sidebar Overlay -->
<div x-show="sidebarOpen" 
     @click="sidebarOpen = false"
     class="fixed inset-0 bg-black/50 z-40 lg:hidden"
     x-transition.opacity></div>

<!-- Sidebar -->
<aside :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full lg:translate-x-0'"
       class="fixed top-0 right-0 h-full w-64 bg-primary-500 dark:bg-gray-800 text-white z-50 transform transition-transform duration-200 lg:translate-x-0 flex flex-col">
    
    <!-- Logo -->
    <div class="p-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-accent-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-envelope-open-text text-lg"></i>
            </div>
            <div>
                <h1 class="font-bold text-lg">ECWS</h1>
                <p class="text-xs text-white/60">نظام المراسلات الإلكترونية</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <ul class="space-y-1 px-3">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span>لوحة التحكم</span>
                </a>
            </li>

            <!-- Transactions -->
            <li>
                <a href="{{ route('transactions.index') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('transactions*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-exchange-alt w-5 text-center"></i>
                    <span>المعاملات</span>
                </a>
            </li>

            <!-- New Transaction -->
            <li>
                <a href="{{ route('transactions.create') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('transactions.create') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-plus-circle w-5 text-center"></i>
                    <span>معاملة جديدة</span>
                </a>
            </li>

            <!-- Track -->
            <li>
                <a href="{{ route('transactions.track') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('transactions.track') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-search w-5 text-center"></i>
                    <span>تتبع معاملة</span>
                </a>
            </li>

            <li class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">التقارير</span>
            </li>

            <!-- Executive Dashboard -->
            @if(auth()->user()->isAdmin() || auth()->user()->isDepartmentHead())
            <li>
                <a href="{{ route('dashboard.executive') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard.executive') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-chart-line w-5 text-center"></i>
                    <span>لوحة التنفيذي</span>
                </a>
            </li>
            @endif

            <!-- Admin Section -->
            @if(auth()->user()->isAdmin())
            <li class="pt-4 pb-2">
                <span class="px-4 text-xs font-semibold text-white/40 uppercase tracking-wider">الإدارة</span>
            </li>

            <li>
                <a href="{{ route('dashboard.admin') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('dashboard.admin') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-shield-alt w-5 text-center"></i>
                    <span>لوحة المدير</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.users') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.users*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-users w-5 text-center"></i>
                    <span>المستخدمين</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.departments') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.departments*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-building w-5 text-center"></i>
                    <span>الأقسام</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.templates') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.templates*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-file-alt w-5 text-center"></i>
                    <span>القوالب</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.audit') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.audit*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-history w-5 text-center"></i>
                    <span>سجل التتبع</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.settings') }}"
                   class="flex items-center gap-3 px-4 py-2.5 rounded-lg hover:bg-white/10 transition-colors {{ request()->routeIs('admin.settings*') ? 'bg-white/15' : '' }}">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span>الإعدادات</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-white/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-9 h-9 bg-accent-500 rounded-full flex items-center justify-center text-sm font-bold">
                {{ substr(auth()->user()->name, 0, 2) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-white/50 font-mono">#{{ auth()->user()->employee_id }}</p>
                <p class="text-xs text-white/40">{{ auth()->user()->role->label() }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('profile') }}" class="flex-1 text-center py-1.5 text-xs bg-white/10 rounded hover:bg-white/20 transition-colors">
                <i class="fas fa-user mr-1"></i> الملف
            </a>
            <form method="POST" action="{{ route('logout') }}" class="flex-1">
                @csrf
                <button type="submit" class="w-full py-1.5 text-xs bg-red-500/20 text-red-300 rounded hover:bg-red-500/30 transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i> خروج
                </button>
            </form>
        </div>
    </div>
</aside>

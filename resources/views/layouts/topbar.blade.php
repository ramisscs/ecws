<header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 sticky top-0 z-30">
    <div class="flex items-center justify-between px-6 py-3">
        <div class="flex items-center gap-4">
            <!-- Mobile Menu Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Search -->
            <form action="{{ route('transactions.index') }}" method="GET" class="hidden md:flex items-center">
                <div class="relative">
                    <input type="text" name="tracking_number" 
                           placeholder="بحث برقم المعاملة أو الموضوع..."
                           class="w-72 pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-700 border-0 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 dark:text-white">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <!-- Dark Mode Toggle -->
            <button @click="toggleDark()" 
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :title="darkMode ? 'وضع النهار' : 'وضع الليل'">
                <i class="fas" :class="darkMode ? 'fa-sun text-yellow-400' : 'fa-moon text-gray-500'"></i>
            </button>

            <!-- Notifications -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <div x-show="open" @click.away="open = false"
                     class="absolute left-0 top-full mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50">
                    <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="font-semibold text-sm">الإشعارات</h3>
                    </div>
                    <div class="px-4 py-3 text-center text-sm text-gray-500">
                        لا توجد إشعارات جديدة
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <a href="{{ route('transactions.create') }}" 
               class="hidden sm:flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus"></i>
                <span>معاملة جديدة</span>
            </a>
        </div>
    </div>
</header>

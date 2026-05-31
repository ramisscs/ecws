<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\WorkflowService;
use App\Repositories\TransactionRepository;
use App\Models\Transaction;
use App\Enums\TransactionStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        private TransactionService $transactionService,
        private WorkflowService $workflowService,
        private TransactionRepository $transactionRepository,
    ) {}

    public function index()
    {
        $user = Auth::user();

        $stats = [
            'new_transactions' => Transaction::visibleTo($user)
                ->where('status', TransactionStatus::NEW)
                ->count(),
            'pending_approval' => Transaction::visibleTo($user)
                ->where('status', TransactionStatus::PENDING_APPROVAL)
                ->count(),
            'transferred_to_me' => Transaction::visibleTo($user)
                ->where('to_user_id', $user->id)
                ->where('status', TransactionStatus::TRANSFERRED)
                ->count(),
            'total_approved' => Transaction::visibleTo($user)
                ->where('status', TransactionStatus::APPROVED)
                ->count(),
            'total_rejected' => Transaction::visibleTo($user)
                ->where('status', TransactionStatus::REJECTED)
                ->count(),
            'closed_this_month' => Transaction::visibleTo($user)
                ->where('status', TransactionStatus::CLOSED)
                ->whereMonth('closed_at', now()->month)
                ->count(),
        ];

        $recentTransactions = Transaction::visibleTo($user)
            ->with(['fromDepartment', 'toDepartment', 'fromUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recentActivity = $this->workflowService->getUserActivity($user->id, 10);

        return view('dashboard.index', compact('stats', 'recentTransactions', 'recentActivity'));
    }

    public function executive()
    {
        $user = Auth::user();

        if (!$user->isAdmin() && !$user->isDepartmentHead()) {
            abort(403);
        }

        $departmentStats = $this->transactionRepository->getDepartmentStats(
            $user->department_id ?? 0
        );

        $monthlyStats = Transaction::selectRaw('
            MONTH(created_at) as month,
            COUNT(*) as total,
            SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected
        ')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $workflowStats = $this->workflowService->getStatistics(30);

        return view('dashboard.executive', compact('departmentStats', 'monthlyStats', 'workflowStats'));
    }

    public function admin()
    {
        $user = Auth::user();

        if (!$user->isAdmin()) {
            abort(403);
        }

        $stats = [
            'total_transactions' => Transaction::count(),
            'active_users' => \App\Models\User::where('is_active', true)->count(),
            'departments' => \App\Models\Department::where('is_active', true)->count(),
            'pending_approvals' => Transaction::where('status', TransactionStatus::PENDING_APPROVAL)->count(),
            'monthly_transactions' => Transaction::whereMonth('created_at', now()->month)->count(),
            'secret_transactions' => $user->isSuperAdmin() ? Transaction::where('is_secret', true)->count() : 0,
        ];

        $systemHealth = [
            'database_size' => $this->getDatabaseSize(),
            'storage_used' => $this->getStorageUsage(),
            'last_backup' => now()->subDays(1)->format('Y-m-d H:i'),
        ];

        return view('dashboard.admin', compact('stats', 'systemHealth'));
    }

    private function getDatabaseSize(): string
    {
        try {
            $result = \DB::select("SELECT 
                table_schema AS db,
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = ?", [config('database.connections.mysql.database')]);
            return ($result[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            return 'غير متوفر';
        }
    }

    private function getStorageUsage(): string
    {
        try {
            $size = 0;
            $path = storage_path('app');
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
                $size += $file->getSize();
            }
            return round($size / 1024 / 1024, 2) . ' MB';
        } catch (\Exception $e) {
            return 'غير متوفر';
        }
    }
}

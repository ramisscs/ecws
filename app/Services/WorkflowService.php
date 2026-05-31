<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\WorkflowLog;
use App\Enums\WorkflowAction;
use Illuminate\Support\Facades\Request;

class WorkflowService
{
    public function logAction(
        Transaction $transaction,
        int $userId,
        WorkflowAction $action,
        ?string $fromStatus = null,
        ?string $toStatus = null,
        ?string $notes = null
    ): WorkflowLog {
        return WorkflowLog::create([
            'transaction_id' => $transaction->id,
            'user_id' => $userId,
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'metadata' => [
                'url' => Request::url(),
                'method' => Request::method(),
            ],
        ]);
    }

    public function getTransactionHistory(Transaction $transaction)
    {
        return $transaction->workflowLogs()
            ->with('user:id,name')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUserActivity(int $userId, int $limit = 50)
    {
        return WorkflowLog::with(['transaction:id,tracking_number,subject', 'user:id,name'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getDepartmentActivity(int $departmentId, int $limit = 50)
    {
        return WorkflowLog::with(['transaction:id,tracking_number,subject', 'user:id,name'])
            ->whereHas('transaction', function ($q) use ($departmentId) {
                $q->where('from_department_id', $departmentId)
                  ->orWhere('to_department_id', $departmentId);
            })
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getStatistics(int $days = 30): array
    {
        $fromDate = now()->subDays($days);

        return [
            'total_actions' => WorkflowLog::where('created_at', '>=', $fromDate)->count(),
            'by_action' => WorkflowLog::where('created_at', '>=', $fromDate)
                ->selectRaw('action, count(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_user' => WorkflowLog::where('created_at', '>=', $fromDate)
                ->selectRaw('user_id, count(*) as count')
                ->groupBy('user_id')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];
    }
}

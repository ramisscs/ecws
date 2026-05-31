<?php

namespace App\Repositories;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository
{
    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function find(int $id): ?Transaction
    {
        return Transaction::with(['fromDepartment', 'toDepartment', 'fromUser', 'toUser', 'attachments'])
            ->findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return Transaction::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return Transaction::where('id', $id)->delete();
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Transaction::with(['fromDepartment', 'toDepartment', 'fromUser', 'toUser']);

        if (!empty($filters['tracking_number'])) {
            $query->where('tracking_number', 'LIKE', '%' . $filters['tracking_number'] . '%');
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subject'])) {
            $query->where('subject', 'LIKE', '%' . $filters['subject'] . '%');
        }

        if (!empty($filters['department_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('from_department_id', $filters['department_id'])
                  ->orWhere('to_department_id', $filters['department_id']);
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('from_user_id', $filters['user_id'])
                  ->orWhere('to_user_id', $filters['user_id']);
            });
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['is_secret'])) {
            $query->where('is_secret', $filters['is_secret']);
        }

        if (isset($filters['is_urgent'])) {
            $query->where('is_urgent', $filters['is_urgent']);
        }

        $sortField = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';

        return $query->orderBy($sortField, $sortOrder)
            ->paginate($filters['per_page'] ?? 20);
    }

    public function getByStatus(string $status)
    {
        return Transaction::where('status', $status)
            ->with(['fromDepartment', 'toDepartment'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDepartmentStats(int $departmentId): array
    {
        return [
            'total' => Transaction::byDepartment($departmentId)->count(),
            'incoming' => Transaction::where('to_department_id', $departmentId)->where('type', 'IN')->count(),
            'outgoing' => Transaction::where('from_department_id', $departmentId)->where('type', 'OUT')->count(),
            'pending' => Transaction::byDepartment($departmentId)->where('status', 'pending')->count(),
            'approved' => Transaction::byDepartment($departmentId)->where('status', 'approved')->count(),
        ];
    }
}

<?php

namespace App\Repositories;

use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function find(int $id): ?User
    {
        return User::with('department')->findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function getByDepartment(int $departmentId)
    {
        return User::where('department_id', $departmentId)
            ->where('is_active', true)
            ->get();
    }

    public function getActiveUsers()
    {
        return User::where('is_active', true)
            ->with('department')
            ->orderBy('name')
            ->get();
    }

    public function getByRole(UserRole $role)
    {
        return User::where('role', $role)
            ->where('is_active', true)
            ->get();
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = User::with('department');

        if (!empty($filters['name'])) {
            $query->where('name', 'LIKE', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['employee_id'])) {
            $query->where('employee_id', 'LIKE', '%' . $filters['employee_id'] . '%');
        }

        if (!empty($filters['email'])) {
            $query->where('email', 'LIKE', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['role'])) {
            $query->where('role', $filters['role']);
        }

        if (!empty($filters['department_id'])) {
            $query->where('department_id', $filters['department_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('employee_id')->paginate($filters['per_page'] ?? 20);
    }

    public function getStats(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('is_active', true)->count(),
            'inactive' => User::where('is_active', false)->count(),
            'by_role' => User::selectRaw('role, count(*) as count')
                ->groupBy('role')
                ->pluck('count', 'role')
                ->toArray(),
        ];
    }
}

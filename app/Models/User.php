<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Enums\UserRole;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_id',
        'name',
        'email',
        'phone',
        'civil_id',
        'job_title',
        'password',
        'role',
        'department_id',
        'is_active',
        'password_changed',
        'first_login_at',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'password_changed' => 'boolean',
            'is_active' => 'boolean',
            'first_login_at' => 'datetime',
            'last_login_at' => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
            'role' => UserRole::class,
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'from_user_id');
    }

    public function receivedTransactions()
    {
        return $this->hasMany(Transaction::class, 'to_user_id');
    }

    public function workflowLogs()
    {
        return $this->hasMany(WorkflowLog::class);
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    public function isDepartmentHead(): bool
    {
        return $this->role === UserRole::DEPARTMENT_HEAD;
    }

    public function canViewSecrets(): bool
    {
        return in_array($this->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN]);
    }

    // هل هذا أول دخول للمستخدم؟
    public function isFirstLogin(): bool
    {
        return is_null($this->first_login_at) || !$this->password_changed;
    }

    // اسم المستخدم للعرض
    public function getDisplayNameAttribute(): string
    {
        return $this->name . ' (' . $this->employee_id . ')';
    }
}

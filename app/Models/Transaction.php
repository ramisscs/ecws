<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;

class Transaction extends Model
{
    protected $fillable = [
        'tracking_number',
        'type',
        'status',
        'subject',
        'content',
        'from_department_id',
        'to_department_id',
        'from_user_id',
        'to_user_id',
        'is_secret',
        'is_urgent',
        'parent_id',
        'template_id',
        'due_date',
        'approved_at',
        'approved_by',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'status' => TransactionStatus::class,
        'is_secret' => 'boolean',
        'is_urgent' => 'boolean',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function fromDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'from_department_id');
    }

    public function toDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'to_department_id');
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function workflowLogs(): HasMany
    {
        return $this->hasMany(WorkflowLog::class)->orderBy('created_at');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function scopeVisibleTo($query, User $user)
    {
        if ($user->canViewSecrets()) {
            return $query;
        }
        return $query->where('is_secret', false);
    }

    public function scopeByDepartment($query, int $departmentId)
    {
        return $query->where(function ($q) use ($departmentId) {
            $q->where('from_department_id', $departmentId)
              ->orWhere('to_department_id', $departmentId);
        });
    }
}

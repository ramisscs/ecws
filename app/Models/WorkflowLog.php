<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\WorkflowAction;

class WorkflowLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
        'ip_address',
        'user_agent',
        'metadata',
    ];

    protected $casts = [
        'action' => WorkflowAction::class,
        'from_status' => 'string',
        'to_status' => 'string',
        'metadata' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

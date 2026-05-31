<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TransactionType;

class Template extends Model
{
    protected $fillable = [
        'name',
        'type',
        'subject',
        'content',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'type' => TransactionType::class,
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

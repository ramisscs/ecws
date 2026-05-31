<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationSetting extends Model
{
    protected $fillable = [
        'user_id',
        'email_enabled',
        'whatsapp_enabled',
        'in_app_enabled',
        'notify_on_transfer',
        'notify_on_approval',
        'notify_on_rejection',
        'notify_on_close',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'in_app_enabled' => 'boolean',
        'notify_on_transfer' => 'boolean',
        'notify_on_approval' => 'boolean',
        'notify_on_rejection' => 'boolean',
        'notify_on_close' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

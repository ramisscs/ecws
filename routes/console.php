<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily cleanup of old audit logs
Artisan::command('audit:cleanup', function () {
    $days = config('ecws.audit.retention_days', 365);
    $deleted = \App\Models\AuditLog::where('created_at', '<', now()->subDays($days))->delete();
    $this->info("Deleted {$deleted} old audit logs.");
})->purpose('Clean up old audit logs');

// Daily backup
Artisan::command('ecws:backup', function () {
    $this->call('backup:run');
})->purpose('Run ECWS backup');

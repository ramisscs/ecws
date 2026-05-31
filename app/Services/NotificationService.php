<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notifyTransfer(Transaction $transaction, User $recipient): void
    {
        $settings = $this->getSettings($recipient);

        if ($settings?->notify_on_transfer) {
            if ($settings->in_app_enabled) {
                $this->sendInApp($recipient, 'تحويل معاملة', "تم تحويل معاملة {$transaction->tracking_number} إليك");
            }
            if ($settings->email_enabled) {
                $this->sendEmail($recipient, $transaction, 'transfer');
            }
            if ($settings->whatsapp_enabled) {
                $this->sendWhatsApp($recipient, $transaction, 'transfer');
            }
        }
    }

    public function notifyApproval(Transaction $transaction, User $recipient): void
    {
        $settings = $this->getSettings($recipient);

        if ($settings?->notify_on_approval) {
            if ($settings->in_app_enabled) {
                $this->sendInApp($recipient, 'اعتماد معاملة', "تم اعتماد معاملة {$transaction->tracking_number}");
            }
            if ($settings->email_enabled) {
                $this->sendEmail($recipient, $transaction, 'approval');
            }
        }
    }

    public function notifyRejection(Transaction $transaction, User $recipient, string $reason): void
    {
        $settings = $this->getSettings($recipient);

        if ($settings?->notify_on_rejection) {
            if ($settings->in_app_enabled) {
                $this->sendInApp($recipient, 'رفض معاملة', "تم رفض معاملة {$transaction->tracking_number}");
            }
            if ($settings->email_enabled) {
                $this->sendEmail($recipient, $transaction, 'rejection', ['reason' => $reason]);
            }
        }
    }

    private function getSettings(User $user): ?NotificationSetting
    {
        return NotificationSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_enabled' => true,
                'whatsapp_enabled' => false,
                'in_app_enabled' => true,
                'notify_on_transfer' => true,
                'notify_on_approval' => true,
                'notify_on_rejection' => true,
                'notify_on_close' => true,
            ]
        );
    }

    private function sendInApp(User $user, string $title, string $message): void
    {
        // Store in notifications table
        $user->notifications()->create([
            'title' => $title,
            'message' => $message,
            'read_at' => null,
        ]);
    }

    private function sendEmail(User $user, Transaction $transaction, string $type, array $data = []): void
    {
        try {
            Mail::to($user->email)->send(new \App\Mail\TransactionNotification($transaction, $type, $data));
        } catch (\Exception $e) {
            Log::error("Email notification failed: " . $e->getMessage());
        }
    }

    private function sendWhatsApp(User $user, Transaction $transaction, string $type): void
    {
        if (!config('ecws.whatsapp.enabled')) return;

        try {
            // Integration with WhatsApp Business API
            $message = $this->formatWhatsAppMessage($transaction, $type);
            // Implementation depends on WhatsApp provider (Twilio, etc.)
        } catch (\Exception $e) {
            Log::error("WhatsApp notification failed: " . $e->getMessage());
        }
    }

    private function formatWhatsAppMessage(Transaction $transaction, string $type): string
    {
        return match($type) {
            'transfer' => "📨 تم تحويل معاملة إليك\nرقم: {$transaction->tracking_number}\nالموضوع: {$transaction->subject}",
            'approval' => "✅ تم اعتماد المعاملة\nرقم: {$transaction->tracking_number}\nالموضوع: {$transaction->subject}",
            default => "📋 تحديث معاملة\nرقم: {$transaction->tracking_number}",
        };
    }
}

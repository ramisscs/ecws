<?php

namespace App\Mail;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Transaction $transaction,
        public string $type,
        public array $data = []
    ) {}

    public function build(): self
    {
        $subject = match($this->type) {
            'transfer' => 'تم تحويل معاملة إليك',
            'approval' => 'تم اعتماد المعاملة',
            'rejection' => 'تم رفض المعاملة',
            default => 'تحديث معاملة',
        };

        return $this->subject($subject)
            ->view('emails.transaction')
            ->with([
                'transaction' => $this->transaction,
                'type' => $this->type,
                'data' => $this->data,
            ]);
    }
}

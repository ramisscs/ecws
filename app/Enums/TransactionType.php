<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOMING = 'IN';
    case OUTGOING = 'OUT';
    case INTERNAL = 'INT';
    case CIRCULAR = 'CIR';
    case MEMO = 'MEM';
    case DECISION = 'DEC';

    public function label(): string
    {
        return match($this) {
            self::INCOMING => 'وارد',
            self::OUTGOING => 'صادر',
            self::INTERNAL => 'داخلي',
            self::CIRCULAR => 'تعميم',
            self::MEMO => 'مذكرة',
            self::DECISION => 'قرار إداري',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::INCOMING => 'bg-blue-500',
            self::OUTGOING => 'bg-green-500',
            self::INTERNAL => 'bg-purple-500',
            self::CIRCULAR => 'bg-orange-500',
            self::MEMO => 'bg-amber-600',
            self::DECISION => 'bg-red-600',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::INCOMING => 'fa-arrow-left',
            self::OUTGOING => 'fa-arrow-right',
            self::INTERNAL => 'fa-rotate',
            self::CIRCULAR => 'fa-bullhorn',
            self::MEMO => 'fa-file-lines',
            self::DECISION => 'fa-gavel',
        };
    }
}

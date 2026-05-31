<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case NEW = 'new';
    case UNDER_REVIEW = 'review';
    case TRANSFERRED = 'transferred';
    case PENDING_APPROVAL = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match($this) {
            self::NEW => 'جديدة',
            self::UNDER_REVIEW => 'قيد المراجعة',
            self::TRANSFERRED => 'محولة',
            self::PENDING_APPROVAL => 'بانتظار الاعتماد',
            self::APPROVED => 'معتمدة',
            self::REJECTED => 'مرفوضة',
            self::CLOSED => 'مغلقة',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::NEW => 'bg-gray-500',
            self::UNDER_REVIEW => 'bg-blue-500',
            self::TRANSFERRED => 'bg-indigo-500',
            self::PENDING_APPROVAL => 'bg-yellow-500',
            self::APPROVED => 'bg-green-500',
            self::REJECTED => 'bg-red-500',
            self::CLOSED => 'bg-gray-400',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::NEW => 'fa-circle',
            self::UNDER_REVIEW => 'fa-eye',
            self::TRANSFERRED => 'fa-share',
            self::PENDING_APPROVAL => 'fa-clock',
            self::APPROVED => 'fa-check-double',
            self::REJECTED => 'fa-xmark',
            self::CLOSED => 'fa-lock',
        };
    }
}

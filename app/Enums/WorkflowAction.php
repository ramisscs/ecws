<?php

namespace App\Enums;

enum WorkflowAction: string
{
    case CREATE = 'create';
    case TRANSFER_EMPLOYEE = 'transfer_employee';
    case TRANSFER_DEPARTMENT = 'transfer_department';
    case REDIRECT = 'redirect';
    case RETURN = 'return';
    case SUSPEND = 'suspend';
    case APPROVE = 'approve';
    case REJECT = 'reject';
    case CLOSE = 'close';
    case REOPEN = 'reopen';
    case ADD_ATTACHMENT = 'add_attachment';
    case ADD_NOTE = 'add_note';

    public function label(): string
    {
        return match($this) {
            self::CREATE => 'إنشاء',
            self::TRANSFER_EMPLOYEE => 'تحويل لموظف',
            self::TRANSFER_DEPARTMENT => 'تحويل لقسم',
            self::REDIRECT => 'إعادة توجيه',
            self::RETURN => 'إرجاع',
            self::SUSPEND => 'تعليق',
            self::APPROVE => 'اعتماد',
            self::REJECT => 'رفض',
            self::CLOSE => 'إغلاق',
            self::REOPEN => 'إعادة فتح',
            self::ADD_ATTACHMENT => 'إضافة مرفق',
            self::ADD_NOTE => 'إضافة ملاحظة',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::CREATE => 'fa-plus',
            self::TRANSFER_EMPLOYEE => 'fa-user-arrow-left',
            self::TRANSFER_DEPARTMENT => 'fa-building',
            self::REDIRECT => 'fa-share',
            self::RETURN => 'fa-rotate-left',
            self::SUSPEND => 'fa-pause',
            self::APPROVE => 'fa-check-double',
            self::REJECT => 'fa-xmark',
            self::CLOSE => 'fa-lock',
            self::REOPEN => 'fa-lock-open',
            self::ADD_ATTACHMENT => 'fa-paperclip',
            self::ADD_NOTE => 'fa-note-sticky',
        };
    }
}

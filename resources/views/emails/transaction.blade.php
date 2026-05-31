<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; direction: rtl; padding: 20px; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        .header { background: #1B3A5C; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 15px; text-align: center; font-size: 12px; color: #666; }
        .badge { display: inline-block; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>نظام المراسلات الإلكترونية — ECWS</h2>
            <p>جمعية صباح السالم التعاونية</p>
        </div>
        <div class="content">
            <h3>{{ match($type) { 'transfer' => 'تم تحويل معاملة إليك', 'approval' => 'تم اعتماد المعاملة', 'rejection' => 'تم رفض المعاملة', default => 'تحديث معاملة' } }}</h3>
            
            <p><strong>رقم المعاملة:</strong> {{ $transaction->tracking_number }}</p>
            <p><strong>الموضوع:</strong> {{ $transaction->subject }}</p>
            <p><strong>الحالة:</strong> {{ $transaction->status->label() }}</p>
            
            @if($type === 'rejection' && !empty($data['reason']))
            <p><strong>سبب الرفض:</strong> {{ $data['reason'] }}</p>
            @endif

            <p style="margin-top: 20px;">
                <a href="{{ route('transactions.show', $transaction) }}" 
                   style="display: inline-block; padding: 10px 20px; background: #1B3A5C; color: white; text-decoration: none; border-radius: 5px;">
                    عرض المعاملة
                </a>
            </p>
        </div>
        <div class="footer">
            <p>هذا البريد مرسل تلقائياً من نظام ECWS</p>
        </div>
    </div>
</body>
</html>

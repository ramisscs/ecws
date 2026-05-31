<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طباعة {{ $transaction->tracking_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1B3A5C;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #1B3A5C;
            font-size: 24px;
            margin-bottom: 5px;
        }
        .header p {
            color: #666;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .badge-IN { background: #3B82F6; }
        .badge-OUT { background: #10B981; }
        .badge-INT { background: #8B5CF6; }
        .badge-CIR { background: #F59E0B; }
        .badge-MEM { background: #D97706; }
        .badge-DEC { background: #DC2626; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .info-item {
            padding: 10px;
            background: #F8FAFC;
            border-radius: 6px;
        }
        .info-item label {
            font-size: 11px;
            color: #64748B;
            display: block;
            margin-bottom: 3px;
        }
        .info-item value {
            font-weight: 600;
            color: #1E293B;
        }
        .content-box {
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            min-height: 200px;
        }
        .qr-section {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #CBD5E1;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 40px;
            right: 40px;
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 10px;
        }
        @media print {
            body { padding: 20px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>جمعية صباح السالم التعاونية</h1>
        <p>نظام المراسلات الإلكترونية — ECWS</p>
    </div>

    <div style="text-align: center; margin-bottom: 20px;">
        <span class="badge badge-{{ $transaction->type->value }}">{{ $transaction->type->label() }}</span>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <label>رقم المعاملة</label>
            <value>{{ $transaction->tracking_number }}</value>
        </div>
        <div class="info-item">
            <label>الحالة</label>
            <value>{{ $transaction->status->label() }}</value>
        </div>
        <div class="info-item">
            <label>القسم المرسل</label>
            <value>{{ $transaction->fromDepartment?->name ?? '—' }}</value>
        </div>
        <div class="info-item">
            <label>المرسل</label>
            <value>{{ $transaction->fromUser?->name ?? '—' }}</value>
        </div>
        <div class="info-item">
            <label>القسم المستلم</label>
            <value>{{ $transaction->toDepartment?->name ?? '—' }}</value>
        </div>
        <div class="info-item">
            <label>تاريخ الإنشاء</label>
            <value>{{ $transaction->created_at->format('Y-m-d H:i') }}</value>
        </div>
    </div>

    <div style="margin-bottom: 10px;">
        <label style="font-size: 11px; color: #64748B;">الموضوع</label>
        <h2 style="font-size: 18px; color: #1E293B;">{{ $transaction->subject }}</h2>
    </div>

    <div class="content-box">
        {!! nl2br(e($transaction->content)) !!}
    </div>

    @if($transaction->workflowLogs->count() > 0)
    <h3 style="font-size: 16px; margin-bottom: 15px; color: #1B3A5C;">سير العمل</h3>
    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
        <thead>
            <tr style="background: #F1F5F9;">
                <th style="padding: 8px; text-align: right; border: 1px solid #E2E8F0;">الإجراء</th>
                <th style="padding: 8px; text-align: right; border: 1px solid #E2E8F0;">المستخدم</th>
                <th style="padding: 8px; text-align: right; border: 1px solid #E2E8F0;">التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->workflowLogs as $log)
            <tr>
                <td style="padding: 8px; border: 1px solid #E2E8F0;">{{ $log->action->label() }}</td>
                <td style="padding: 8px; border: 1px solid #E2E8F0;">{{ $log->user?->name ?? '—' }}</td>
                <td style="padding: 8px; border: 1px solid #E2E8F0;">{{ $log->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <div class="qr-section">
        <p style="font-size: 12px; color: #64748B; margin-bottom: 10px;">
            امسح للتحقق من صحة المعاملة
        </p>
        <div style="display: inline-block; padding: 10px; border: 1px solid #E2E8F0; border-radius: 8px;">
            {!! QrCode::size(120)->generate(route('transactions.track', ['tracking_number' => $transaction->tracking_number])) !!}
        </div>
        <p style="font-size: 11px; color: #94A3B8; margin-top: 10px;">
            {{ $transaction->tracking_number }}
        </p>
    </div>

    <div class="footer">
        تم الطباعة بتاريخ {{ now()->format('Y-m-d H:i') }} — نظام المراسلات الإلكترونية ECWS
    </div>

    <div class="no-print" style="text-align: center; margin-top: 40px; padding: 20px;">
        <button onclick="window.print()" style="padding: 12px 30px; background: #1B3A5C; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 14px;">
            <i class="fas fa-print" style="margin-left: 8px;"></i> طباعة
        </button>
    </div>
</body>
</html>

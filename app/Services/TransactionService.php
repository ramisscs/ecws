<?php

namespace App\Services;

use App\Models\Transaction;
use App\Enums\TransactionType;
use App\Enums\TransactionStatus;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private TransactionRepository $repository
    ) {}

    public function generateTrackingNumber(TransactionType $type): string
    {
        $prefix = config('ecws.prefix', 'SSCS');
        $typeCode = $type->value;
        $year = now()->year;

        $lastSequence = Transaction::where('type', $type)
            ->whereYear('created_at', $year)
            ->lockForUpdate()
            ->count();

        $sequence = str_pad($lastSequence + 1, 6, '0', STR_PAD_LEFT);

        return "{$prefix}-{$typeCode}-{$year}-{$sequence}";
    }

    public function create(array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $type = TransactionType::from($data['type']);
            $trackingNumber = $this->generateTrackingNumber($type);

            $transaction = $this->repository->create([
                ...$data,
                'tracking_number' => $trackingNumber,
                'status' => TransactionStatus::NEW,
                'from_user_id' => $userId,
            ]);

            app(WorkflowService::class)->logAction(
                $transaction,
                $userId,
                \App\Enums\WorkflowAction::CREATE,
                null,
                TransactionStatus::NEW->value,
                'تم إنشاء المعاملة'
            );

            return $transaction;
        });
    }

    public function transfer(int $transactionId, array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($transactionId, $data, $userId) {
            $transaction = $this->repository->find($transactionId);
            $oldStatus = $transaction->status;

            $updateData = [
                'to_department_id' => $data['to_department_id'] ?? $transaction->to_department_id,
                'to_user_id' => $data['to_user_id'] ?? null,
                'status' => TransactionStatus::TRANSFERRED,
            ];

            $this->repository->update($transactionId, $updateData);
            $transaction->refresh();

            app(WorkflowService::class)->logAction(
                $transaction,
                $userId,
                $data['to_user_id']
                    ? \App\Enums\WorkflowAction::TRANSFER_EMPLOYEE
                    : \App\Enums\WorkflowAction::TRANSFER_DEPARTMENT,
                $oldStatus->value,
                TransactionStatus::TRANSFERRED->value,
                $data['notes'] ?? 'تم التحويل'
            );

            return $transaction;
        });
    }

    public function approve(int $transactionId, int $userId, ?string $notes = null): Transaction
    {
        return DB::transaction(function () use ($transactionId, $userId, $notes) {
            $transaction = $this->repository->find($transactionId);
            $oldStatus = $transaction->status;

            $this->repository->update($transactionId, [
                'status' => TransactionStatus::APPROVED,
                'approved_at' => now(),
                'approved_by' => $userId,
            ]);

            app(WorkflowService::class)->logAction(
                $transaction,
                $userId,
                \App\Enums\WorkflowAction::APPROVE,
                $oldStatus->value,
                TransactionStatus::APPROVED->value,
                $notes ?? 'تم الاعتماد'
            );

            return $transaction->refresh();
        });
    }

    public function reject(int $transactionId, int $userId, string $reason): Transaction
    {
        return DB::transaction(function () use ($transactionId, $userId, $reason) {
            $transaction = $this->repository->find($transactionId);
            $oldStatus = $transaction->status;

            $this->repository->update($transactionId, [
                'status' => TransactionStatus::REJECTED,
            ]);

            app(WorkflowService::class)->logAction(
                $transaction,
                $userId,
                \App\Enums\WorkflowAction::REJECT,
                $oldStatus->value,
                TransactionStatus::REJECTED->value,
                $reason
            );

            return $transaction->refresh();
        });
    }

    public function close(int $transactionId, int $userId, ?string $notes = null): Transaction
    {
        return DB::transaction(function () use ($transactionId, $userId, $notes) {
            $transaction = $this->repository->find($transactionId);
            $oldStatus = $transaction->status;

            $this->repository->update($transactionId, [
                'status' => TransactionStatus::CLOSED,
                'closed_at' => now(),
                'closed_by' => $userId,
            ]);

            app(WorkflowService::class)->logAction(
                $transaction,
                $userId,
                \App\Enums\WorkflowAction::CLOSE,
                $oldStatus->value,
                TransactionStatus::CLOSED->value,
                $notes ?? 'تم الإغلاق'
            );

            return $transaction->refresh();
        });
    }

    public function search(array $filters)
    {
        return $this->repository->search($filters);
    }
}

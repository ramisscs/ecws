<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number')->unique();
            $table->enum('type', ['IN', 'OUT', 'INT', 'CIR', 'MEM', 'DEC']);
            $table->enum('status', ['new', 'review', 'transferred', 'pending', 'approved', 'rejected', 'closed'])
                ->default('new');
            $table->string('subject');
            $table->longText('content');
            $table->foreignId('from_department_id')->constrained('departments');
            $table->foreignId('to_department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('from_user_id')->constrained('users');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_secret')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->foreignId('parent_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('templates')->nullOnDelete();
            $table->date('due_date')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'type']);
            $table->index(['from_department_id', 'to_department_id']);
            $table->index('tracking_number');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

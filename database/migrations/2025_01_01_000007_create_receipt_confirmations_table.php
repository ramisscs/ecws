<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('device_info')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->unique(['transaction_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_confirmations');
    }
};

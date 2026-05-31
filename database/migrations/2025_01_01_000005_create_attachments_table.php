<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('file_type', 100);
            $table->unsignedBigInteger('file_size');
            $table->boolean('is_encrypted')->default(false);
            $table->string('checksum', 64)->nullable();
            $table->timestamps();

            $table->index('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('employee_id', 20)->unique();  // الرقم الوظيفي - تسجيل الدخول به
            $table->string('name');
            $table->string('email')->nullable()->unique(); // اختياري لاحقاً
            $table->string('phone')->nullable();
            $table->string('civil_id', 20)->nullable()->unique(); // الرقم المدني
            $table->string('job_title')->nullable(); // المسمى الوظيفي
            $table->string('password');
            $table->enum('role', ['super_admin', 'admin', 'department_head', 'employee', 'viewer'])
                ->default('employee');
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->boolean('password_changed')->default(false); // هل غير الباسورد؟
            $table->timestamp('first_login_at')->nullable(); // أول دخول
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

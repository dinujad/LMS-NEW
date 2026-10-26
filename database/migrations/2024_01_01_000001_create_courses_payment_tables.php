<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Courses Table
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0.00); // 0 = free
            $table->string('thumbnail')->nullable(); // path to image
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });

        // Enrollments Table
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('active'); // active, inactive, pending
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'course_id']); // Prevent double enrollment
        });

        // Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('method')->default('bank_transfer'); // bank_transfer, card, cash
            $table->string('transaction_ref')->nullable(); // Slip ID or Transaction ID
            $table->string('proof_image')->nullable(); // Slip photo path
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('admin_note')->nullable(); // Rejection reason etc.
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('courses');
    }
};

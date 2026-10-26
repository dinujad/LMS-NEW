<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('physical_exam_marks', function (Blueprint $table) {
            $table->float('mcq_marks')->nullable()->after('exam_name');
            $table->float('essay_marks')->nullable()->after('mcq_marks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('physical_exam_marks', function (Blueprint $table) {
            $table->dropColumn(['mcq_marks', 'essay_marks']);
        });
    }
};

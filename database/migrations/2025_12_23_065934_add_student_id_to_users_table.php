<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('student_id')->nullable()->unique()->after('id');
        });

        // Backfill existing students
        $students = \Illuminate\Support\Facades\DB::table('users')
            ->where('role', 'student')
            ->orderBy('created_at')
            ->get();
            
        $counter = 1000;
        foreach ($students as $student) {
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $student->id)
                ->update(['student_id' => 'SID' . $counter]);
            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('student_id');
        });
    }
};

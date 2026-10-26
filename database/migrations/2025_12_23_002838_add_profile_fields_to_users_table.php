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
            // Personal
            $table->string('whatsapp_number')->nullable()->after('phone');
            $table->string('nic')->nullable()->after('whatsapp_number');
            $table->date('dob')->nullable()->after('nic');
            
            // School
            $table->string('school')->nullable()->after('dob');
            $table->string('exam_year')->nullable()->after('school');
            $table->string('medium')->nullable()->after('exam_year'); // Sinhala/English
            $table->string('stream')->nullable()->after('medium'); // Maths/Bio
            
            // Delivery / Address
            $table->string('address_line_1')->nullable()->after('stream');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('district')->nullable()->after('address_line_2');
            
            // Guardian
            $table->string('guardian_name')->nullable()->after('district');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_number', 'nic', 'dob', 
                'school', 'exam_year', 'medium', 'stream',
                'address_line_1', 'address_line_2', 'district',
                'guardian_name', 'guardian_phone'
            ]);
        });
    }
};

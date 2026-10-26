<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notices Table
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('cascade'); // Null = Global Notice
            $table->string('title');
            $table->text('content');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // Tutes/Materials Table
        Schema::create('tutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('file'); // file, video_link, embed
            $table->string('file_path')->nullable(); // For PDF/Zip
            $table->string('video_url')->nullable(); // YouTube/Vimeo link
            $table->boolean('is_published')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tutes');
        Schema::dropIfExists('notices');
    }
};

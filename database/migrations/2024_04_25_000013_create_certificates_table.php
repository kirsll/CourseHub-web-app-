<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->string('certificate_number', 50)->unique();
            $table->string('template', 50)->default('default');
            $table->json('certificate_data');
            $table->string('file_path', 255);
            $table->timestamp('issued_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['user_id', 'issued_at']);
            $table->index(['course_id', 'issued_at']);
            $table->index(['certificate_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

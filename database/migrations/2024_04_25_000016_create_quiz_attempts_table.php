<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number');
            $table->json('answers');
            $table->integer('score');
            $table->integer('total_points');
            $table->decimal('percentage', 5, 2);
            $table->boolean('is_passed');
            $table->timestamp('started_at');
            $table->timestamp('completed_at')->nullable();
            $table->integer('time_taken_seconds')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'quiz_id', 'attempt_number']);
            $table->index(['user_id', 'quiz_id']);
            $table->index(['enrollment_id', 'is_passed']);
            $table->index(['percentage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->text('description');
            $table->longText('content')->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->string('preview_video', 255)->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->string('level', 20)->default('beginner'); // beginner, intermediate, advanced
            $table->string('language', 10)->default('ru');
            $table->integer('duration_minutes')->default(0);
            $table->integer('lessons_count')->default(0);
            $table->integer('students_count')->default(0);
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('reviews_count')->default(0);
            $table->json('requirements')->nullable();
            $table->json('what_you_will_learn')->nullable();
            $table->json('target_audience')->nullable();
            $table->boolean('is_published')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            $table->index(['slug', 'is_published']);
            $table->index(['instructor_id', 'is_published']);
            $table->index(['category_id', 'is_published']);
            $table->index(['rating', 'students_count']);
            $table->fullText(['title', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};

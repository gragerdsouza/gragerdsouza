<?php

declare(strict_types=1);

/**
 * Migration: create projects table.
 *
 * @author Grager D'souza <gragerdsouza@gmail.com>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void Nothing is returned.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500);
            $table->longText('body')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('url')->nullable();
            $table->string('repository_url')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void Nothing is returned.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

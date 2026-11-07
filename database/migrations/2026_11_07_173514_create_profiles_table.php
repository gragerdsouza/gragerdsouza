<?php

declare(strict_types=1);

/**
 * Migration: create profiles table.
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
        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('headline');
            $table->string('tagline')->nullable();
            $table->text('bio');
            $table->string('location')->nullable();
            $table->string('email');
            $table->string('avatar_path')->nullable();
            $table->string('resume_url')->nullable();
            $table->json('social_links')->nullable();
            $table->boolean('available_for_work')->default(true);
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
        Schema::dropIfExists('profiles');
    }
};

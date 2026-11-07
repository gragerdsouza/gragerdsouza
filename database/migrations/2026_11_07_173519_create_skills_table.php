<?php

declare(strict_types=1);

/**
 * Migration: create skills table.
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
        Schema::create('skills', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('category')->index();
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
        Schema::dropIfExists('skills');
    }
};

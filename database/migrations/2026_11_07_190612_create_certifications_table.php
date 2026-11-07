<?php

declare(strict_types=1);

/**
 * Migration: create certifications table.
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
        Schema::create('certifications', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('issuer');
            $table->date('issued_at');
            $table->date('expires_at')->nullable();
            $table->string('credential_url')->nullable();
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
        Schema::dropIfExists('certifications');
    }
};

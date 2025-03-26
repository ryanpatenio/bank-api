<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique(); // Ensures email uniqueness
            $table->timestamp('email_verified_at')->nullable(); // Track email verification
        
            $table->string('password');
            $table->string('google_id')->nullable(); // For OAuth logins
        
            // Use enum for role (more explicit than integer)
            $table->enum('role', ['user', 'admin', 'super_admin'])->default('user');
        
            // User status with index for faster queries
            $table->enum('status', [
                'active',     // Normal access
                'inactive',   // Not activated (e.g., email unverified)
                'suspended',  // Temporary ban (admin action)
                'blocked',    // Permanent ban
                'locked',     // Temporary lock (e.g., failed logins)
            ])->default('inactive');
        
            $table->index('status'); // Add index for faster filtering by status
        
            // Developer flag (assuming boolean is sufficient)
            $table->boolean('is_dev')->default(false); // Better than integer for flags
        
            $table->rememberToken(); // For "remember me" functionality
            $table->timestamps(); // created_at and updated_at
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

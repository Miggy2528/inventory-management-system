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
        Schema::create('customer_auth_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('action', [
                'account_created',
                'login_success',
                'login_failed',
                'logout',
                'password_changed',
                'account_suspended',
                'account_activated'
            ]);
            $table->string('ip_address', 45); // IPv6 compatible
            $table->text('user_agent')->nullable();
            $table->json('details')->nullable(); // Additional details like login method, failure reason, etc.
            $table->timestamps();
            
            $table->index(['customer_id', 'action']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['action', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_auth_logs');
    }
}; 
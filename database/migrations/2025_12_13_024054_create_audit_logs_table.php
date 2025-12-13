<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('actor_id')->nullable();
            $table->string('action');
            $table->string('entity_type');
            $table->uuid('entity_id');
            $table->string('request_id');
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->jsonb('meta')->default('{}');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('actor_id')->references('id')->on('users')->onDelete('set null');

            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

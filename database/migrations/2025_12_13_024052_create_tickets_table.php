<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('type');
            $table->string('status')->default('new');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('priority')->nullable();
            $table->uuid('created_by');
            $table->uuid('assigned_to')->nullable();
            $table->uuid('department_id')->nullable();
            $table->uuid('team_id')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('set null');

            $table->index(['tenant_id', 'status', 'created_at']);
            $table->index(['tenant_id', 'assigned_to', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

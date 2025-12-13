<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_teams', function (Blueprint $table) {
            $table->uuid('tenant_id');
            $table->uuid('user_id');
            $table->uuid('team_id');
            $table->string('role')->default('member');
            $table->timestamps();

            $table->primary(['user_id', 'team_id']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->index(['tenant_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_teams');
    }
};

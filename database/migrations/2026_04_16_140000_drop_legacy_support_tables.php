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
        // Drop legacy message table first due to foreign key constraints
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No practical way to restore legacy tables once dropped in this cleanup phase
    }
};

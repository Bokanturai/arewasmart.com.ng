<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agent_services', function (Blueprint $table) {
            // Allow loan applications (and other no-charge services) to have no transaction
            $table->foreignId('transaction_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agent_services', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable(false)->change();
        });
    }
};

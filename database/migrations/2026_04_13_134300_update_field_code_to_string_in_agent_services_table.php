<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agent_services', function (Blueprint $table) {
            // Change field_code from unsignedBigInteger to string to support alphanumeric codes like 'A100'
            $table->string('field_code', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agent_services', function (Blueprint $table) {
            $table->unsignedBigInteger('field_code')->nullable()->change();
        });
    }
};

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
        Schema::table('sme_datas', function (Blueprint $table) {
            $table->integer('failure_count')->default(0)->after('status');
            $table->timestamp('last_failure_at')->nullable()->after('failure_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sme_datas', function (Blueprint $table) {
            $table->dropColumn(['failure_count', 'last_failure_at']);
        });
    }
};

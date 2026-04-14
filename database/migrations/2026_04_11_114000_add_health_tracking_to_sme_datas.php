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
            if (!Schema::hasColumn('sme_datas', 'failure_count')) {
                $table->integer('failure_count')->default(0)->after('status');
            }
            if (!Schema::hasColumn('sme_datas', 'last_failure_at')) {
                $table->timestamp('last_failure_at')->nullable()->after('failure_count');
            }
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

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            $table->string('type')->default('support')->after('user_id')->index(); 
            $table->string('subject')->nullable()->after('type');
            $table->string('status')->nullable()->after('subject'); 
            $table->string('attachment')->nullable()->after('content');
            // We use raw SQL to avoid doctrine/dbal requirement for column modification
            DB::statement("ALTER TABLE ai_chats MODIFY COLUMN role ENUM('user', 'assistant', 'admin') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ai_chats', function (Blueprint $table) {
            $table->dropColumn(['type', 'subject', 'status', 'attachment']);
            DB::statement("ALTER TABLE ai_chats MODIFY COLUMN role ENUM('user', 'assistant') NOT NULL");
        });
    }
};

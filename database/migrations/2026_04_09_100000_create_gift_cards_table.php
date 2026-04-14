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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code_hash')->unique(); // Store hashed generated code
            $table->text('code_token_encrypted')->nullable(); // Encrypted raw code
            $table->decimal('amount', 15, 2);
            $table->string('title');
            $table->string('title_color')->nullable();
            $table->text('message')->nullable();
            $table->string('style')->default('default'); // 'birthday', 'love', 'gaming', etc.
            $table->string('text_color')->default('#ffffff');
            $table->string('image_path')->nullable();
            $table->enum('status', ['unused', 'used'])->default('unused');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('used_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};

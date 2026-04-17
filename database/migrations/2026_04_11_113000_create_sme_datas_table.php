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
        Schema::create('sme_datas', function (Blueprint $table) {
            $table->id();
            $table->string('data_id');
            $table->string('network');
            $table->string('plan_type');
            $table->decimal('amount', 10, 2);
            $table->string('size');
            $table->string('validity');
            $table->string('status')->default('enabled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sme_datas');
    }
};

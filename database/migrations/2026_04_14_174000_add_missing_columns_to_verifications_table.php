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
        Schema::table('verifications', function (Blueprint $table) {
            if (!Schema::hasColumn('verifications', 'stateOfOrigin')) {
                $table->string('stateOfOrigin')->nullable()->after('surname');
            }
            if (!Schema::hasColumn('verifications', 'lgaOfOrigin')) {
                $table->string('lgaOfOrigin')->nullable()->after('stateOfOrigin');
            }
            if (!Schema::hasColumn('verifications', 'maritalStatus')) {
                $table->string('maritalStatus')->nullable()->after('lgaOfOrigin');
            }
            if (!Schema::hasColumn('verifications', 'registrationDate')) {
                $table->string('registrationDate')->nullable()->after('maritalStatus');
            }
            if (!Schema::hasColumn('verifications', 'enrollmentBank')) {
                $table->string('enrollmentBank')->nullable()->after('registrationDate');
            }
            if (!Schema::hasColumn('verifications', 'enrollmentBranch')) {
                $table->string('enrollmentBranch')->nullable()->after('enrollmentBank');
            }
            if (!Schema::hasColumn('verifications', 'watchListed')) {
                $table->string('watchListed')->nullable()->after('enrollmentBranch');
            }
            if (!Schema::hasColumn('verifications', 'levelOfAccount')) {
                $table->string('levelOfAccount')->nullable()->after('watchListed');
            }
            if (!Schema::hasColumn('verifications', 'stateOfResidence')) {
                $table->string('stateOfResidence')->nullable()->after('levelOfAccount');
            }
            if (!Schema::hasColumn('verifications', 'lgaOfResidence')) {
                $table->string('lgaOfResidence')->nullable()->after('stateOfResidence');
            }
            if (!Schema::hasColumn('verifications', 'residentialAddress')) {
                $table->string('residentialAddress')->nullable()->after('lgaOfResidence');
            }
            if (!Schema::hasColumn('verifications', 'nationality')) {
                $table->string('nationality')->nullable()->after('residentialAddress');
            }
            if (!Schema::hasColumn('verifications', 'nameOnCard')) {
                $table->string('nameOnCard')->nullable()->after('nationality');
            }
            if (!Schema::hasColumn('verifications', 'phoneNumber2')) {
                $table->string('phoneNumber2')->nullable()->after('nameOnCard');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn([
                'stateOfOrigin',
                'lgaOfOrigin',
                'maritalStatus',
                'registrationDate',
                'enrollmentBank',
                'enrollmentBranch',
                'watchListed',
                'levelOfAccount',
                'stateOfResidence',
                'lgaOfResidence',
                'residentialAddress',
                'nationality',
                'nameOnCard',
                'phoneNumber2',
            ]);
        });
    }
};

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
        Schema::table('scoring', function (Blueprint $table) {
            $table->text('scores')->after('nama')->nullable();
            $table->string('template')->before('assessor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scoring', function (Blueprint $table) {
            $table->dropColumn('developing');
            $table->dropColumn('entrepreneurial');
            $table->dropColumn('organization');
            $table->dropColumn('decision');
            $table->dropColumn('thinking');
            $table->dropColumn('proactiveness');
        });
    }
};

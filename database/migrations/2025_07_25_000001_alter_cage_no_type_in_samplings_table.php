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
        // First, clean up any non-numeric data in cage_no (MySQL syntax)
        DB::statement("UPDATE samplings SET cage_no = NULL WHERE cage_no NOT REGEXP '^[0-9]+$'");
        
        // Change cage_no from string to unsignedBigInteger
        Schema::table('samplings', function (Blueprint $table) {
            $table->unsignedBigInteger('cage_no')->change();
        });
        
        // Add foreign key constraint
        Schema::table('samplings', function (Blueprint $table) {
            $table->foreign('cage_no')->references('id')->on('cages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samplings', function (Blueprint $table) {
            $table->string('cage_no')->change();
            $table->dropForeign(['cage_no']);
        });
    }
}; 
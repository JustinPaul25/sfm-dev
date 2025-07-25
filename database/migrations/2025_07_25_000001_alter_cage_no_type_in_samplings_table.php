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
        // Change cage_no from string to unsignedBigInteger
        Schema::table('samplings', function (Blueprint $table) {
            // If you have existing data, you may need to cast or clean it first
            $table->unsignedBigInteger('cage_no')->change();
            // Optionally add a foreign key constraint
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
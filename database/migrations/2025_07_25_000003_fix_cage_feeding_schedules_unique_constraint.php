<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Check if the constraint exists before trying to drop it
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'cage_feeding_schedules' 
            AND CONSTRAINT_NAME = 'unique_active_schedule_per_cage'
        ");
        
        if (!empty($constraints)) {
            Schema::table('cage_feeding_schedules', function (Blueprint $table) {
                // Drop the problematic unique constraint only if it exists
                $table->dropUnique('unique_active_schedule_per_cage');
            });
        }
    }

    public function down(): void
    {
        Schema::table('cage_feeding_schedules', function (Blueprint $table) {
            // Re-add the constraint if rolling back
            $table->unique(['cage_id', 'is_active'], 'unique_active_schedule_per_cage');
        });
    }
}; 
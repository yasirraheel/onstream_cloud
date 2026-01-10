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
        Schema::table('gd_urls', function (Blueprint $table) {
            // Add folder_id column if it doesn't exist
            if (!Schema::hasColumn('gd_urls', 'folder_id')) {
                $table->string('folder_id')->nullable()->after('file_id');
                $table->index('folder_id');
            }
        });

        // Clean up duplicates before adding unique constraint
        $duplicates = DB::table('gd_urls')
            ->select('file_id', DB::raw('COUNT(*) as count'))
            ->groupBy('file_id')
            ->having('count', '>', 1)
            ->get();

        foreach ($duplicates as $duplicate) {
            $entries = DB::table('gd_urls')
                ->where('file_id', $duplicate->file_id)
                ->orderBy('is_used', 'desc')
                ->orderBy('id', 'asc')
                ->get();

            // Keep the first one (prioritizing is_used = 1)
            $keep_id = $entries->first()->id;

            // Delete all others
            DB::table('gd_urls')
                ->where('file_id', $duplicate->file_id)
                ->where('id', '!=', $keep_id)
                ->delete();
        }

        // Add unique constraint on file_id
        Schema::table('gd_urls', function (Blueprint $table) {
            $table->unique('file_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gd_urls', function (Blueprint $table) {
            // Drop unique constraint
            $table->dropUnique(['file_id']);

            // Drop folder_id column
            if (Schema::hasColumn('gd_urls', 'folder_id')) {
                $table->dropColumn('folder_id');
            }
        });
    }
};

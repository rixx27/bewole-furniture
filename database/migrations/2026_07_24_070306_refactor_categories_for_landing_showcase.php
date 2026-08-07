<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('code')->nullable()->after('id');
            $table->renameColumn('image', 'cover_image');
            $table->renameColumn('description', 'short_description');
            $table->unsignedInteger('sort_order')->default(0)->after('short_description');
        });

        // Backfill code untuk data lama agar konsisten (CAT001, CAT002, ...)
        $counter = 1;
        DB::table('categories')->orderBy('id')->each(function (object $row) use (&$counter) {
            DB::table('categories')->where('id', $row->id)->update([
                'code' => 'CAT' . str_pad((string) $counter, 3, '0', STR_PAD_LEFT),
            ]);
            $counter++;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('sort_order');
            $table->renameColumn('cover_image', 'image');
            $table->renameColumn('short_description', 'description');
            $table->dropColumn('code');
        });
    }
};

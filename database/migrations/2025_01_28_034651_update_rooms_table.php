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
        Schema::table('rooms', function (Blueprint $table) {
            \DB::statement("DELETE FROM rooms WHERE quantity !~ '^[0-9]+$';");
            \DB::statement("ALTER TABLE rooms ALTER COLUMN quantity TYPE integer USING quantity::integer;");
            $table->integer('quantity')->unsigned()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            \DB::statement("ALTER TABLE rooms ALTER COLUMN quantity TYPE varchar(255);");
        });
    }
};

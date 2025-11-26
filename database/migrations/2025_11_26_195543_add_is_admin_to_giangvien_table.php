<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('giangvien', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false)->after('trangthai');
        });
    }

    public function down(): void
    {
        Schema::table('giangvien', function (Blueprint $table) {
            $table->dropColumn('is_admin');
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE peminjamans MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak', 'selesai', 'menunggu_kembali') NOT NULL DEFAULT 'pending'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamans', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE peminjamans MODIFY COLUMN status ENUM('pending', 'disetujui', 'ditolak', 'selesai') NOT NULL DEFAULT 'pending'");
        });
    }
};

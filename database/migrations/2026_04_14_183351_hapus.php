<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::table('detail_produksis', function (Blueprint $table) {
            // Hapus foreign key constraint
            $table->dropForeign('detail_produksis_id_produksi_foreign');
        });
    }

    public function down(): void
    {
        Schema::table('detail_produksis', function (Blueprint $table) {
            $table->foreign('id_produksi')
                  ->references('id_produksi')
                  ->on('produksis')
                  ->onDelete('cascade');
        });
    }
};

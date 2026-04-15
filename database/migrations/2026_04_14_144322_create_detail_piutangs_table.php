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
        Schema::create('detail_piutangs', function (Blueprint $table) {
            $table->id();
            $table->string('id_produksi', 50);
            $table->foreign('id_produksi')->references('id_produksi')->on('produksis')->onDelete('cascade');
            $table->string('cicilan_ke');
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->enum('pembayaran', ['Tunai', 'Bank'])->default('Tunai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_piutangs');
    }
};

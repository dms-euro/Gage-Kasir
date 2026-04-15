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
        Schema::create('detail_produksis', function (Blueprint $table) {
            $table->id();
            $table->string('id_produksi', 50);
            $table->foreign('id_produksi')->references('id_produksi')->on('produksis')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('restrict');
            $table->string('deskripsi');
            $table->string('bahan')->nullable();
            $table->decimal('panjang', 10, 2)->default(0);
            $table->decimal('lebar', 10, 2)->default(0);
            $table->integer('jumlah')->default(1);
            $table->decimal('harga', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_produksis');
    }
};

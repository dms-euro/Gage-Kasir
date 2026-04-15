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
        Schema::create('produksis', function (Blueprint $table) {
            $table->id();
            $table->string('id_produksi', 50)->unique();
            $table->foreignId('pelanggan_id')->constrained('pelanggans')->onDelete('restrict');
            $table->string('pic')->nullable();
            $table->decimal('biaya_design', 15, 2)->default(0);
            $table->decimal('diskon', 15, 2)->default(0);
            $table->decimal('total_tagihan', 15, 2);
            $table->decimal('bayar', 15, 2)->default(0);
            $table->enum('pembayaran', ['Tunai', 'Bank'])->default('Tunai');
            $table->enum('keterangan', ['LUNAS', 'UTANG'])->default('UTANG');
            $table->date('tanggal');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produksis');
    }
};

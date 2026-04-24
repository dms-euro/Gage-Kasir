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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('id_pelanggan', 50)->unique();
            $table->string('nama', 50);
            $table->string('cv', 50);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->integer('status');
            $table->foreignId('jenis_pelanggan_id')->nullable()->constrained('jenis_pelanggans')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};

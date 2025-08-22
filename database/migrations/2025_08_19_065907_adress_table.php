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
        Schema::create('addresses', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('nama_depan', 255);
        $table->string('nama_belakang', 255);
        $table->text('alamat');
        $table->string('kode_pos', 100);
        $table->string('kecamatan', 100);
        $table->string('provinsi', 100);
        $table->string('hp', 100);
        $table->string('kelurahan', 100);
        $table->string('kota', 100);
        $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    
};

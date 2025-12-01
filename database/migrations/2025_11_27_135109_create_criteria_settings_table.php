<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('criteria_settings', function (Blueprint $table) {
            $table->id();
            $table->string('column_name');       // nama kolom di tabel products
            $table->float('weight');             // bobot kriteria
            $table->enum('type', ['benefit', 'cost']); // jenis kriteria
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteria_settings');
    }
};

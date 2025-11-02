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
        Schema::create('live_accounts', function (Blueprint $table) {
            // Relasi user (nullable jika akun tidak selalu terhubung ke user sistem)
            $table->unsignedBigInteger('user_id');

            // Field utama
            $table->string('shopee_user_name');
            $table->string('affiliate_id'); // ID unik dari Shopee Affiliate
            $table->text('cookies')->nullable(); // disimpan terenkripsi
            $table->string('status')->default('active');

            // Relasi ke studio
            $table->foreignId('studio_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Composite primary key
            $table->primary(['affiliate_id', 'user_id']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_accounts');
    }
};

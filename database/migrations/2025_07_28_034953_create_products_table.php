<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->unsignedBigInteger('itemid')->primary(); // Primary Key
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('product_link')->nullable();
            $table->decimal('seller_commission', 10, 2)->nullable(); // misal 5.25%
            $table->unsignedBigInteger('historical_sold')->default(0);
            $table->decimal('price_min', 15, 2)->default(0);
            $table->decimal('price_max', 15, 2)->default(0);
            $table->decimal('rating_star', 3, 2)->nullable(); // misal 4.75
            $table->decimal('shop_rating', 3, 2)->nullable();
            $table->timestamps();

            $table->unsignedBigInteger('live_account_id')->nullable();
            $table->foreign('live_account_id')->references('user_id')->on('live_accounts')->onDelete('cascade');
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

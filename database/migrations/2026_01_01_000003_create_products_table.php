<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku', 50)->unique();
            $table->string('name');
            $table->unsignedBigInteger('price');   // rupiah, tanpa desimal
            $table->unsignedBigInteger('cost')->default(0);
            $table->integer('stock')->default(0);
            $table->unsignedInteger('min_stock')->default(5);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();                 // riwayat penjualan tetap utuh
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

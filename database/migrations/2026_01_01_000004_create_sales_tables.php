<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no', 40)->unique();
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('total');
            $table->unsignedBigInteger('paid');
            $table->unsignedBigInteger('change_amount')->default(0);
            $table->string('payment_method', 20)->default('cash'); // cash | transfer | qris
            $table->timestamps();
            $table->index('created_at');
        });

        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->string('product_name');        // snapshot nama saat transaksi
            $table->unsignedBigInteger('price');   // snapshot harga saat transaksi
            $table->unsignedInteger('qty');
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('type', 10);            // in | out | adjust
            $table->integer('qty');                // bertanda: + masuk, - keluar
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};

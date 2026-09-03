<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('address');
            $table->text('comment')->nullable();
            $table->string('status')->default('new');
            $table->decimal('total', 12, 2)->default(0);
            $table->boolean('stock_taken')->default(false);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->nullOnDelete();
            $table->string('name');
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('qty');
            $table->decimal('sum', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};

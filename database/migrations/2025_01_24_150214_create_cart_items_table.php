<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartItemsTable extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Links to users table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Links to products table
            $table->integer('quantity')->unsigned()->default(1);
            $table->timestamps();

            $table->unique(['user_id', 'product_id']); // Prevent duplicate cart items for the same product and user
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
}

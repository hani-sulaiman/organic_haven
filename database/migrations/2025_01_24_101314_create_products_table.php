<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('ingredients')->nullable();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->string('img_id')->nullable();
            $table->enum('unit_type', ['lb', 'oz', 'ea','ct'])->default('ea');
            $table->decimal('unit_value', 8, 2)->default(1);
            $table->string('img_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

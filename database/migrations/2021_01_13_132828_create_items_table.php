<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('Наименование товара');
            $table->text('manufacturer_part_number')->nullable()->comment('Код модели (артикул производителя)');
            $table->integer('price')->nullable()->comment('Цена');
            $table->string('warranty')->nullable()->comment('Гарантия');
            $table->boolean('in_stock')->nullable()->comment('В наличии');
            $table->unsignedBigInteger('rubric_id')->nullable()->index()->comment('Связь с рубрикой');
            $table->foreign('rubric_id')->references('id')->on('rubrics')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('category_id')->nullable()->comment('Связь с категорией');
            $table->foreign('category_id')->references('id')->on('categories')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('manufacturer_id')->nullable()->comment('Связь с производителем');
            $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}

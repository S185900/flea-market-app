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
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('出品者');
            $table->string('title')->comment('商品名');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->text('description')->comment('商品説明');
            $table->integer('price');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->integer('likes_count')->default(0)->comment('いいね数');
            $table->integer('comments_count')->default(0)->comment('コメント数');
            $table->integer('condition')->comment('商品状態_1:良好/2:目立った傷や汚れなし/3:やや傷や汚れあり/4:状態が悪い');
            $table->string('status')->nullable();
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

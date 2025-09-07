<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->comment('購入者');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade')->comment('出品者');
            $table->string('status');
            $table->string('payment_method');
            $table->string('stripe_payment_intent_id')->nullable()->comment('決済状態確認');
            $table->string('stripe_customer_id')->nullable()->comment('顧客情報管理');
            $table->string('stripe_checkout_session_id')->nullable()->comment('決済セッション管理');
            $table->string('shipping_address')->nullable()->comment('配送先住所/購入者の住所');
            $table->dateTime('completed_at')->nullable()->comment('取引完了日時');
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
        Schema::dropIfExists('transactions');
    }
}

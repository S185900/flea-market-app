<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Transaction;
use App\Models\Item;
use App\Models\User;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        $seller = User::factory()->create();
        $buyer = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $seller->id]);

        return [
            'item_id' => $item->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
            'status' => 'completed',
            'payment_method' => 'credit_card',
        ];
    }
}

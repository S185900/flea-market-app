<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\ItemImage;

class ItemImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Item::all()->each(function ($item) {
            ItemImage::factory()->count(rand(1, 3))->create(['item_id' => $item->id]);
        });
    }
}

<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use Faker\Generator as Faker;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Item;

$factory->define(Orderitem::class, function (Faker $faker) {

    return [
          
        'unit_price'=>$faker->numberBetween($min = 100, $max = 100000),
        'quantity'=>$faker->numberBetween($min = 1, $max = 100)
    ];

	 // $word = $faker->word();
	 //  //$order =  factory(Order::class)->create();
  //     // $item = factory(Item::class)->create(); 
  //   return [

  //       //'order_id'=>factory(Order::class)->create()->id,
  //       //'item_id'=>factory(Item::class)->create()->id, 
  //       'order_id'=>$order->id,
  //       'item_id'=>$item->id,       
  //       'unit_price'=>$faker->numberBetween($min = 100, $max = 100000),
  //       'quantity'=>$faker->numberBetween($min = 1, $max = 100)
        
  //   ];
});

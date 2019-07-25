<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    const API_PATH = '/api/orderitems';
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }    

    /** @test */
    public function orderitems_index_show()
    {
        $order=factory(Order::class)->create();       
        $category=factory(Category::class)->create();
         $item=factory(Item::class)->create(['category_id'=>$category->id]);
        $exps = factory(Orderitem::class, 2)->create(['order_id'=>$order->id,'item_id'=>$item->id]);

       $res = $this->json('GET','/api/orderitems?order_id=1');
        $res->assertStatus(200); 
        $res->assertExactJson([
            'data' => [
                [
                    'id'=>$order->id,
                    'total_price'=>$order->total_price,
                    'first_name'=>$order->first_name,
                    'last_name'=>$order->last_name,
                    'address1'=>$order->address1,
                    'address2'=>$order->address2,
                    'country'=>$order->country,
                    'state'=>$order->state,
                    'city'=>$order->city,                  
                    'created_at' => $this->toMySqlDateFromJson($order->created_at),
                    'updated_at' => $this->toMySqlDateFromJson($order->updated_at),
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'image' => $item->image,
                    'category_id' =>$category->id,
                    'deleted_at' => NULL,
                    'created_at' => $this->toMySqlDateFromJson($item->created_at),
                    'updated_at' => $this->toMySqlDateFromJson($item->updated_at),    
                    'id' => $exps[0]->id,
                    'order_id'=>$order->id,                    
                    'item_id'=>$item->id,                   
                    'unit_price'=>$exps[0]->unit_price,
                    'quantity'=>$exps[0]->quantity,
                    'created_at' => $this->toMySqlDateFromJson($exps[0]->created_at), 
                    'updated_at' => $this->toMySqlDateFromJson($exps[0]->updated_at),                                        
                ],
                [
                     'id'=>$order->id,
                    'total_price'=>$order->total_price,
                    'first_name'=>$order->first_name,
                    'last_name'=>$order->last_name,
                    'address1'=>$order->address1,
                    'address2'=>$order->address2,
                    'country'=>$order->country,
                    'state'=>$order->state,
                    'city'=>$order->city,                  
                    'created_at' => $this->toMySqlDateFromJson($order->created_at),
                    'updated_at' => $this->toMySqlDateFromJson($order->updated_at),
                    'id' => $item->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'image' => $item->image,
                    'category_id' =>$category->id,
                    'deleted_at' => NULL,
                    'created_at' => $this->toMySqlDateFromJson($item->created_at),
                    'updated_at' => $this->toMySqlDateFromJson($item->updated_at),    
                    'id' => $exps[1]->id,
                    'order_id'=>$order->id,                    
                    'item_id'=>$item->id,                   
                    'unit_price'=>$exps[1]->unit_price,
                    'quantity'=>$exps[1]->quantity,
                    'created_at' => $this->toMySqlDateFromJson($exps[1]->created_at), 
                    'updated_at' => $this->toMySqlDateFromJson($exps[1]->updated_at),                
                ]
            ]
        ]);
    }

    

}

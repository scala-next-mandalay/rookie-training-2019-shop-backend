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
    public function nothing_return_no_orderId()
    {
        $orderitem =  factory(Orderitem::class)->create();
        $res = $this->json('GET', self::API_PATH); 
        $res->assertStatus(200);
        $res->assertJsonCount(0, 'data');
    }

    /** @test */
    public function get_orderitems_with_orderId()
    {
         $order=factory(Order::class)->create(['id'=>'100']);
         $orderid1 =  factory(Orderitem::class)->create();
         $orderid2 =  factory(Orderitem::class)->create(['order_id'=>$order->id]);
         $orderid3 =  factory(Orderitem::class)->create();

        $res = $this->json('GET', '/api/orderitems?order_id='.$order->id); 
        $res->assertStatus(200);
        $res->assertJsonCount(1, 'data');
        $res->assertJson([
            'data' => [
                ['id' =>$orderid2->id,
                    'order_id'=>$orderid2->order_id
                ],
                
            ]
        ]);
    }   

     /** @test */ 
    public function orderitems_index_show()
    {        
        $item=factory(Item::class)->create();
         $order=factory(Order::class)->create(['id'=>'100']);
        $orderitem = factory(Orderitem::class,2)->create(['order_id'=>$order->id,'item_id'=>$item->id]);

       $res = $this->json('GET', '/api/orderitems?order_id='.$order->id);
        $res->assertStatus(200); 
        $res->assertExactJson([
            'data' => [
                [
                    'id' => $orderitem[0]->id,
                    'order_id'=>$order->id,                    
                    'item_id'=>$item->id,                   
                    'unit_price'=>$orderitem[0]->unit_price,
                    'quantity'=>$orderitem[0]->quantity,
                    'name'=>$item->name,
                    'created_at' => $this->toMySqlDateFromJson($orderitem[0]->created_at), 
                    'updated_at' => $this->toMySqlDateFromJson($orderitem[0]->updated_at),
                                                            
                ],
                [
                    'id' => $orderitem[1]->id,
                    'order_id'=>$order->id,                    
                    'item_id'=>$item->id,                   
                    'unit_price'=>$orderitem[1]->unit_price,
                    'quantity'=>$orderitem[1]->quantity,
                    'name'=>$item->name,
                    'created_at' => $this->toMySqlDateFromJson($orderitem[1]->created_at), 
                    'updated_at' => $this->toMySqlDateFromJson($orderitem[1]->updated_at),
                                                            
                ],                
            ]
        ]);
    }

}

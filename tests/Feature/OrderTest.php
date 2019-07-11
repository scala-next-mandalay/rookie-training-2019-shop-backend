<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;
use App\Models\Orderitem;
use App\Models\Item;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\QueryException;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    const API_PATH = '/api/orders';
   const STR255 = '0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789ABCDE';
   const STR256 = '0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789ABCDEF';
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

    //=========================================================================

    // store

    // Actually, you shuould add auth for store method.

    //===

     /** @test */
    public function on_store_order_success()
    {
         $item1 = factory(Item::class)->create();
         $item2 = factory(Item::class)->create();
         $item3 = factory(Item::class)->create();
         $order=factory(Order::class)->create();
         $res = $this->json('POST', self::API_PATH, [ 

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'order_id'=>[$order->id],
            'item_id_array'=>[$item1->id,$item2->id,$item3->id],
            'item_qty_array'=>[3,2,5],
            'item_price_array'=>[50,20,30]


        ]);

        $res->assertStatus(201);
        $res->assertJsonCount(12, 'data');
        $res->assertJsonStructure([
            'data' => [
                'id',
                'total_price',
                'first_name',
                'last_name',
                'address1',
                'address2',
                'country',
                 'state',
                 'city',
                'created_at',
                'updated_at'
            ]

        ]);

        $json = $res->json();//1 is id
        $this->assertEquals(100, $json['data']['total_price']);//2
        $this->assertEquals('kay', $json['data']['first_name']);//3
        $this->assertEquals('aung',$json['data']['last_name']);//4
        $this->assertEquals('padauk',$json['data']['address1']);//5
        $this->assertEquals('street',$json['data']['address2']);//6
        $this->assertEquals('myanmar',$json['data']['country']);//7
        $this->assertEquals('sagaing',$json['data']['state']);//8
        $this->assertEquals('mandalay',$json['data']['city']);//9 
        // $this->assertEquals($order->id, $json['data']['order_id']);//2
        // $this->assertEquals($item->id, $json['data']['item_id']);//3
        // $this->assertEquals(999, $json['data']['unit_price']);//4
        // $this->assertEquals(3, $json['data']['quantity']);
        $this->assertLessThan(2, time() - strtotime($json['data']['created_at']));//1
        $this->assertLessThan(2, time() - strtotime($json['data']['updated_at']));//2

    }  

    /** @test */
    public function store_without_postData_will_occur_validation_error()
    {        
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH);
    }

    
    /** @test */
    public function store_noParentCategoryId_will_occur_database_error()
    {
         // $item = factory(Item::class)->create();
        $this->expectException(QueryException::class);
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[1],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    //For FirstName

    /** @test */
    public function store_firstname_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_firstname_length_1_will_no_validation_error()
    {
         $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'1',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201); 
    }

    /** @test */
    public function store_firstname_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256
         $item = factory(Item::class)->create();
        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured

        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [            
            'total_price'=>100,
            'first_name'=>self::STR256,
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_firstname_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [           

            'total_price'=>100,
            'first_name'=>self::STR255,
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201);    

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['first_name']));
    }


    //for Price

    /** @test */
    public function store_price_minus1_will_occur_validation_error()
    {

       $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [             

            'total_price'=>-1,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_price_0_will_no_validation_error()
    {

        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>0,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201); 
    }

    //For LastName

    /** @test */
    public function store_lastname_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
    }

    /** @test */
    public function store_lastname_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'1',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201); 

    }

    /** @test */
    public function store_lastname_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256

        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>self::STR256,
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
    }

    /** @test */
    public function store_lastname_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>self::STR255,
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['last_name']));
    }

    //For Address1

    /** @test */
    public function store_address1_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_address1_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'1',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201); 

    }

    /** @test */
    public function store_address1_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256
        $item = factory(Item::class)->create();
        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured

        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>self::STR256,
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_address1_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>self::STR255,
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['address1']));
    }

    //For Address2

    /** @test */
    public function store_address2_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

     /** @test */
    public function store_address2_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'1',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201); 

    }

    /** @test */
    public function store_address2_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256

        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
           
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>self::STR256,
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_address2_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>self::STR255,
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]
        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['address2']));
    }

    //For Country

    /** @test */
    public function store_country_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [           

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
    }

    /** @test */
    public function store_country_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'1',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]
        ]);
        $res->assertStatus(201); 
    }

    /** @test */
    public function store_country_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256

        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [           

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>self::STR256,
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_country_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>self::STR255,
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['country']));
    }

    //For State

    /** @test */
    public function store_state_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_state_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'1',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);
        $res->assertStatus(201); 

    }

    /** @test */
    public function store_state_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256
        $item = factory(Item::class)->create();
        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured

        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
           

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>self::STR256,
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);

    }

    /** @test */
    public function store_state_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>self::STR255,
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['state']));
    }

    //For City

    /** @test */
    public function store_city_length_0_will_occur_validation_error()
    {
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_city_length_1_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'1',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201); 

    }

    /** @test */
    public function store_city_length_256_will_occur_validation_error()
    {

        //first, confirm strlen is 256

        $this->assertEquals(256, strlen(self::STR256));        

        //then, confirm exception is occured
        $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [
           

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>self::STR256,
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);

    }

    /** @test */
    public function store_city_length_255_will_no_validation_error()
    {
        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [            

           'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>self::STR255,
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201);         

        //Confirm that the string is not truncated due to DB constraints.

        $json = $res->json();
        $this->assertEquals(255, strlen($json['data']['city']));
    }


    //For item_qty_array

    /** @test */
     public function store_item_qty_zero_will_occur_validation_error()
    {

       $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [             

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[0],
            'item_price_array'=>[999]

        ]);

    }

    /** @test */
    public function store_item_qty_1_will_no_validation_error()
    {

        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[1],
            'item_price_array'=>[999]


        ]);
        $res->assertStatus(201); 
    }


    //For item_price_array

     /** @test */
    public function store_item_price_minus1_will_occur_validation_error()
    {

       $item = factory(Item::class)->create();
        $this->expectException(ValidationException::class);
        $res = $this->json('POST', self::API_PATH, [             

            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[-1]

        ]);

    }

    /** @test */
    public function store_item_price_0_will_no_validation_error()
    {

        $item = factory(Item::class)->create();
        $res = $this->json('POST', self::API_PATH, [
            
            'total_price'=>100,
            'first_name'=>'kay',
            'last_name'=>'aung',
            'address1'=>'padauk',
            'address2'=>'street',
            'country'=>'myanmar',
            'state'=>'sagaing',
            'city'=>'mandalay',
            'item_id_array'=>[$item->id],
            'item_qty_array'=>[3],
            'item_price_array'=>[0]


        ]);
        $res->assertStatus(201); 
    }


}

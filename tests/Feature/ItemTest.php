<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Item;
use App\Models\Category;

class ItemTest extends TestCase
{
     use RefreshDatabase;

     const API_PATH = '/api/items';

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

     /** @test */
    public function on_index_items_success()
    {
        $category =  factory(Category::class)->create();
        $exps = factory(Item::class, 2)->create(['category_id' => $category->id]);
        echo "Item index .....";
        $now = time();
        $res = $this->json('GET', '/api/items?offset=0'); 
        $res->assertStatus(200); 
        $res->assertExactJson([
            'data' => [
                [
                    'id' => $exps[0]->id,
                    'name' => $exps[0]->name,
                    'price' => $exps[0]->price,
                    'image' => $exps[0]->image,
                    'category_id' => $category->id,
                    'deleted_at' => NULL,
                    'created_at' => $this->toMySqlDateFromJson($exps[0]->updated_at),
                    'updated_at' => $this->toMySqlDateFromJson($exps[0]->created_at),
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'created_at' => $this->toMySqlDateFromJson($category->updated_at),
                        'updated_at' => $this->toMySqlDateFromJson($category->created_at),
                        'deleted_at' => null,
                    ]
                ],
                [
                    'id' => $exps[1]->id,
                    'name' => $exps[1]->name,
                    'price' => $exps[1]->price,
                    'image' => $exps[1]->image,
                    'category_id' => $category->id,
                    'deleted_at' => NULL,
                    'created_at' => $this->toMySqlDateFromJson($exps[1]->updated_at),
                    'updated_at' => $this->toMySqlDateFromJson($exps[1]->created_at),
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name,
                        'created_at' => $this->toMySqlDateFromJson($category->updated_at),
                        'updated_at' => $this->toMySqlDateFromJson($category->created_at),
                        'deleted_at' => null,
                    ]
                ],
            ]
        ]);
    }

     /** @test */
    public function items_are_order_by_id_desc()
    {
        echo "Item Desc .....";
        factory(Item::class)->create(['id' => 8]);
        factory(Item::class)->create(['id' => 35]);
        factory(Item::class)->create(['id' => 1250]);
        $res = $this->json('GET', self::API_PATH); 
        $res->assertStatus(200);
        $res->assertJsonCount(3, 'data');
        $res->assertJson([
            'data' => [
                ['id' => 1250],
                ['id' => 35],
                ['id' => 8],
            ]
        ]);
    }

     /** @test */
    public function deleted_items_are_not_shown()
    {
        echo "Not show deleted item ....";
        $row1 = factory(Item::class)->create();
        $row2 = factory(Item::class)->create();
        $row2->delete();
        $row3 = factory(Item::class)->create();
       
        
        $res = $this->json('GET',self::API_PATH); 
        $res->assertStatus(200);
        $res->assertJsonCount(2, 'data');
        $res->assertJson([
            'data' => [
                ['id' => $row3->id],
                ['id' => $row1->id],
            ]
        ]);
    }

     /** @test */
    public function get_11th_to_20th_items_if_limit10_offset10_totalSize30()
    {
            echo "Item Offset .....";
           $category =  factory(Category::class)->create();
           $exps = factory(Item::class, 30)->create(['category_id' => $category->id]);

           $res = $this->json('GET', '/api/items?offset=10');
           $res->assertJsonCount(10, 'data');
           $res->assertJson([
               'data' => [
                   ['id' => $exps[19]->id],//11th
                   ['id' => $exps[18]->id],
                   ['id' => $exps[17]->id],
                   ['id' => $exps[16]->id],
                   ['id' => $exps[15]->id],
                   ['id' => $exps[14]->id],
                   ['id' => $exps[13]->id],
                   ['id' => $exps[12]->id],
                   ['id' => $exps[11]->id],
                   ['id' => $exps[10]->id],//20th
               ]
           ]);
     }
    
    
}

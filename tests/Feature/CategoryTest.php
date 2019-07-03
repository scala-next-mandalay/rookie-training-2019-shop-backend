<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;
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

    public function categories_are_order_by_id_asc()
   {
       factory(Category::class)->create(['id' => 1250]);
       factory(Category::class)->create(['id' => 8]);
       factory(Category::class)->create(['id' => 35]);
       $res = $this->json('GET', '/api/categories');
       $res->assertStatus(200);
       $res->assertJsonCount(3, 'data');
       $res->assertJson([
           'data' => [
               ['id' => 8],
               ['id' => 35],
               ['id' => 1250],
           ]
       ]);
   }

   public function deleted_categories_are_not_shown()
   {
       $row1 = factory(Category::class)->create();
       $row2 = factory(Category::class)->create();
       $row2->delete();
       $row3 = factory(Category::class)->create();


       $res = $this->json('GET', '/api/categories');
       $res->assertStatus(200);
       $res->assertJsonCount(2, 'data');
       $res->assertJson([
           'data' => [
               ['id' => $row1->id],
               ['id' => $row3->id],
           ]
       ]);
   }

   public function on_index_categories_success()
    {
        $exps = factory(Category::class, 2)->create();
 
        $res = $this->json('GET', '/api/categories'); 
        $res->assertStatus(200); 
        $res->assertExactJson([
            'data' => [
                [
                    'id' => $exps[0]->id,
                    'name' => $exps[0]->name,
                    'created_at' => $this->toMySqlDateFromJson($exps[0]->updated_at),
                    'updated_at' => $this->toMySqlDateFromJson($exps[0]->created_at),
                    'deleted_at' => null,
                ],
                [
                    'id' => $exps[1]->id,
                    'name' => $exps[1]->name,
                    'created_at' => $this->toMySqlDateFromJson($exps[1]->updated_at),
                    'updated_at' => $this->toMySqlDateFromJson($exps[1]->created_at),
                    'deleted_at' => null,
                ]
            ]
        ]);
    }
}

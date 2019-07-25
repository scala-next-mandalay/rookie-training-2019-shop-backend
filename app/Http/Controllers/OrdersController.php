<?php

namespace App\Http\Controllers;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Order;
use App\Models\Orderitem;
use App\Http\Requests\Order\StoreOrderRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Order\IndexOrderRequest;
use App\Http\Requests\OrderItem\IndexOrderItemRequest;


class OrdersController extends Controller
{

    public function store(StoreOrderRequest $request)
    {
      return \DB::transaction(function() use($request){
          $data=$request->validated();

          $orderKeys=['total_price','first_name','last_name','address1','address2','country','state','city'];

          $orderArr=[];
          
          foreach ($orderKeys as $key) {
              $orderArr[$key]=$data[$key];
          }

          $orderModel=Order::create($orderArr);
          $dump=[];

          foreach ($data['item_id_array'] as $i => $itemId) {
              $itemArr=[
                      'order_id' => $orderModel->id,
                   'item_id' => $itemId,
                   'unit_price' => $data['item_price_array'][$i],
                   'quantity' => $data['item_qty_array'][$i]
              ];

              $dump[]=Orderitem::create($itemArr);
          }

          $orderModel->Orderitem=$dump;
          return new JsonResource($orderModel);
      });
    }

    public function index(IndexOrderRequest $request,IndexOrderItemRequest $req):JsonResource
    {    
   
        var_dump($request->begin_date);
        var_dump($request->end_date);      


        //$builder = Order::orderBy('id','desc');
        $builder = Order::query();
        $builder->orderBy('id','desc');

        if ($request->begin_date) {
          $builder->where('created_at','>=',$request->begin_date);
        }
        if ($request->end_date) {
          $builder->where('created_at','<=',$request->end_date);
        }
        if($req->order_id) {
          $builder->where('id','=',$req->order_id);
        }
        if($request->begin_date&&$request->end_date)
        {
          $builder->where('created_at','>=',$request->begin_date)
                  ->where('created_at','<=',$request->end_date);
        }    

        return JsonResource::collection($builder->get()); 
       
    }           
        

}

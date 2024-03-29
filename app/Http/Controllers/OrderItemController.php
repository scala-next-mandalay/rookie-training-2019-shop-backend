<?php

namespace App\Http\Controllers;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;
use App\Models\Orderitem;
use App\Models\Order;
use Illuminate\Http\Response;
use App\Http\Requests\OrderItem\IndexOrderItemRequest;


class OrderItemController extends Controller
{	    

    public function index(IndexOrderItemRequest $request)
    {	
         $orderitem=Orderitem::
            join('items','items.id','=','orderitems.item_id')
            ->select('orderitems.*','items.name')
            ->where('orderitems.order_id','=',$request->order_id)
            ->get();
            return JsonResource::collection($orderitem);
    }          
    
}

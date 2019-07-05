<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Item;
use App\Models\Category;
use App\Http\Requests\Item\StoreItemRequest;
use App\Http\Requests\Item\IndexItemsRequest;

class ItemsController extends Controller
{
    public function index(IndexItemsRequest $request): ResourceCollection
    {
        return JsonResource::collection(
            Item::orderBy('id','desc')
            ->with('category')->orderBy('id')
            ->limit(config('const.ITEM_LIMIT'))
            ->offset($request->offset)
            ->get()                      
        );
    }

    
   
}

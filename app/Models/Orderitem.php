<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Orderitem;

class Orderitem extends Model
{
    public $fillable = [
        'order_id',
        'item_id',
        'unit_price',
        'quantity',

    ];

    public function orders()
    {
    	$this->belongsTo(Order::class);
    }
}

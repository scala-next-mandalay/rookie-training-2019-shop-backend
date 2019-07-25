<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Order;
use App\Models\Orderitem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Orderitem extends Model
{
    public $fillable = [
        'order_id',
        'item_id',
        'unit_price',
        'quantity',

    ];

    public function orders(): BelongsTo
    {
		 return $this->belongsTo(Order::class, 'order_id');
    }

    
}

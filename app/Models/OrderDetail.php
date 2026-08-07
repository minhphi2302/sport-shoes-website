<?php

namespace App\Models;

use App\Core\Model;

class OrderDetail extends Model
{
    protected string $table = 'order_details';
    protected string $primaryKey = 'detail_id';
}

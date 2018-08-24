<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
    	return $this->belongsTo('App\Users', 'user_id');
    }

    public function get_order_list($user_id, $order_status = null, $paginate)
    {
    	$order_list = new Order;

    	if (isset($order_status) && $order_status!=99) {
    		$order_list = $order_list->where('status', $order_status);
    	}

    	$order_list = $order_list->where('user_id', $user_id)->orderBy('id', 'DESC')->paginate($paginate);

    	return $order_list;
    }
}

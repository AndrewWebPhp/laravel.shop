<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

	public function index()
	{
		$orders = Order::active()->paginate(3); // scope

		return view('auth.orders.index', [
			'orders' => $orders
		]);
	}

	public function show(Order $order)
	{
		return view('auth.orders.show', [
			'order' => $order
		]);
	}
}

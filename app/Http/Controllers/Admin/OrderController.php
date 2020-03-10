<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{

	public function index()
	{
		$orders = Order::active()->paginate(10); // scope

		return view('auth.orders.index', [
			'orders' => $orders
		]);
	}

	public function show(Order $order)
	{
		$products = $order->products()->withTrashed()->get();

		return view('auth.orders.show', [
			'order' => $order,
			'products' => $products,
		]);
	}
}

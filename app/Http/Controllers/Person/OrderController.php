<?php

namespace App\Http\Controllers\Person;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

	public function index()
	{
		$orders = Auth::user()->orders()->active()->paginate(3); // Scope

		return view('auth.orders.index', [
			'orders' => $orders
		]);
	}

	public function show(Order $order)
	{
		 if( !Auth::user()->orders->contains($order) ){
			return redirect()->route('person.orders.index');
		 }

		return view('auth.orders.show', [
			'order' => $order
		]);
	}
}

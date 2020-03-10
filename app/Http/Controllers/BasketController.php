<?php

namespace App\Http\Controllers;


use App\Classes\Basket;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{

	public function basket()
	{
		$order = (new Basket())->getOrder();

		return view('basket', [
			'order' => $order
		]);
	}



	/*
	 * Так можно получить все товары, которые были закрепленны к конкретному заказу
	 * Данные берутся из промежуточной таблицы
	 *
	 * dump($order->products);
	 *
	 */
	public function basketAdd(Product $product)
	{
		$result = (new Basket(true))->addProduct($product);

		if($result) {
			session()->flash('success', 'Товар ' . $product->name .' был добавлен');
		} else {
			session()->flash('warning', 'Товар ' . $product->name .' в большем количестве не доступен для заказа.');
		}

		return redirect()->route('basket');
	}



	public function basketRemove(Product $product)
	{
		(new Basket())->removeProduct($product);

		Order::changeFullSum(-$product->price);
		session()->flash('warning', 'Товар ' . $product->name .' был удален из корзины');

		return redirect()->route('basket');
	}

	/*
	 * Checkout
	 */
	public function basketPlace()
	{
		$basket = new Basket();
		$order = $basket->getOrder();

		if( !$basket->countAvailable() ) {
			session()->flash('warning', 'Товар в большем количестве не доступен для заказа!');
			return redirect()->route('basket');
		}

		return view('order', [
			'order' => $order
		]);
	}

	/*
	 * Checkout Confirm
	 */
	public function basketConfirm( Request $request )
	{
		$success = (new Basket())->saveOrder($request->name, $request->phone);

		if($success) {
			session()->flash('success', 'Ваш заказ принят в обработку');
		} else {
			session()->flash('warning', 'Товар в большем количестве не доступен для заказа!');
		}

		Order::eraseOrderSum();

		return redirect()->route('index');
	}

}
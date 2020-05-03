<?php

namespace App\Classes;

use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\Product;
use App\Services\CurrencyConversion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Basket
{
	protected $order;


	public function __construct($createOrder = false)
	{
		$order = session('order'); // нам нужен весь заказ в сессии


		/*$order->currency_id = CurrencyConversion::getCurrentCurrencyFromSession()->id;
		session(['order' => $order]);*/


		if( is_null($order) && $createOrder ){
			$data = [];
			if(Auth::check()){
				$data['user_id'] = Auth::id(); // ID юзера, который делает заказ, для таблицы Orders
			}
			$data['currency_id'] = CurrencyConversion::getCurrentCurrencyFromSession()->id; // текущая валюта для таблицы Orders

			$this->order = new Order($data);
			//$this->order = Order::create($data); //создаем заказ в таблице "orders"
			session(['order' => $this->order]); // заказ закидываем в сессию, чтобы доставать его на других страницах
		} else {
			$this->order = $order;
		}

	}

	public function countAvailable($updateCount = false)
	{
		$products = collect([]);
		foreach ($this->order->products as $orderProduct)
		{
			$product = Product::find($orderProduct->id);
			if ($orderProduct->countInOrder > $product->count) {
				return false;
			}

			if ($updateCount) {
				$product->count -= $orderProduct->countInOrder;
				$products->push($product);
			}
		}

		if ($updateCount) {
			$products->map->save();
		}

		return true;
	}


	public function getOrder()
	{
		return $this->order;
	}

	/*public function getPivotRow($product)
	{
		return $this->order->products()->where('product_id', $product->id)->first()->pivot;
	}*/


	public function saveOrder($name, $phone, $email)
	{
		if( !$this->countAvailable(true) ) {
			return false;
		}

		$this->order->saveOrder($name, $phone);
		// Send an Email message
		Mail::to($email)->send( new OrderCreated($name, $this->getOrder()) );

		return true;
	}

	public function removeProduct(Product $product)
	{
		if( $this->order->products->contains($product) ) {

			//$pivotRow = $this->order->products()->where('product_id', $product->id)->first()->pivot; // pivot

			$pivotRow = $this->order->products->where('id', $product->id)->first();

			if( $pivotRow->countInOrder < 2 ) {
				//$this->order->products()->detach($product->id);
				//dd($this->order->products);
				//$this->order->products->pop($product);


				$id = $product->id;
				$key = $this->order->products->search(function($item) use($id) {
					return $item->id == $id;
				});
				$this->order->products->pull($key);

			} else {
				//$pivotRow->count--;
				//$pivotRow->update();
				$pivotRow->countInOrder--;
			}

		}
	}

	public function addProduct(Product $product)
	{
		// Метод contains() определяет, содержит ли коллекция заданное значение
		if( $this->order->products->contains($product) ) {

			//$pivotRow = $order->products()->where('product_id', $productId)->first();  // модель
			//$pivotRow = $this->order->products()->where('product_id', $product->id)->first()->pivot; // pivot
			//$pivotRow->count++;


			$pivotRow = $this->order->products->where('id', $product->id)->first();

			if( $pivotRow->countInOrder >= $product->count ) {
				return false;
			}
			$pivotRow->countInOrder++;

			//$pivotRow->update();
		} else {

			if( $product->count == 0 ) {
				return false;
			}

			/*
			 * Прикрепляем к конкретному заказу конкретный товар в промежуточной таблице.
			 * https://laravel.com/docs/5.8/eloquent-relationships#updating-many-to-many-relationships
			 */
			//$this->order->products()->attach($product->id);



			$product->countInOrder = 1;
			// В свойство этого класса Order, в ячейку 'products' помещаем очередной товар
			$this->order->products->push($product);
		}

		return true;
	}



}
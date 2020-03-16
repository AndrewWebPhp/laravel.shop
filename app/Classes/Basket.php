<?php

namespace App\Classes;

use App\Mail\OrderCreated;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class Basket
{
	protected $order;


	public function __construct($createOrder = false)
	{
		$orderId = session('orderId');

		if( is_null($orderId) && $createOrder ){

			$data = [];

			if(Auth::check()){
				$data['user_id'] = Auth::id();
			}

			$this->order = Order::create($data); //создаем заказ в таблице "orders"
			session(['orderId' => $this->order->id]); // id этого заказа закидываем в сессию, чтобы доставать его на других страницах

		} else {
			$this->order = Order::findOrFail($orderId);
		}
	}

	public function countAvailable($updateCount = false)
	{

		foreach ($this->order->products as $orderProduct)
		{
			if( $orderProduct->count < $this->getPivotRow($orderProduct)->count ) {
				return false;
			}

			if($updateCount) {
				$orderProduct->count -= $this->getPivotRow($orderProduct)->count;
			}
		}

		if($updateCount) {
			$this->order->products->map->save();
		}

		return true;
	}


	public function getOrder()
	{
		return $this->order;
	}

	public function getPivotRow($product)
	{
		return $this->order->products()->where('product_id', $product->id)->first()->pivot;
	}


	public function saveOrder($name, $phone, $email)
	{
		if( !$this->countAvailable(true) ) {
			return false;
		}

		// Send an Email message
		Mail::to($email)->send( new OrderCreated($name, $this->getOrder()) );

		return $this->order->saveOrder($name, $phone);
	}

	public function removeProduct(Product $product)
	{
		if( $this->order->products->contains($product->id) ) {
			$pivotRow = $this->order->products()->where('product_id', $product->id)->first()->pivot; // pivot

			if( $pivotRow->count < 2 ) {
				$this->order->products()->detach($product->id);
			} else {
				$pivotRow->count--;
				$pivotRow->update();
			}

		}
	}

	public function addProduct(Product $product)
	{
		// Есть ли уже текущий продукт в корзине
		if( $this->order->products->contains($product->id) ) {

			//$pivotRow = $order->products()->where('product_id', $productId)->first();  // модель
			$pivotRow = $this->order->products()->where('product_id', $product->id)->first()->pivot; // pivot
			$pivotRow->count++;

			if( $pivotRow->count > $product->count ) {
				return false;
			}

			$pivotRow->update();

		} else {

			if( $product->count == 0 ) {
				return false;
			}

			/*
			 * Прикрепляем к конкретному заказу конкретный товар в промежуточной таблице.
			 * https://laravel.com/docs/5.8/eloquent-relationships#updating-many-to-many-relationships
			 */
			$this->order->products()->attach($product->id);

		}

		Order::changeFullSum($product->price);

		return true;
	}



}
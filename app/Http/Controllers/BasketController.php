<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasketController extends Controller
{


	public function basket()
	{
		$orderId = session('orderId');
		$order = null;

		if(!is_null($orderId)) {
			$order = Order::findOrFail($orderId);
		}

		return view('basket', [
			'order' => $order
		]);
	}




	public function basketAdd($productId)
	{
		$orderId = session('orderId');

		if(is_null($orderId)){
			$order = Order::create(); //создаем заказ в таблице "orders"
			session(['orderId' => $order->id]); // id этого заказа закидываем в сессию, чтобы доставать его на других страницах

		} else {
			$order = Order::find($orderId);
		}


		// Есть ли уже текущий продукт в корзине
		if( $order->products->contains($productId) ) {

			//$pivotRow = $order->products()->where('product_id', $productId)->first();  // модель
			$pivotRow = $order->products()->where('product_id', $productId)->first()->pivot; // pivot
			$pivotRow->count++;
			$pivotRow->update();

		} else {

			/*
			 * Прикрепляем к конкретному заказу конкретный товар в промежуточной таблице.
			 * https://laravel.com/docs/5.8/eloquent-relationships#updating-many-to-many-relationships
			 */
			$order->products()->attach($productId);

		}


		if(Auth::check()){
			$order->user_id = Auth::id();
			$order->save();
		}


		/*
		 * Так можно получить все товары, которые были закрепленны к конкретному заказу
		 * Данные берутся из промежуточной таблицы
		 */
		//dump($order->products);


		$product = Product::find($productId);

		Order::changeFullSum($product->price);

		session()->flash('success', 'Товар ' . $product->name .' был добавлен');

		return redirect()->route('basket');
	}



	public function basketRemove($productId)
	{
		$orderId = session('orderId');

		if(is_null($orderId)) {
			return redirect()->route('basket');
		}

		$order = Order::find($orderId);


		if( $order->products->contains($productId) ) {
			$pivotRow = $order->products()->where('product_id', $productId)->first()->pivot; // pivot

			if( $pivotRow->count < 2 ) {
				$order->products()->detach($productId);
			} else {
				$pivotRow->count--;
				$pivotRow->update();
			}

		}


		$product = Product::find($productId);

		Order::changeFullSum(-$product->price);

		session()->flash('warning', 'Товар ' . $product->name .' был удален из корзины');

		return redirect()->route('basket');
	}







	/*
	 * Checkout
	 */
	public function basketPlace()
	{
		$orderId = session('orderId');

		if(is_null($orderId)) {
			return redirect()->route('index');
		}

		$order = Order::find($orderId);

		return view('order', [
			'order' => $order
		]);
	}

	/*
	 * Checkout Confirm
	 */
	public function basketConfirm( Request $request )
	{
		$orderId = session('orderId');

		if(is_null($orderId)) {
			return redirect()->route('index');
		}
		$order = Order::find($orderId);

		$success = $order->saveOrder($request->name, $request->phone);

		if($success) {
			session()->flash('success', 'Ваш заказ принят в обработку');
		} else {
			session()->flash('warning', 'Ошибка!');
		}

		Order::eraseOrderSum();

		return redirect()->route('index');
	}

}

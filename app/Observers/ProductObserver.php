<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Subscription;

class ProductObserver
{

	public function updating(Product $product)
	{
		//dd($product);

		$oldCount = $product->getOriginal('count'); // Получаем значение поля "count", которое было до обновление модели!

		if ($oldCount == 0 && $product->count > 0) {
			Subscription::sendEmailsBySubscription($product);
		}
	}
}

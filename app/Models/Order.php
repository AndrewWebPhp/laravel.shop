<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

	protected $fillable = ['user_id'];

    public function products(){
    	//return $this->belongsToMany(Product::class);
    	return $this->belongsToMany(Product::class)->withPivot('count')->withTimestamps();
    }

	public function user()
    {
        return $this->belongsTo(User::class);
    }


	public function calculateFullSum()
    {
	    $sum = 0;
	    foreach ($this->products()->withTrashed()->get() as $product){
		    $sum += $product->getPriceForCount();
	    }

	    return $sum;
    }

	public static function changeFullSum($changeSum)
    {
        $sum = self::getFullPrice() + $changeSum;
        session(['full_order_sum' => $sum]);
    }

	public static function eraseOrderSum()
    {
        session()->forget('full_order_sum');
    }

    /*
     * Этот метот никак не зависит от конкретного заказа
     * так как у него нет обращения к свойствам
     *
     * */
	public static function getFullPrice()
    {
	    return session('full_order_sum', 0);
    }


	public function saveOrder($name, $phone)
    {

    	if($this->status == 0) {
		    $this->name = $name;
		    $this->phone = $phone;
		    $this->status = 1;
		    $this->save();

		    session()->forget('orderId');

		    return true;
	    }

	    return false;
    }

	public function scopeActive($query)
    {
		return $query->where('status', 1);
    }
}

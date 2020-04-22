<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use App\Services\CurrencyConversion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

	/*
	 * Трейт. Трейт - расширение класса.
	 * Мы добавляем функционал класса, который уже где-то реализован.
	 */
	use SoftDeletes, Translatable;

	protected $fillable = ['name', 'name_en', 'code', 'price', 'category_id', 'description', 'description_en', 'image', 'hit', 'new', 'recommend', 'count'];

    /*public function getCategory(){
    	//$category = Category::where('id', $this->category_id)->first();
    	return Category::find($this->category_id);
    }*/

    public function category(){
    	//Eloquent will try to match the category_id from the Product model to an id on the Category model
    	return $this->belongsTo(Category::class);   /// тоже самое что: return Category::find($this->category_id);
    }



	public function getPriceForCount()
    {
    	if( !is_null( $this->pivot ) ) {
    		return $this->pivot->count * $this->price;
	    }
        return $this->price;
    }


    // Мутатор
	public function setNewAttribute($value)
    {
        $this->attributes['new'] = $value === 'on' ? 1 : 0;
    }

	// Мутатор
	public function setHitAttribute($value)
	{
		$this->attributes['hit'] = $value === 'on' ? 1 : 0;
	}

	// Мутатор
	public function setRecommendAttribute($value)
	{
		$this->attributes['Recommend'] = $value === 'on' ? 1 : 0;
	}

	// Scope
	public function scopeByCode($query, $code)
	{
		return $query->where('code', $code);
	}
	// Scope
	public function scopeHit($query)
	{
		return $query->where('hit', 1);
	}
	// Scope
	public function scopeNew($query)
	{
		return $query->where('new', 1);
	}
	// Scope
	public function scopeRecommend($query)
	{
		return $query->where('recommend', 1);
	}


	public function isHit()
    {
		return $this->hit === 1;
    }

	public function isNew()
    {
	    return $this->new === 1;
    }

	public function isRecommend()
    {
	    return $this->recommend === 1;
    }
	public function isAvailable()
    {
		return !$this->trashed() && $this->count > 0;
    }


    // Аксесоры...
	public function getPriceAttribute($value)
	{
		//return $value . ' test ';
		return round(CurrencyConversion::convert($value), 2);
	}

}

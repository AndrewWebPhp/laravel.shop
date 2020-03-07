<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

	protected $fillable = ['name', 'code', 'price', 'category_id', 'description', 'image', 'hit', 'new', 'recommend'];

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

}

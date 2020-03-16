<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductsFilterRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index(ProductsFilterRequest $request){
    	//dd(get_class_methods($request));
    	//dd($request->all());
    	//$products = Product::get();

	    //\Debugbar::info('my info');

	    //$productsQuery = Product::query(); // аналог Product::get()
	    $productsQuery = Product::with('category'); // with() заменяет метотд query()

	    if( $request->filled('price_from') ){ // если поле заполненно
		    $productsQuery->where('price', '>=', $request->price_from);
	    }
	    if( $request->filled('price_to') ){
		    $productsQuery->where('price', '<=', $request->price_to);
	    }
	    foreach (['new', 'hit', 'recommend'] as $field) {
		    if( $request->has( $field ) ){
			    //$productsQuery->where($field, 1);
			    $productsQuery->$field(); // scope
		    }
	    }


	    //dd($request->getQueryString());

    	$products = $productsQuery->paginate(6)->withPath( "?" . $request->getQueryString() );

	    return view('index', [
	    	'products' => $products
	    ]);
    }

	public function categories(){

    	$categories = Category::get();

		return view('categories', [
			'categories' => $categories
		]);
	}


	public function category($code){
		//$products = Product::where('category_id', $category->id)->get();


		$category = Category::where('code', $code)->first();


		return view('category', [
			'category' => $category,
			//'products' => $products
		]);
	}

	public function product($category, $productCode){

		$product = Product::withTrashed()->byCode($productCode)->firstOrFail(); // Scope

		return view('product', [
			'product' => $product
		]);
	}


}

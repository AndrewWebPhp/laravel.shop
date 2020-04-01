<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductsFilterRequest;
use App\Http\Requests\SubscriptionRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MainController extends Controller
{
    public function index(ProductsFilterRequest $request){
    	//dd(get_class_methods($request));
    	//dd($request->all());
    	//$products = Product::get();

	    //\Debugbar::info('my info');


	    // some test comment
	    //$productsQuery = Product::query(); // аналог Product::get()
	    $productsQuery = Product::with('category'); // with() заменяет метотд query()
	    $productsFilterUrl = [];

	    if( $request->filled('price_from') ){ // если поле заполненно
		    $productsQuery->where('price', '>=', $request->price_from);
		    $productsFilterUrl['price_from'] = $request->price_from;
	    }
	    if( $request->filled('price_to') ){
		    $productsQuery->where('price', '<=', $request->price_to);
		    $productsFilterUrl['price_to'] = $request->price_to;
	    }
	    foreach (['new', 'hit', 'recommend'] as $field) {
		    if( $request->has( $field ) ){
			    //$productsQuery->where($field, 1);
			    $productsQuery->$field(); // scope
			    $productsFilterUrl[$field] = 'on';
		    }
	    }

	    //dd($request->getQueryString());
    	//$products = $productsQuery->paginate(6)->withPath( "?" . $request->getQueryString() );
    	$products = $productsQuery->paginate(6)->appends($productsFilterUrl);


	    $allProductsCount = Product::count();

	    return view('index', [
	    	'products' => $products,
	    	'allProductsCount' => $allProductsCount
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


	public function subscribe( SubscriptionRequest $request, Product $product )
	{
		Subscription::create([
			'email' => $request->email,
			'product_id' => $product->id
		]);

		return redirect()->back()->with('success', 'Мы дадим Вам занть, когда товар появится на складе.');
	}


	public function changeLocale($locale)
	{
		//dd( App::getLocale() );

		$availableLocales = ['ru', 'en'];
		if (!in_array($locale, $availableLocales)) {
			$locale = config('app.locale'); // достаем значение из Config. config() - хелпер
		}
		session(['locale' => $locale]); // записываем в ссесию локаль
		App::setLocale($locale);
		return redirect()->back();
	}

}

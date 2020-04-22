<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     */
    public function index()
    {

	    $products = Product::paginate(10);

	    return view('auth.products.index', [
		    'products' => $products
	    ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     */
    public function create()
    {

	    $categories = Category::get();

	    return view('auth.products.form', [
	    	'categories' => $categories
	    ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
	    $params = $request->all();
	    unset($params['image']);

	    if( $request->has('image') ) {
		    $path = $request->file('image')->store('products');
		    $params['image'] = $path;
	    }

	    Product::create($params);
        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     *
     */
    public function show(Product $product)
    {
	    return view('auth.products.show', [
		    'product' => $product
	    ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     */
    public function edit(Product $product)
    {
	    $categories = Category::get();

        return view('auth.products.form', [
        	'product' => $product,
	        'categories' => $categories
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     */
    public function update(ProductRequest $request, Product $product)
    {
	    $params = $request->all();
	    unset($params['image']);

	    if( $request->has('image') ){
		    Storage::delete($product->image);
		    $path = $request->file('image')->store('products');
		    $params['image'] = $path;
	    }

	    foreach (['new', 'hit', 'recommend'] as $fieldName) {
		    if( !isset($params[$fieldName]) ) {
			    $params[$fieldName] = 0;
		    }
	    }

	    $product->update($params);
	    return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy(Product $product)
    {
	    $product->delete();

	    return redirect()->route('products.index');
    }
}

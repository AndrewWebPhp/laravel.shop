<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Classes\Test;
use App\Models\Product;
use Faker\Generator as Faker;



$factory->define(Product::class, function (Faker $faker) {



	return [
	    'name' => 'Product' . ' ' . Test::$number++,
	    'code' => 'product',
	    'description' => 'description of my test product',
	    'price' => rand(100, 5000),
	    'category_id' => rand(1, 3),
	    'image' => 'products/lnddOjD9LXWgd1ehW1albTei0OBdp7pvpNWLrBFp.jpeg',
	    'count' => rand(0, 10),
    ];


});

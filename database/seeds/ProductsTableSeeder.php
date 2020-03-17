<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductsTableSeeder extends Seeder
{

	public function run()
	{

		factory(App\Models\Product::class, 5)->create();

	}


}

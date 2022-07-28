<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests\CreateOrder;
use PhpParser\Node\Stmt\TraitUseAdaptation\Precedence;
use GuzzleHttp\Promise\Create;
use App\Models\Category;
use App\Models\Country;
use App\Models\Offer;


class ProductCartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){

        DB::table('categories')->truncate();
        DB::table('products')->truncate();
        DB::table('countries')->truncate();
        DB::table('offers')->truncate();

        $category = Category::insert([           // 3ayzin category lel tops w pants bs
           
            ['name'=>'Tops'], 
            ['name'=>'Pants'],
            ['name'=>'Jackets'],
            ['name'=>'Shoes'],
                           
        ]);

        $subCategory = Category::insert([

            ['name'=>'tshirts',
            'parent_id'=>Category::whereName('Tops')->first()->id],
            ['name'=>'blouses',
            'parent_id'=>Category::whereName('Tops')->first()->id],
            ['name'=>'pants',
            'parent_id'=>Category::whereName('Pants')->first()->id],
            ['name'=>'sweatpants',
            'parent_id'=>Category::whereName('Pants')->first()->id],
           
        ]);

        $country = Country::insert([
            ['name'=>'US',
            'rate'=>'2'],
            ['name'=>'UK',
            'rate'=>'3'],
            ['name'=>'CN',
            'rate'=>'2'], 
        ]);

        $product = Product::insert([
            [
                'category_id'=>Category::whereName('tshirts')->first()->id,
                'country_id'=>Country::whereName('US')->first()->id,
                'price'=>30.99,                                        //tshirt price & rate
                'weight'=>'0.2',
            ],
            [
                'category_id'=>Category::whereName('blouses')->first()->id,
                'country_id'=>Country::whereName('UK')->first()->id,
                'price'=>10.99,                                          //blouse price & rate 
                'weight'=>'0.3',

            ],
            [
                'category_id'=>Category::whereName('pants')->first()->id,
                'country_id'=>Country::whereName('UK')->first()->id,
                'price'=>64.99,                                          //pants price & rate 
                'weight'=>'0.9',

            ],
            [
                'category_id'=>Category::whereName('sweatpants')->first()->id,
                'country_id'=>Country::whereName('CN')->first()->id,
                'price'=>84.99,                                          //sweatpants price & rate 
                'weight'=>'1.1',

            ],
            [
                'category_id'=>Category::whereName('Jackets')->first()->id,
                'country_id'=>Country::whereName('US')->first()->id,
                'price'=>199.99,                                          //jackets price & rate 
                'weight'=>'2.2',

            ],
            [
                'category_id'=>Category::whereName('Shoes')->first()->id,
                'country_id'=>Country::whereName('CN')->first()->id,
                'price'=>79.99,                                          //shoes price & rate 
                'weight'=>'1.3',

            ],
        ]);

        $offer= Offer::insert([
           
            [
                'category_id'=> Category::whereName('Shoes')->first()->id,  
                'value'=>'10',                              
                'condition_id'=> Category::whereName('Shoes')->first()->id,  
                'min_condition'=>'1',                
                'type'=>'percentage',                            // 2 types : percentage and value
            ],
            [
                'category_id'=>Category::whereName('Jackets')->first()->id,  
                'value'=>'50',                              
                'condition_id' => Category::whereName('Tops')->first()->id,  
                'min_condition'=>'2',                
                'type'=>'percentage'                            // 2 types : percentage and value
            ],
            [
                'category_id'=>'',     // any 2 products         DEPENDS ON SHIPPING FEES
                'value'=>'10',                              
                'condition_id'=>'',                                               
                'min_condition'=>'2',                
                'type'=>'fixed'                            // 2 types : percentage and value
            ],
                        

        ]);

    }

}



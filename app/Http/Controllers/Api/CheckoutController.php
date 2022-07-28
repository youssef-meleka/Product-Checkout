<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Requests\CreateOrder;
use App\Models\Category;
use App\Models\Country;
use App\Models\Offer;
use phpDocumentor\Reflection\Types\Null_;

class CheckoutController extends Controller
{

    //test
    public function index(){

        return Offer::all();

    }
    public function offerClaculation(CreateOrder $request){
        
        $prdouctsInArray = $request->validated();
        
        $discount['fixed'] = 0;
        
        $offers = Offer::all();
        $categories = collect();
        $products = collect();

        foreach ($request->validated()['products'] as $cartItem){
           
            $prod = Product::find($cartItem['id']);
            $cat = $prod->category()->first();
            $cat_id = $cat->parent_id ?: $cat->id;
            
            $categories->push([
                'category_id' => $cat_id,
                'id' => $cartItem['id'],
                'quantity' => $cartItem['quantity'],
                'price'=>$prod->price,
                'discount'=>0,
                'final_price'=>0,
            ]);
            $products->push([
                'category_id' => $cat_id,
                'id' => $cartItem['id'],
                'quantity' => $cartItem['quantity'],
                'price'=>$prod->price,
                'subtotal'=>$cartItem['quantity']*$prod->price,
                'discount'=>0,
                'final_price'=>0,
                'shipping_fees' => $prod->weight*$prod->country()->first()->rate*10,
                'vat'=>$cartItem['quantity']*$prod->price*(14/100),
            ]);
        }

        foreach($offers as $offer){

            $prodCount =collect($request->validated()['products'])->sum('quantity')*count($request->validated()['products']);
            
            if($offer->condition_id){
                
               $categoriesWithOfferArr = $categories->where('category_id', $offer->condition_id)->values();
               
               if($categoriesWithOfferArr){

                    $soldQuantity = collect($categoriesWithOfferArr)->sum('quantity');
                    if($soldQuantity >= $offer->min_condition){  
                                             
                        $applyOffer=$categories->where('category_id', $offer->category_id)->values()->first();

                        if($offer->type =='percentage'){
                            
                            $initialPrice = Product::find($applyOffer['id'])->price;      
                            
                            $applyOffer=collect($applyOffer)->put('price', $initialPrice);
                           
                            $dicountValue=collect($applyOffer)->get('price')*($offer->value/100);                            
                            $priceAfterDicount = $applyOffer->get('price')-$dicountValue;                            
                            
                            $products=collect($products)->map(function ($item) use($applyOffer, $dicountValue, $priceAfterDicount, $prodCount) {
                                
                                    return [
                                        'id' => $item['id'],
                                        'category_id' => $item['category_id'],
                                        'price' => $item['price'],
                                        'discount' => $applyOffer['id'] == $item['id'] ? $dicountValue : $item['discount'],
                                        'final_price' => $applyOffer['id'] == $item['id'] ? $priceAfterDicount : $item['final_price'],
                                        'quantity' => $item['quantity'],
                                        'subtotal'=>$item['quantity']*$item['price'],
                                        'shipping_fees'=> $item['shipping_fees'],
                                        'vat'=> $item['vat'],
                                        
                                    ];
                            });
                        }   
                    }
                }
            }
        }

        return [
            'data' => [
                'products' => $products,
                'subtotal' => $products->sum('subtotal'),
                'discount' => $products->sum('discount'),
                'shipping_fees' => $products->sum('shipping_fees'),            // there is 10$ off shipping fees as OFFER !!
                'off_shipping_fees'=> $prodCount >= 2 ? $products->sum('shipping_fees')-10 : $products->sum('shipping_fees') ,
                'vat' => $products->sum('vat'),
                'total' => $products->sum('subtotal')-$products->sum('discount')+$products->sum('shipping_fees'),  
            ]
        ];
    
    }

}

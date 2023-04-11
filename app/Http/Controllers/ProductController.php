<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Illuminate\Validation\Rule;
use Stripe\Exception\ApiErrorException;
use App\Models\StripeProduct;
use App\Http\Controllers\StripeProductController;



class ProductController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }
    // public function index()
    // {
    //     //if no api key is set, redirect to previous page with alert
    //     if (config('services.stripe.secret')) {
    //         $products = Product::all()->data;
    //     } else {
    //         $products_array = [
    //             [
    //                 "id" => __('no api key set'),
    //                 "name" => __('no api key set'),
    //                 "active" => true,
    //                 "alert_no_key" => true, 
    //             ]
    //         ];
    //         $products = json_decode(json_encode($products_array));
    //     }
        
    //     // dd($products);
    //     return view('admin.products.index', compact('products'));
    // }

    public function index()
    {
        // If no API key is set, retrieve products from the local database
        if (config('services.stripe.secret')) {
            $products = StripeProduct::all(); // Use your StripeProduct model to retrieve all records
        }
        foreach($products as $product) {
            $product->visible = ($product->metadata['visible'] == "false") ? false : true;
            foreach ($product->prices as $price) {
                if ($price['price_id'] == $product->default_price) {
                    $product->amount = $price['unit_amount'];
                    $product->currency = $price['currency'];
                    $number_formatted = number_format($price['unit_amount']/100, 2, ',', '.');
                    //build the price with currency
                    $product->price = ($price['currency'] == 'brl') ? "R$$number_formatted" : "$$number_formatted";

                }
            }            
        }
        
        return view('admin.products.index', compact('products'));
    }

    public function setVisibility(Request $request, $productId)
    {
        $validated = $request->validate([
            'visible' => ['required', Rule::in([true, false])],
        ]);
    
        $visible = $validated['visible'];
    
        try {    
            $product = Product::retrieve($productId);
            $product->metadata['visible'] = $visible;
            $product->save();
            //sync database with class StripeProductController method sync
            $stripeProductController = new StripeProductController();
            $stripeProductController->sync();

            return response()->json(['message' => __('Product visibility updated successfully. DB sync done!')], 200);
        } catch (ApiErrorException $e) {
            return response()->json(['message' => __('Failed to update product visibility')], 400);
        }
    }
}

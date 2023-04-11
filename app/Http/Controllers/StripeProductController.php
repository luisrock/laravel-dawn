<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StripeProduct;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;

class StripeProductController extends Controller
{
    public function sync()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $stripeProducts = Product::all(['active' => true]);

        foreach ($stripeProducts->data as $stripeProduct) {
            $localProduct = StripeProduct::updateOrCreate(
                ['product_id' => $stripeProduct->id],
                [
                    'active' => $stripeProduct->active,
                    'default_price' => $stripeProduct->default_price,
                    'description' => $stripeProduct->description,
                    'image' => isset($stripeProduct->images[0]) ? $stripeProduct->images[0] : null,
                    'metadata' => $stripeProduct->metadata,
                    'name' => $stripeProduct->name,
                ]
            );

            $stripePrices = Price::all(['product' => $stripeProduct->id, 'active' => true]);
            $pricesData = [];

            foreach ($stripePrices->data as $stripePrice) {
                $pricesData[] = [
                    'price_id' => $stripePrice->id,
                    'nickname' => $stripePrice->nickname,
                    'active' => $stripePrice->active,
                    'unit_amount' => $stripePrice->unit_amount,
                    'currency' => $stripePrice->currency,
                ];
            }

            $localProduct->update(['prices' => $pricesData]);
        }

        return response()->json(['message' => 'Sync completed successfully.']);
    }

    public function get_for_profile()
    {
        $stripeProducts = StripeProduct::all();
        foreach ($stripeProducts as $product) {
            $period = '';
            $meta = $product->metadata;
            // dd($meta);
            if ($meta['period_number'] == "30" && $meta['period_frequency'] == "day") {
                $period = __("month");
            } else if ($meta['period_frequency'] == "year") {
                $period = __("year");
            }
            $product->period = $period;

            foreach ($product->prices as $price) {
                if ($price['price_id'] == $product->default_price) {
                    $formatted_price = (strtolower($price['currency']) == 'brl') ? "R$" . number_format($price['unit_amount'] / 100, 2, ',', '.') : '$' . number_format($price['unit_amount'] / 100, 2);
                    $formatted_price .= (strtolower($price['currency']) == 'brl') ? '' : $price['currency'];
                    $product->formatted_price = $formatted_price;
                }
            }
        }
        //return $products as std object
        return json_decode(json_encode($stripeProducts));
    }
}
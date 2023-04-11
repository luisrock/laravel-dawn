<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('stripe_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_id')->unique();
            $table->boolean('active')->default(true);
            $table->string('default_price')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->json('metadata')->nullable();
            $table->string('name');
            //prices. store as array of arrays, each one with 'price_id', 'nickname', 'active', 'unit_amount', 'currency' 
            $table->json('prices')->nullable();   
            $table->softDeletes();         
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_products');
    }
};

/*
Stripe Product Object
{
    "id": "prod_NeY8bWO6dCkW1G",
    "object": "product",
    "active": true,
    "attributes": [],
    "created": 1680634623,
    "default_price": "price_1MtF2SAabNZCbwviNnyTaVIw",
    "description": "30 dias de acesso ao teses e Súmulas",
    "images": [],
    "livemode": false,
    "metadata": {
      "period_frequency": "day",
      "period_number": "30",
      "role": "subscriber",
      "visible": "true"
    },
    "name": "T&S Acesso Mensal",
    "owning_merchant": "acct_1MmeSnAabNZCbwvi",
    "owning_merchant_info": "acct_1MmeSnAabNZCbwvi",
    "package_dimensions": null,
    "prices": {
      "object": "list",
      "data": [
        {
          "id": "price_1Mtap4AabNZCbwviAjk9dS2l",
          "object": "price",
          "active": true,
          "automatic_currency_conversion_eligible": false,
          "billing_scheme": "per_unit",
          "created": 1680718362,
          "currency": "brl",
          "currency_options": {
            "brl": {
              "custom_unit_amount": null,
              "tax_behavior": "unspecified",
              "unit_amount": 2900,
              "unit_amount_decimal": "2900"
            }
          },
          "custom_unit_amount": null,
          "livemode": false,
          "lookup_key": null,
          "metadata": {},
          "never_used": true,
          "nickname": "Preço Promocional",
          "owning_merchant": "acct_1MmeSnAabNZCbwvi",
          "owning_merchant_info": "acct_1MmeSnAabNZCbwvi",
          "product": "prod_NeY8bWO6dCkW1G",
          "recurring": null,
          "tax_behavior": "unspecified",
          "tiers_mode": null,
          "transform_quantity": null,
          "type": "one_time",
          "unit_amount": 2900,
          "unit_amount_decimal": "2900"
        },
        {
          "id": "price_1MtF2SAabNZCbwviNnyTaVIw",
          "object": "price",
          "active": true,
          "automatic_currency_conversion_eligible": false,
          "billing_scheme": "per_unit",
          "created": 1680634624,
          "currency": "brl",
          "currency_options": {
            "brl": {
              "custom_unit_amount": null,
              "tax_behavior": "unspecified",
              "unit_amount": 3900,
              "unit_amount_decimal": "3900"
            }
          },
          "custom_unit_amount": null,
          "livemode": false,
          "lookup_key": null,
          "metadata": {},
          "never_used": false,
          "nickname": null,
          "owning_merchant": "acct_1MmeSnAabNZCbwvi",
          "owning_merchant_info": "acct_1MmeSnAabNZCbwvi",
          "product": "prod_NeY8bWO6dCkW1G",
          "recurring": null,
          "tax_behavior": "unspecified",
          "tiers_mode": null,
          "transform_quantity": null,
          "type": "one_time",
          "unit_amount": 3900,
          "unit_amount_decimal": "3900"
        }
      ],
      "has_more": false,
      "total_count": 2,
      "url": "/v1/prices?product=prod_NeY8bWO6dCkW1G"
    },
    "shippable": null,
    "skus": {
      "object": "list",
      "data": [],
      "has_more": false,
      "total_count": 0,
      "url": "/v1/skus?product=prod_NeY8bWO6dCkW1G&active=true"
    },
    "statement_descriptor": null,
    "tax_code": null,
    "type": "service",
    "unit_label": null,
    "updated": 1680718365,
    "url": null,
    "user_hidden_in_lists": false
  }
*/

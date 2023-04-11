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
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('stripe_payment_intent_id')->unique();
            $table->string('stripe_customer_id');
            $table->string('payment_method')->nullable();
            $table->string('status');
            $table->string('currency');
            $table->integer('amount');
            $table->string('description')->nullable();
            $table->string('barcode')->nullable();
            $table->dateTime('expiration_date')->nullable();
            $table->string('voucher_url')->nullable();
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};

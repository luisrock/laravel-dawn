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
        Schema::table('payment_intents', function (Blueprint $table) {
            $table->string('last_digits')->nullable();
            $table->string('brand')->nullable();
            $table->string('card_id')->nullable();
            $table->string('card_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payment_intents', function (Blueprint $table) {
            $table->dropColumn('last_digits');
            $table->dropColumn('brand');
            $table->dropColumn('card_id');
            $table->dropColumn('card_type');
        });
    }
};

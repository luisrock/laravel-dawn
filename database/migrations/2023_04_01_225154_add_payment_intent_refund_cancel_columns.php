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
            $table->boolean('refunded')->default(false);
            $table->timestamp('cancelled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('payment_intents', function (Blueprint $table) {
            $table->dropColumn('refunded');
            $table->dropColumn('cancelled_at');
        });
    }
};

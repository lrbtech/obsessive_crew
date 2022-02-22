<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_histories', function (Blueprint $table) {
            $table->id();
            $table->string('booking_id')->nullable();
            $table->string('invoicereference')->nullable()->default('');
            $table->string('transactiondate')->nullable()->default(''); 
            $table->string('paymentgateway')->nullable()->default(''); 
            $table->string('referenceid')->nullable()->default('');
            $table->string('trackid')->nullable()->default('');
            $table->string('transactionid')->nullable()->default('');
            $table->string('paymentid')->nullable()->default('');
            $table->string('authorizationid')->nullable()->default('');
            $table->string('transactionstatus')->nullable()->default('');
            $table->string('transationvalue')->nullable()->default('');
            $table->string('customerservicecharge')->nullable()->default('');
            $table->string('duevalue')->nullable()->default('');
            $table->string('paidcurrency')->nullable()->default('');
            $table->string('paidcurrencyvalue')->nullable()->default('');
            $table->string('currency')->nullable()->default('');
            $table->string('error')->nullable()->default('');
            $table->string('cardnumber')->nullable()->default('');
            $table->string('status')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_histories');
    }
}

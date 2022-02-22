<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBankCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_cards', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->nullable();
            $table->string('card_type')->nullable()->default(''); //example credit or debit
            $table->string('card_info')->nullable()->default(''); //example visa/master
            $table->string('card_name')->nullable()->default('');
            $table->string('card_no')->nullable()->default('');
            $table->string('expiry_month')->nullable()->default('');
            $table->string('expiry_year')->nullable()->default('');
            $table->string('cvv_no')->nullable()->default('');
            $table->string('token_value')->nullable()->default('');
            $table->string('save_card')->default('0');
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
        Schema::dropIfExists('bank_cards');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('shop_id')->nullable();
            $table->string('customer_id')->nullable();
            $table->string('booking_id')->nullable();
            $table->string('payment_type')->default('0')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('payment_status')->default('0')->nullable();
            $table->string('booking_type')->default('0')->nullable();
            $table->TEXT('booking_for')->nullable();
            $table->string('booking_date')->nullable();
            $table->string('booking_time')->nullable();
            $table->string('coupon_id')->default('0')->nullable();
            $table->string('coupon_code')->nullable();
            $table->string('subtotal',11)->default('0')->nullable();
            $table->string('coupon_value',11)->default('0')->nullable();
            $table->string('membership_name')->nullable();
            $table->string('membership_percentage',11)->default('0')->nullable();
            $table->string('membership_value',11)->default('0')->nullable();
            $table->string('total',11)->default('0')->nullable();
            $table->string('service_charge',11)->default('0')->nullable();
            $table->string('otp')->nullable();
            $table->string('address_id',11)->default('0')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->TEXT('address')->nullable();
            $table->string('vehicle_id',11)->default('0')->nullable();
            $table->string('booking_status',11)->default('0')->nullable();
            $table->string('read_status',11)->default('0')->nullable();
            $table->string('status',11)->default('0')->nullable();
            $table->string('assign_to',11)->default('0');
            $table->string('assign_agent_id',11)->nullable();
            $table->string('assign_date',20)->nullable();
            $table->string('assign_time',20)->nullable();
            $table->string('process_agent_id',11)->nullable();
            $table->string('process_date',20)->nullable();
            $table->string('process_time',20)->nullable();
            $table->string('complete_agent_id',11)->nullable();
            $table->string('complete_date',20)->nullable();
            $table->string('complete_time',20)->nullable();
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
        Schema::dropIfExists('bookings');
    }
}

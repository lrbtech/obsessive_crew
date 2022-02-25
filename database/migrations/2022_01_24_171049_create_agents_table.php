<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default('');
            $table->string('email')->nullable()->default('');
            $table->string('mobile')->nullable()->default('');
            $table->string('password')->nullable()->default('');
            $table->string('otp')->nullable()->default('');
            $table->string('firebase_key')->nullable()->default('');
            $table->string('role_id')->nullable()->default('');
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
        Schema::dropIfExists('agents');
    }
}

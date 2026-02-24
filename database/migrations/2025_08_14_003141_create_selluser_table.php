<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('selluser', function (Blueprint $table) {
           $table->increments('id'); 
            $table->string('state',150);
            $table->string('property_type', 150);
            $table->string('property_sub_type',150);
            $table->string('price_range',150);
            $table->string('first_name',150);
            $table->string('last_name',150);
            $table->string('email',150);
            $table->string('country_code',150);
            $table->string('phone_number',100);
            $table->timestamp('created_at')->nullable();  
            $table->timestamp('updated_at')->nullable();
            $table->boolean('is_email_verified')->default(false);
            $table->string('email_otp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('selluser');
    }
};

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
        Schema::create('agent', function (Blueprint $table) {
          $table->increments('id'); 
            $table->string('states',150);
            $table->string('experience', 150);
            $table->string('brokerageCompany',250);
            $table->string('agentId',150);
            $table->string('firstName',150);
            $table->string('lastName',150);
            $table->string('email',150);
            $table->string('countryCode',150);
            $table->string('phoneNumber',100);
            $table->string('prefrenceType',150);
            $table->string('cashbackBuyer',150);
            $table->string('agentCommission',150);
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
        Schema::dropIfExists('agent');
    }
};

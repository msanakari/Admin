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
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id'); // AUTO_INCREMENT + PRIMARY KEY
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password', 60);
            $table->string('profile_image', 100)->nullable();
            $table->enum('status', ['Active', 'Inactive','Block'])->default('Active');
            $table->string('remember_token', 100)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->tinyInteger('user_type')->default(0);
            $table->tinyInteger('is_active')->default(0);




        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
};

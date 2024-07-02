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
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('ic')->unique();
            $table->string('no_matric')->nullable()->unique();
            $table->string('email')->nullable();
            $table->string('intake');
            $table->bigInteger('batch');
            $table->bigInteger('session');
            $table->bigInteger('semester');
            $table->string('program');
            $table->string('password');
            $table->string('status', 25)->nullable()->default('2');
            $table->integer('campus_id')->nullable();
            $table->date('date')->nullable();
            $table->date('date_offer')->nullable();
            $table->integer('student_status')->nullable();
            $table->string('stafID_add', 45)->nullable();
            $table->date('date_add')->nullable();
            $table->string('stafID_mod', 45)->nullable();
            $table->date('date_mod')->nullable();
            $table->string('image')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('students');
    }
};

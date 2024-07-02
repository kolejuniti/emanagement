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
        Schema::create('student_form', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_ic');
            $table->unsignedBigInteger('main')->nullable();
            $table->bigInteger('pre_registration')->nullable();
            $table->bigInteger('c19')->nullable();
            $table->bigInteger('complete_form')->nullable();
            $table->bigInteger('copy_ic')->nullable();
            $table->bigInteger('copy_birth')->nullable();
            $table->bigInteger('copy_spm')->nullable();
            $table->bigInteger('copy_school')->nullable();
            $table->bigInteger('copy_pic')->nullable();
            $table->bigInteger('copy_pincome')->nullable();
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
        Schema::dropIfExists('student_form');
    }
};

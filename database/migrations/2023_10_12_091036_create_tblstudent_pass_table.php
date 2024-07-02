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
        Schema::create('tblstudent_pass', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_ic', 25);
            $table->string('pass_type', 25)->nullable();
            $table->string('pass_no', 25)->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_expired')->nullable();
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
        Schema::dropIfExists('tblstudent_pass');
    }
};

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
        Schema::create('tblstudent_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_ic', 25);
            $table->string('semester_id', 25);
            $table->string('session_id', 25);
            $table->string('status_id', 25);
            $table->bigInteger('kuliah_id');
            $table->dateTime('date');
            $table->string('remark')->nullable();
            $table->string('add_staffID');
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
        Schema::dropIfExists('tblstudent_log');
    }
};

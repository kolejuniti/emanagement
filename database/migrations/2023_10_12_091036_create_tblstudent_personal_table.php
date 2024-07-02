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
        Schema::create('tblstudent_personal', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('student_ic')->unique('student_ic_2');
            $table->date('date_birth')->nullable();
            $table->string('advisor_id', 25)->nullable();
            $table->string('bank_name', 30)->nullable();
            $table->string('bank_no')->nullable();
            $table->string('ptptn_no')->nullable();
            $table->dateTime('datetime')->nullable();
            $table->bigInteger('religion_id');
            $table->bigInteger('nationality_id');
            $table->bigInteger('sex_id');
            $table->bigInteger('state_id');
            $table->bigInteger('marriage_id');
            $table->bigInteger('statelevel_id');
            $table->bigInteger('citizenship_id');
            $table->string('no_tel')->nullable();
            $table->string('no_tel2')->nullable();
            $table->string('no_telhome')->nullable();
            $table->bigInteger('dun');
            $table->bigInteger('parlimen');
            $table->bigInteger('qualification');
            $table->bigInteger('oku')->nullable();
            $table->string('no_jkm', 25)->nullable();

            $table->index(['student_ic'], 'student_ic');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblstudent_personal');
    }
};

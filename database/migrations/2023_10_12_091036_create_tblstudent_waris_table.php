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
        Schema::create('tblstudent_waris', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('student_ic', 25);
            $table->string('name');
            $table->string('ic', 25);
            $table->string('home_tel')->nullable();
            $table->string('phone_tel');
            $table->string('occupation');
            $table->string('dependent_no');
            $table->decimal('kasar', 10);
            $table->decimal('bersih', 10)->nullable();
            $table->bigInteger('relationship');
            $table->bigInteger('race');
            $table->bigInteger('status');
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
        Schema::dropIfExists('tblstudent_waris');
    }
};

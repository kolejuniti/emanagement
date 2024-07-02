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
        Schema::create('tblspm_dtl', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('student_spm_ic')->index('student_spm_id');
            $table->bigInteger('subject_spm_id')->nullable();
            $table->bigInteger('grade_spm_id')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->useCurrentOnUpdate()->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tblspm_dtl');
    }
};

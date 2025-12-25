<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_steps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sub_step_name');

            $table->unsignedBigInteger('step_id');
            $table->foreign('step_id')->references('id')->on('steps');
       
            $table->text('sub_step_detail')->nullable();
            $table->string('sub_step_img')->nullable();
            $table->integer('order_no');
            $table->integer('estimated_time');

            $table->softDeletes();
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
        Schema::dropIfExists('sub_steps');
    }
}

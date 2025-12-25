<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEstimatedTimeNullableOnSubStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sub_steps', function (Blueprint $table) {
            $table->integer('estimated_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sub_steps', function (Blueprint $table) {
            $table->integer('estimated_time')->nullable(false)->change();
        });
    }
}

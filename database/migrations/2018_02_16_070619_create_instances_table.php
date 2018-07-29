<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('project_id');

            $table->foreign('project_id')
                ->references('id')->on('projects')
                ->onDelete('cascade');


            $table->string('name', 96);
            $table->string('url',200);

            $table->timestamps();

            $table->index(['name'], 'i_name_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('instances');
    }
}

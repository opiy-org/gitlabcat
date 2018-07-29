<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');

            $table->string('gitlab_name')->unique();
            $table->unsignedInteger('gitlab_id')->nullable();

            $table->string('channel');
            $table->timestamps();

            $table->index(['gitlab_id'], 'p_gi_index');
            $table->index(['gitlab_name'], 'p_gn_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->string('name', 96);

            $table->string('gitlab_name', 96)->unique();
            $table->unsignedInteger('gitlab_id')->nullable();

            $table->string('channel', 128);
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

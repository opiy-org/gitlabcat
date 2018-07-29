<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('gitlab_name')->unique();
            $table->unsignedInteger('gitlab_id')->nullable();

            $table->string('uid')->nullable();
            $table->string('api_key')->nullable();

            $table->unsignedSmallInteger('rights')->default(0);

            $table->timestamps();

            $table->index(['gitlab_id'], 'u_gi_index');
            $table->index(['gitlab_name'], 'u_gn_index');
            $table->index(['uid'], 'u_uid_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

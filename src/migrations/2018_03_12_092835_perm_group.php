<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PermGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('rest_access', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->unique();
            $table->enum('type', ['permission', 'group']);
            $table->string('description', 128);
            $table->timestamps();

        });

        Schema::create('rest_group_permission', function (Blueprint $table) {
            $table->unsignedInteger('group');
            $table->unsignedInteger('permission');
            $table->timestamps();
            $table->primary(['group', 'permission']);
        });

        Schema::create('rest_access_user', function (Blueprint $table) {
            $table->unsignedInteger('access_name');
            $table->string('user_outer', 64);
            $table->timestamps();
            $table->primary(['access_name', 'user_outer']);
        });


        Schema::table('rest_group_permission', function (Blueprint $table) {
            $table->foreign('group')->references('id')->on('rest_access')->onDelete('cascade');
            $table->foreign('permission')->references('id')->on('rest_access')->onDelete('cascade');
        });

        Schema::table('rest_access_user', function (Blueprint $table) {
            $table->foreign('access_name')->references('id')->on('rest_access')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rest_access_user', function (Blueprint $table) {
            $table->dropForeign(['access_name']);
        });

        Schema::table('rest_group_permission', function (Blueprint $table) {
            $table->dropForeign(['group']);
            $table->dropForeign(['permission']);
        });

        Schema::dropIfExists('rest_access');
        Schema::dropIfExists('rest_group_permission');
        Schema::dropIfExists('rest_access_user');
    }
}

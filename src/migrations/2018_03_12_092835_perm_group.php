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
            $table->string('name', 64)->unique();
            $table->enum('type', ['permission', 'group']);
            $table->string('description', 128);
            $table->timestamps();
            $table->primary('name');
        });

        Schema::create('rest_group_permission', function (Blueprint $table) {
            $table->string('group', 64);
            $table->string('permission', 64);
            $table->timestamps();
            $table->primary(['group', 'permission']);
        });

        Schema::create('rest_access_user', function (Blueprint $table) {
            $table->string('access_name', 64);
            $table->string('user_outer', 64);
            $table->timestamps();
            $table->primary(['access_name', 'user_outer']);
        });


        Schema::table('rest_group_permission', function (Blueprint $table) {
            $table->foreign('group')->references('name')->on('rest_access')->onDelete('cascade');
            $table->foreign('permission')->references('name')->on('rest_access')->onDelete('cascade');
        });

        Schema::table('rest_access_user', function (Blueprint $table) {
            $table->foreign('access_name')->references('name')->on('rest_access')->onDelete('cascade');
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
            $table->dropForeign(['access']);
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

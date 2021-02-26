<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      if (!Schema::hasTable('users')){
        Schema::create('users', function (Blueprint $table) {
          $table->id();
          $table->uuid('uuid')->nullable();
          $table->string('email')->unique();
          $table->timestamp('email_verified_at')->nullable();
          $table->string('password');
          $table->string('first_name')->nullable();
          $table->string('last_name')->nullable();
          $table->enum('gender',['male','female'])->nullable();
          $table->string('phone_number')->nullable();
          $table->rememberToken();
          $table->timestamps();
          $table->softDeletes();
        });
      }
      Schema::table('users',function (Blueprint $table){
        if (Schema::hasColumns('users',['name'])){
          $table->dropColumn('name');
        }
        $table->uuid('uuid')->nullable();
        $table->string('first_name')->nullable();
        $table->string('last_name')->nullable();
        $table->enum('gender',['male','female'])->nullable();
        $table->string('phone_number')->nullable();
        $table->softDeletes();
      });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

      Schema::table('users',function (Blueprint $table){
      if (!Schema::hasColumns('users',['name'])){
          $table->string('name');
        }
        $table->dropColumn('uuid');
        $table->dropColumn('deleted_at');
        $table->dropColumn('first_name');
        $table->dropColumn('last_name');
        $table->dropColumn('gender',['male','female']);
        $table->dropColumn('phone_number');
      });
    }
}
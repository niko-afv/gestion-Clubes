<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->increments('id');
            $table->string('dni',50)->nullable();
            $table->string('name',255);
            $table->string('email',255)->nullable();
            $table->string('phone',20)->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('degree_id')->nullable();
            $table->integer('institutable_id')->unsigned();
            $table->string('institutable_type')->default('App\Club');
            $table->integer('unit_id')->unsigned()->nullable();
            $table->integer('active')->default(0);
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
        Schema::dropIfExists('members');
    }
}

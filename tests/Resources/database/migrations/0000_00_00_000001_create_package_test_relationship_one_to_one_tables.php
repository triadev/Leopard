<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreatePackageTestRelationshipOneToOneTables extends \Illuminate\Database\Migrations\Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
    
        Schema::create('relationship_one_to_one', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('test_id')->unsigned();
            $table->foreign('test_id')->references('id')->on('test')->onDelete('cascade');
            
            $table->string('title');
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
        Schema::drop('relationship_one_to_one');
    }
}

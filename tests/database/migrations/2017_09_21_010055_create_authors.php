<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthors extends Migration
{
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->softDeletes();

            $table->string('email');
            $table->boolean("is_famous")->default(false);
            $table->string('name');
            $table->json("finances")->nullable();
        });
    }
}

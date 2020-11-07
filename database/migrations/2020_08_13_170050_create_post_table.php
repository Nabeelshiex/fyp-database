<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title', 80);
            $table->text('description');
            $table->string('image', 20)->nullable();
            $table->string('category', 40);
            $table->boolean('isActive')->default(true);
            $table->boolean('isPaid')->default(false);
            $table->integer('soldTo')->nullable();
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
        Schema::dropIfExists('post');
    }
}

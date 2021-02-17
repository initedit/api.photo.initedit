<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_metas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id');
            $table->string('name',1024);
            $table->string('description',4048)
            ->nullable();
            $table->string('tags',512)
                    ->nullable();
            $table->string('path',512);
            $table->string('extra',4048)
                  ->nullable();
            $table->integer('width');
            $table->integer('height');
            $table->integer('size');
            $table->integer('status');
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
        Schema::dropIfExists('post_metas');
    }
}

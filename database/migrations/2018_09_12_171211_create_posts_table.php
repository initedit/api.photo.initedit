<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',256)
                //   ->unique()
                  ->comment("URI for photo");
            $table->string('password',2048)
                  ->comment("Encrypted token provided by user")
                  ->nullable();
            $table->integer('type')
                  ->comment("1->Public, 2->Protected, 3->Private");
            $table->integer('status')
                  ->comment("0->Enabled, 1->Disabled");
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
        Schema::dropIfExists('posts');
    }
}

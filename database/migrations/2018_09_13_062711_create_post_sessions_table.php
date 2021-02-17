<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer("post_id")
                  ->comment("Reference to Post Table id");
            $table->string("token",2048)
                  ->comment("Unique Token for create session");
            $table->integer("valid_till")
                  ->comment("Session till its valid");
            $table->integer("status")
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
        Schema::dropIfExists('post_sessions');
    }
}

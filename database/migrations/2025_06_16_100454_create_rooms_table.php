<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('rooms', function (Blueprint $table) {
        $table->id();
        $table->foreignId('property_id')->constrained()->onDelete('cascade');
        $table->string('name'); // e.g., "Room A", "2nd Floor Room"
        $table->decimal('price', 10, 2);
        $table->integer('capacity'); // total number of people it can accommodate
        $table->integer('available_slots'); // current number of open slots
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
        Schema::dropIfExists('rooms');
    }
}

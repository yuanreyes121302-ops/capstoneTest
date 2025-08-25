<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('room_id')->constrained()->onDelete('cascade');
        $table->text('comment');
        $table->text('reply')->nullable();
        $table->unsignedTinyInteger('rating'); // 1 to 5
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
        Schema::dropIfExists('reviews');
    }
}

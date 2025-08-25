<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->foreignId('landlord_id')->constrained('users')->onDelete('cascade');

            $table->text('terms')->nullable(); // Simulated contract terms
            $table->enum('status', ['pending', 'accepted', 'declined', 'cancelled'])->default('pending');

            $table->boolean('signed_by_tenant')->default(false);
            $table->boolean('signed_by_landlord')->default(false);

            $table->timestamp('finalized_at')->nullable(); // when both parties agree
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
        Schema::dropIfExists('bookings');
    }
}

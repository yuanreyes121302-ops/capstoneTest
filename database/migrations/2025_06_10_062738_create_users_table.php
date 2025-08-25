<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->unique(); // like a student/school ID
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('gender')->nullable(); // male, female, other
            $table->date('dob')->nullable();
            $table->enum('role', ['admin', 'landlord', 'tenant']);
            $table->boolean('is_approved')->default(false);
            $table->string('profile_image')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}

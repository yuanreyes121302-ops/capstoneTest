<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractFieldsToBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('contract_status', ['active', 'completed', 'terminated', 'expired'])->default('active')->after('landlord_terms');
            $table->text('termination_reason')->nullable()->after('contract_status');
            $table->timestamp('terminated_at')->nullable()->after('termination_reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['contract_status', 'termination_reason', 'terminated_at']);
        });
    }
}

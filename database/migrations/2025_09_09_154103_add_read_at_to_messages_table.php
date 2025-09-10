<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadAtToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (!Schema::hasColumn('messages', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('updated_at');
            }
            // Helpful indexes
            $table->index(['receiver_id', 'read_at'], 'messages_receiver_read_idx');
            $table->index(['sender_id', 'receiver_id'], 'messages_sender_receiver_idx');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            if (Schema::hasColumn('messages', 'read_at')) {
                $table->dropIndex('messages_receiver_read_idx');
                $table->dropIndex('messages_sender_receiver_idx');
                $table->dropColumn('read_at');
            }
        });
    }
}

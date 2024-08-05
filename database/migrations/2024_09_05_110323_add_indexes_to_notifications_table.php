<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['model_id', "model_type"], "n_model_id_type_index");
            $table->index('user_id', "notifications_user_id_index");
        });

        Schema::table('notification_user', function (Blueprint $table) {
            $table->index('n_u_notification_id_index');
            $table->index('user_id', "n_u_user_id_index");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex("n_model_id_type_index");
            $table->dropIndex('notifications_user_id_index');
        });

        Schema::table('notification_user', function (Blueprint $table) {
            $table->dropIndex('n_u_notification_id_index');
            $table->dropIndex('n_u_user_id_index');
        });
    }
};

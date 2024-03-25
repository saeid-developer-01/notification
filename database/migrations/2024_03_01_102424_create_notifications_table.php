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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("content");
            $table->string("condition")->nullable();
            $table->text("image_url")->nullable();
            $table->json("data");
            $table->unsignedBigInteger("model_id")->nullable();
            $table->string("model_type")->nullable();
            $table->unsignedBigInteger("user_id");
            $table->string("timezone")->nullable();
            $table->timestamp("send_date")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

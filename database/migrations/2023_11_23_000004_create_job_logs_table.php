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
        Schema::create('job_logs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('provider_id')->unsigned();

            $table->tinyInteger('status'); //1 : success , 0 : failed
            $table->text('request')->nullable();
            $table->text('response')->nullable();
            $table->text('error')->nullable();

            $table->foreign('provider_id')->references('id')->on('news_providers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropForeign(['provider_id']);
        Schema::dropIfExists('job_logs');
    }
};

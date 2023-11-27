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
        Schema::create('news_providers', function (Blueprint $table)
        {
            $table->bigIncrements('id');

            $table->string('name');
            $table->text('token');
            $table->string('has_published_at');

            $table->string('auth_url')->nullable(); //for token refresh
            $table->string('user_name')->nullable(); //for token refresh
            $table->string('password')->nullable(); //for token refresh

            $table->timestamps();
            $table->softDeletes();

        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_providers');
    }
};

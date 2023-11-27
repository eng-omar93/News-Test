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
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('provider_id')->unsigned();
            $table->bigInteger('category_id')->unsigned()->default(1);
            $table->bigInteger('source_id')->unsigned();
            $table->bigInteger('author_id')->unsigned()->default(1);

            $table->text('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->text('url')->nullable();
            $table->text('image')->nullable();
            $table->timestamp('published_at');

            $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            $table->foreign('source_id')->references('id')->on('sources')->cascadeOnDelete();
            $table->foreign('author_id')->references('id')->on('authors')->cascadeOnDelete();
            $table->foreign('provider_id')->references('id')->on('news_providers')->cascadeOnDelete();
            $table->index(['published_at']);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $table->dropIndex(['published_at']);
        $table->dropForeign(['category_id']);
        $table->dropForeign(['source_id']);
        $table->dropForeign(['author_id']);
        $table->dropForeign(['provider_id']);

        Schema::dropIfExists('news');
    }
};

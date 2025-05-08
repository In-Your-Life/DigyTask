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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->boolean('is_page')->default(false);
            $table->string('slug')->unique();
            $table->string('status');
            $table->string('priority');
            $table->dateTime('deadline')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->boolean('is_template')->default(false);
            $table->string('figma_url')->nullable();
            $table->string('notion_url')->nullable();
            $table->string('github_url')->nullable();
            $table->string('slack_channel')->nullable();
            $table->string('webhook_url')->nullable();
            $table->timestamps();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

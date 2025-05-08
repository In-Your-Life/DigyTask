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
        Schema::table('shared_pages', function (Blueprint $table) {
            $table->longText('editable_html_content')->nullable()->after('html_content');
            $table->unsignedBigInteger('edited_by')->nullable()->after('editable_html_content');
            $table->foreign('edited_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shared_pages', function (Blueprint $table) {
            $table->dropForeign(['edited_by']);
            $table->dropColumn(['editable_html_content', 'edited_by']);
        });
    }
};

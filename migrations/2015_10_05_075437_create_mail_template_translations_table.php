<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMailTemplateTranslationsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mail_template_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('mail_template_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->string('title');
            $table->text('description');

            $table->foreign('mail_template_id')->references('id')->on('mail_templates')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->unique(['mail_template_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::drop('mail_template_translations');
    }
}
